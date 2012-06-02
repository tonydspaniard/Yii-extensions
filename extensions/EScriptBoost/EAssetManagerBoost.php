<?php
/**
 * EAssetManagerBoost class
 * 
 * Extended version of CAssetManager to compress published assets
 * 
 * @author Antonio Ramirez Cobos
 * @link www.ramirezcobos.com
 *
 * 
 * @copyright 
 * 
 * Copyright (c) 2010 Antonio Ramirez Cobos
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial 
 * portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE 
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
 */
class EAssetManagerBoost extends CAssetManager {

	/**
	 * @var mixed $minifiedExtensionFlags specify the extension names that
	 * the extension will check against in order to not compress/minify the 
	 * files. This flags are ignored if $forceCompress is true
	 */
	public $minifiedExtensionFlags = array('packed.js', 'min.js');
	/**
	 * @var boolean $forceCompress if true, all files will be processed to 
	 * be minified
	 */
	public $forceCompress = false;
	/**
	 * @var array published assets
	 */
	private $_published = array();

	/**
	 * Modified version of CAssetManager::publish method
	 * -------------------------------------------------------
	 * Publishes a file or a directory.
	 * This method will copy the specified asset to a web accessible directory
	 * and return the URL for accessing the published asset.
	 * <ul>
	 * <li>If the asset is a file, its file modification time will be checked
	 * to avoid unnecessary file copying;</li>
	 * <li>If the asset is a directory, all files and subdirectories under it will
	 * be published recursively. Note, in this case the method only checks the
	 * existence of the target directory to avoid repetitive copying.</li>
	 * </ul>
	 *
	 * Note: On rare scenario, a race condition can develop that will lead to a
	 * one-time-manifestation of a non-critical problem in the creation of the directory
	 * that holds the published assets. This problem can be avoided altogether by 'requesting'
	 * in advance all the resources that are supposed to trigger a 'publish()' call, and doing
	 * that in the application deployment phase, before system goes live. See more in the following
	 * discussion: http://code.google.com/p/yii/issues/detail?id=2579
	 *
	 * @param string $path the asset (file or directory) to be published
	 * @param boolean $hashByName whether the published directory should be named as the hashed basename.
	 * If false, the name will be the hashed dirname of the path being published.
	 * Defaults to false. Set true if the path being published is shared among
	 * different extensions.
	 * @param integer $level level of recursive copying when the asset is a directory.
	 * Level -1 means publishing all subdirectories and files;
	 * Level 0 means publishing only the files DIRECTLY under the directory;
	 * level N means copying those directories that are within N levels.
	 * @param boolean $forceCopy whether we should copy the asset file or directory even if it is already published before.
	 * This parameter is set true mainly during development stage when the original
	 * assets are being constantly changed. The consequence is that the performance
	 * is degraded, which is not a concern during development, however.
	 * This parameter has been available since version 1.1.2.
	 * @return string an absolute URL to the published asset
	 * @throws CException if the asset to be published does not exist.
	 */
	public function publish($path, $hashByName=false, $level=-1, $forceCopy=false)
	{
		if (isset($this->_published[$path]))
			return $this->_published[$path];
		else if (($src = realpath($path)) !== false)
		{
			if (is_file($src))
			{
				$dir = $this->hash($hashByName ? basename($src) : dirname($src));
				$fileName = basename($src);
				$dstDir = $this->getBasePath() . DIRECTORY_SEPARATOR . $dir;
				$dstFile = $dstDir . DIRECTORY_SEPARATOR . $fileName;

				if ($this->linkAssets)
				{
					if (!is_file($dstFile))
					{
						if (!is_dir($dstDir))
						{
							mkdir($dstDir);
							@chmod($dstDir, $this->newDirMode);
						}
						symlink($src, $dstFile);
					}
				} 
				else if (@filemtime($dstFile) < @filemtime($src) || $forceCopy)
				{
					if (!is_dir($dstDir))
					{
						mkdir($dstDir);
						@chmod($dstDir, $this->newDirMode);
					}
					$this->copyFile($src, $dstFile);
					@chmod($dstFile, $this->newFileMode);
				}

				return $this->_published[$path] = $this->getBaseUrl() . "/$dir/$fileName";
			} 
			else if (is_dir($src))
			{
				$dir = $this->hash($hashByName ? basename($src) : $src);
				$dstDir = $this->getBasePath() . DIRECTORY_SEPARATOR . $dir;

				if ($this->linkAssets)
				{
					if (!is_dir($dstDir))
						symlink($src, $dstDir);
				}
				else if (!is_dir($dstDir) || $forceCopy)
				{
					$fileTypes = array();
					$options = array(
						'newDirMode' => $this->newDirMode,
						'newFileMode' => $this->newFileMode);

					$this->copyDirectoryRecursive($src, $dstDir, '', array(), $this->excludeFiles, $level, $options);
				}

				return $this->_published[$path] = $this->getBaseUrl() . '/' . $dir;
			}
		}
		throw new CException(Yii::t('yii', 'The asset "{asset}" to be published does not exist.', array('{asset}' => $path)));
	}
	/**
	 * Custom copy file to compress/copy|copy files
	 * @param string $src the full path of the file to read from
	 * @param string $dstFile the full path of the file to write to
	 */
	public function copyFile($src, $dstFile)
	{
		// assumed config includes the required path aliases to use
		// EScriptBoost
		$ext = strtoupper(substr(strrchr($src, '.'), 1));

		if (($ext == 'JS' && !$this->strpos_arr($dstFile, $this->minifiedExtensionFlags)) || $this->forceCompress )
		{

			Yii::trace('copyFile JS Compressing: ' . $src, 'EAssetManagerBoost');

			@file_put_contents(
					$dstFile, EScriptBoost::minifyJs(@file_get_contents($src), EScriptBoost::JS_MIN_PLUS));
			@touch($dstFile, @filemtime($src));
		} 
		else if (($ext == 'CSS' && !$this->strpos_arr($dstFile, $this->minifiedExtensionFlags)) || $this->forceCompress )
		{

			Yii::trace('copyFile CSS Compressing: ' . $src, 'EAssetManagerBoost');

			@file_put_contents(
					$dstFile, EScriptBoost::minifyCss(@file_get_contents($src)));
			@touch($dstFile, @filemtime($src));
		} 
		else
		{
			copy($src, $dstFile);
		}

	}

	/**
	 * Modified version of CFileHelper::copyDirectoryRecursive
	 * -------------------------------------------------------
	 * Copies a directory.
	 * This method is mainly used by {@link copyDirectory}.
	 * @param string $src the source directory
	 * @param string $dst the destination directory
	 * @param string $base the path relative to the original source directory
	 * @param array $fileTypes list of file name suffix (without dot). Only files with these suffixes will be copied.
	 * @param array $exclude list of directory and file exclusions. Each exclusion can be either a name or a path.
	 * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
	 * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
	 * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
	 * @param integer $level recursion depth. It defaults to -1.
	 * Level -1 means copying all directories and files under the directory;
	 * Level 0 means copying only the files DIRECTLY under the directory;
	 * level N means copying those directories that are within N levels.
	 * @param array $options additional options. The following options are supported:
	 * newDirMode - the permission to be set for newly copied directories (defaults to 0777);
	 * newFileMode - the permission to be set for newly copied files (defaults to the current environment setting).
	 */
	protected function copyDirectoryRecursive($src, $dst, $base, $fileTypes, $exclude, $level, $options)
	{
		if (!is_dir($dst))
			mkdir($dst);
		if (isset($options['newDirMode']))
			@chmod($dst, $options['newDirMode']);
		else
			@chmod($dst, 0777);
		$folder = opendir($src);
		while (($file = readdir($folder)) !== false)
		{
			if ($file === '.' || $file === '..')
				continue;
			$path = $src . DIRECTORY_SEPARATOR . $file;
			$isFile = is_file($path);
			if ($this->validatePath($base, $file, $isFile, $fileTypes, $exclude))
			{
				if ($isFile)
				{
					$this->copyFile($path, $dst . DIRECTORY_SEPARATOR . $file);
					if (isset($options['newFileMode']))
						@chmod($dst . DIRECTORY_SEPARATOR . $file, $options['newFileMode']);
				}
				else if ($level)
					$this->copyDirectoryRecursive($path, $dst . DIRECTORY_SEPARATOR . $file, $base . '/' . $file, $fileTypes, $exclude, $level - 1, $options);
			}
		}
		closedir($folder);
	}

	/**
	 * Forced included from CFileHelper::validate in order to
	 * use copyDirectoryRecursive -why not static?
	 * -------------------------------------------------------
	 * Validates a file or directory.
	 * @param string $base the path relative to the original source directory
	 * @param string $file the file or directory name
	 * @param boolean $isFile whether this is a file
	 * @param array $fileTypes list of file name suffix (without dot). Only files with these suffixes will be copied.
	 * @param array $exclude list of directory and file exclusions. Each exclusion can be either a name or a path.
	 * If a file or directory name or path matches the exclusion, it will not be copied. For example, an exclusion of
	 * '.svn' will exclude all files and directories whose name is '.svn'. And an exclusion of '/a/b' will exclude
	 * file or directory '$src/a/b'. Note, that '/' should be used as separator regardless of the value of the DIRECTORY_SEPARATOR constant.
	 * @return boolean whether the file or directory is valid
	 */
	protected function validatePath($base, $file, $isFile, $fileTypes, $exclude)
	{
		foreach ($exclude as $e)
		{
			if ($file === $e || strpos($base . '/' . $file, $e) === 0)
				return false;
		}
		if (!$isFile || empty($fileTypes))
			return true;
		if (($type = pathinfo($file, PATHINFO_EXTENSION)) !== '')
			return in_array($type, $fileTypes);
		else
			return false;
	}
	/**
	 * Custom strpos to support arrays as needles
	 * @param string $haystack the string to check against
	 * @param mixed $needle the array|string to check against
	 * @return boolean
	 */
	protected function strpos_arr($haystack, $needle) {
		if (!is_array($needle))
			return strpos($haystack, $needle);
		
		foreach ($needle as $what)
		{
			if (($pos = strpos($haystack, $what)) !== false)
				return $pos;
		}
		return false;
	}

}
