<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Singleton;

/**
 * i18n Utility
 *
 * @package MecabSweet\Utility
 */
final class i18n extends Singleton
{
	/**
	 * Domain
	 *
	 * @var string
	 */
	private $domain = '';

	/**
	 * Constructor
	 *
	 * @param array $settings
	 *
	 * @throws \Exception
	 */
	protected function __construct( array $settings = array() ) {
		if( !isset($settings['domain']) ){
			throw new \Exception('Domain is not set.');
		}
		$this->domain = (string)$settings['domain'];
	}

	/**
	 * GetText shorthand
	 *
	 * @param string $string
	 * @return string
	 */
	public function __($string){
		return __($string, $this->domain);
	}

	/**
	 * GetText shorthand
	 *
	 * @param string $string
	 */
	public function _e($string){
		_e($string, $this->domain);
	}

	/**
	 * Shorthand for sprintf and __
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function _sp($string){
		$args = func_get_args();
		if( count($args) > 1 ){
			$args[0] = $this->__($args[0]);
			return call_user_func_array('sprintf', $args);
		}else{
			return $this->__($string);
		}
	}

	/**
	 * Short hand for printf and __
	 *
	 * @param string $string
	 */
	public function _p($string){
		echo call_user_func_array(array($this, '_sp'), func_get_args());
	}

}
