<?php

namespace MeCabSweet\Pattern;


/**
 * Class RowWrapper
 *
 * @package MeCabSweet\Pattern
 */
class RowWrapper
{

	/**
	 * @var null|\stdClass
	 */
	private $row = null;


	/**
	 * Constructor
	 *
	 * @param \stdClass $row
	 */
	public function __construct($row){
		$this->row = $row;
	}

	/**
	 * Convert row object to specific class
	 *
	 * @param null|\stdClass|array $row
	 *
	 * @return array|null|self
	 */
	final public static function convert( $row ){
		$class_name = get_called_class();
		if( is_a($row, 'stdClass') ){
			return new $class_name($row);
		}elseif( is_array($row) ){
			$new_result = array();
			foreach($row as $r){
				$new_result[] = new $class_name($r);
			}
			return $new_result;
		}elseif( is_null($row) ){
			return null;
		}else{
			return $row;
		}
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get($name){
		if( isset($this->row->{$name}) ){
			return $this->row->{$name};
		}else{
			return null;
		}
	}

} 