<?php

class EGMapBase {

	protected static $_counter = 0;
	protected $js_name;
	protected $options = array();

	/**
	 * 
	 * Sets value of a component property.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to set a property or attach setProperty method
	 * to create appropiate setting to specific properties
	 * <pre>
	 * $this->propertyName=$value;
	 * $this->setName($value)
	 * </pre>
	 * @param string $name the property name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			return $this->$setter($value);
		elseif (array_key_exists($name, $this->options))
		{
			return $this->options[$name] = $value;
		}
		if (method_exists($this, 'get' . $name))
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" is read only.', array('{class}' => get_class($this), '{property}' => $name)));
		else
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" is not defined.', array('{class}' => get_class($this), '{property}' => $name)));
	}

	/**
	 *
	 * Returns a property value, an event handler list or a behavior based on its name.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to read a property
	 * <pre>
	 * $value=$component->propertyName;
	 * $value=$component->getPropertyName;
	 * </pre>
	 * @param string $name
	 * @throws CException
	 */
	public function __get($name)
	{
		$getter = 'get' . ucfirst($name);

		if (method_exists($this, $getter))
			return $this->$getter();
		if (array_key_exists($name, $this->options))
		{
			return $this->options[$name];
		}

		throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" is not defined.', array('{class}' => get_class($this), '{property}' => $name)));
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * 
	 * @param string $name name of the property
	 */
	public function __isset($name)
	{
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter))
			return $this->$getter() !== null;

		return isset($this->options[$name]);
	}

	/**
	 * Sets a component property to be null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using unset() to set a component property to be null.
	 * @param string $name the property name or the event name
	 * @throws CException if the property is read only or not exists.
	 */
	public function __unset($name)
	{
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			return $this->$setter(null);
		else if (isset($this->option[$name]))
			return $this->option[$name] = null;
		else if (method_exists($this, 'get' . $name))
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" is read only.', array('{class}' => get_class($this), '{property}' => $name)));
		else
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" is not defined.', array('{class}' => get_class($this), '{property}' => $name)));
	}

	/**
	 * @return string Javascript name of the renderer service
	 * @author Antonio Ramirez
	 * @since 2011-01-24 
	 */
	public function getJsName($autoGenerate=true)
	{
		if ($this->js_name !== null)
			return $this->js_name;
		else if ($autoGenerate)
			return $this->js_name = get_class($this) . self::$_counter++;
	}

	/**
	 * 
	 * Sets the Javascript name of the renderer service
	 * @param string $name
	 */
	public function setJsName($name)
	{

		$this->js_name = $name;
	}

	/**
	 * @return array $options
	 * @author Antonio Ramirez
	 * @since 2011-01-25
	 */
	public function getOptions()
	{
		return $this->options;
	}

}

