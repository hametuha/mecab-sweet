<?php

namespace MeCabSweet\Pattern;

/**
 * Singleton Pattern
 */
abstract class Singleton
{

	/**
	 * Instance holder
	 *
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct(array $settings = array()){
		// Override this function if required
	}

	/**
	 * Instance
	 *
	 * @param array $settings
	 *
	 * @return mixed
	 */
	final public static function get_instance(array $settings = array()){
		$class_name = get_called_class();
		if( !isset(self::$instances[$class_name]) ){
			self::$instances[$class_name] = new $class_name($settings);
		}
		return self::$instances[$class_name];
	}
}
