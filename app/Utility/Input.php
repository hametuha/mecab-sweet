<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Singleton;

/**
 * Input utility
 *
 * @package MecabSweet\Utility
 */
final class Input extends Singleton
{


	/**
	 * GET $_GET
	 *
	 * @param string $key
	 *
	 * @return null
	 */
	public function get($key){
		return isset($_GET[$key]) ? $_GET[$key] : null;
	}

	/**
	 * Get $_POST
	 *
	 * @param string $key
	 *
	 * @return null
	 */
	public function post($key){
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}


	/**
	 * Get $_REQUEST
	 *
	 * @param string $key
	 *
	 * @return null
	 */
	public function request($key){
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
	}

	/**
	 * Check nonce
	 *
	 * @param string $action
	 * @param string $key
	 *
	 * @return bool
	 */
	public function verify_nonce($action, $key = '_wpnonce'){
		return wp_verify_nonce($this->request($key), $action);
	}

}
