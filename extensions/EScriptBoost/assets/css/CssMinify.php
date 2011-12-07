<?php
/**
 * Class CssMinify  
 * @package scriptboost
 */

/**
 * Minify CSS
 *
 * This class uses Css_Compressor and CssUriRewriter to 
 * minify CSS and rewrite relative URIs.
 * 
 * @author Stephen Clay <steve@mrclay.org>
 * @author http://code.google.com/u/1stvamp/ (Issue 64 patch)
 */
class CssMinify {
    
    /**
     * Minify a CSS string
     * 
     * @param string $css
     * 
     * @param array $options available options:
     * 
     * 'preserveComments': (default true) multi-line comments that begin
     * with "/*!" will be preserved with newlines before and after to
     * enhance readability.
     *
     * 'removeCharsets': (default true) remove all @charset at-rules
     * 
     * 'prependRelativePath': (default null) if given, this string will be
     * prepended to all relative URIs in import/url declarations
     * 
     * 'currentDir': (default null) if given, this is assumed to be the
     * directory of the current CSS file. Using this, minify will rewrite
     * all relative URIs in import/url declarations to correctly point to
     * the desired files. For this to work, the files *must* exist and be
     * visible by the PHP process.
     *
     * 'symlinks': (default = array()) If the CSS file is stored in 
     * a symlink-ed directory, provide an array of link paths to
     * target paths, where the link paths are within the document root. Because 
     * paths need to be normalized for this to work, use "//" to substitute 
     * the doc root in the link paths (the array keys). E.g.:
     * <code>
     * array('//symlink' => '/real/target/path') // unix
     * array('//static' => 'D:\\staticStorage')  // Windows
     * </code>
     *
     * 'docRoot': (default = $_SERVER['DOCUMENT_ROOT'])
     * see CssUriRewriter::rewrite
     * 
     * @return string
     */
    public static function minify($css, $options = array()) 
    {
        $options = array_merge(array(
            'removeCharsets' => true,
            'preserveComments' => true,
            'currentDir' => null,
            'docRoot' => $_SERVER['DOCUMENT_ROOT'],
            'prependRelativePath' => null,
            'symlinks' => array(),
        ), $options);
        
        if ($options['removeCharsets']) {
            $css = preg_replace('/@charset[^;]+;\\s*/', '', $css);
        }
        if (! $options['preserveComments']) {
            $css = CssCompressor::process($css, $options);
        } else {
            $css = CommentPreserver::process(
                $css
                ,array('CssCompressor', 'process')
                ,array($options)
            );
        }
        if (! $options['currentDir'] && ! $options['prependRelativePath']) {
            return $css;
        }
        if ($options['currentDir']) {
            return CssUriRewriter::rewrite(
                $css
                ,$options['currentDir']
                ,$options['docRoot']
                ,$options['symlinks']
            );  
        } else {
            return CssUriRewriter::prepend(
                $css
                ,$options['prependRelativePath']
            );
        }
    }
}

/**
 * Class CommentPreserver 
 * @package Minify
 */

/**
 * Process a string in pieces preserving C-style comments that begin with "/*!"
 * 
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class CommentPreserver {
    
    /**
     * String to be prepended to each preserved comment
     *
     * @var string
     */
    public static $prepend = "\n";
    
    /**
     * String to be appended to each preserved comment
     *
     * @var string
     */
    public static $append = "\n";
    
    /**
     * Process a string outside of C-style comments that begin with "/*!"
     *
     * On each non-empty string outside these comments, the given processor 
     * function will be called. The comments will be surrounded by 
     * Minify_CommentPreserver::$preprend and Minify_CommentPreserver::$append.
     * 
     * @param string $content
     * @param callback $processor function
     * @param array $args array of extra arguments to pass to the processor 
     * function (default = array())
     * @return string
     */
    public static function process($content, $processor, $args = array())
    {
        $ret = '';
        while (true) {
            list($beforeComment, $comment, $afterComment) = self::_nextComment($content);
            if ('' !== $beforeComment) {
                $callArgs = $args;
                array_unshift($callArgs, $beforeComment);
                $ret .= call_user_func_array($processor, $callArgs);    
            }
            if (false === $comment) {
                break;
            }
            $ret .= $comment;
            $content = $afterComment;
        }
        return $ret;
    }
    
    /**
     * Extract comments that YUI Compressor preserves.
     * 
     * @param string $in input
     * 
     * @return array 3 elements are returned. If a YUI comment is found, the
     * 2nd element is the comment and the 1st and 3rd are the surrounding
     * strings. If no comment is found, the entire string is returned as the 
     * 1st element and the other two are false.
     */
    private static function _nextComment($in)
    {
        if (
            false === ($start = strpos($in, '/*!'))
            || false === ($end = strpos($in, '*/', $start + 3))
        ) {
            return array($in, false, false);
        }
        $ret = array(
            substr($in, 0, $start)
            ,self::$prepend . '/*!' . substr($in, $start + 3, $end - $start - 1) . self::$append
        );
        $endChars = (strlen($in) - $end - 2);
        $ret[] = (0 === $endChars)
            ? ''
            : substr($in, -$endChars);
        return $ret;
    }
}
