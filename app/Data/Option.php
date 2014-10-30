<?php

namespace MeCabSweet\Data;

use MeCabSweet\Pattern\Singleton;
use MeCabSweet\Utility\Input;

/**
 * Option utility
 *
 * @package MecabSweet\Data
 * @property-read Input $input
 * @property-read array $option
 * @property-read bool $fulltext_search
 * @property-read bool $user_dic
 * @property-read string $user_dic_path
 * @property-read bool $taxonomy
 */
class Option extends Singleton
{

	/**
	 * @var string
	 */
	private $key = 'mecab_sweet_settings';

	/**
	 * Default option value
	 *
	 * @var array
	 */
	private $default_options = array(
		'fulltext_search' => false,
		'user_dic' => false,
		'user_dic_path' => '',
		'taxonomy' => false,
	);

	/**
	 * Update option
	 *
	 * @return bool
	 */
	public function update(){
		$option = $this->option;
		foreach( $option as $key => $value ){
			switch($key){
				case 'user_dic_path':
					$option[$key] = (string)$this->input->post($key);
					break;
				default:
					$option[$key] = (bool)$this->input->post($key);
					break;
			}
		}
		return update_option($this->key, $option);
	}

	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return mixed|null|void
	 */
	public function __get($key){
		switch($key){
			case 'option':
				$option = get_option($this->key, array());
				foreach( $this->default_options as $key => $value ){
					if( !isset($option[$key]) ){
						$option[$key] = $value;
					}
				}
				return $option;
				break;
			case 'input':
				return Input::get_instance();
				break;
			default:
				if( array_key_exists($key, $this->default_options) ){
					return $this->option[$key];
				}else{
					return null;
				}
				break;
		}
	}

}
