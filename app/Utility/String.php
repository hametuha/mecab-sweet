<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Singleton;

/**
 * String helper
 *
 * @package MeCabSweet\Utility
 * @property-read \MeCab_Tagger $tagger
 */
class String extends Singleton
{

	/**
	 * @var \MeCab_Tagger
	 */
	private $mecab_tagger = null;

	/**
	 * Convert string to mecab-ready
	 *
	 * @param string $string
	 * @return string
	 */
	public function normalize($string){
		// Remove rt tag
		$string = preg_replace('/<rt>.*?<\/rt>/', '', $string);
		// Remove rp tag
		$string = preg_replace('/<rp>.*?<\/rp>/', '', $string);
		// Remove tags
		$string = strip_tags($string);
		// Remove short code
		$string = strip_shortcodes($string);
		// Remove sequential line break
		$string = preg_replace('/\n{2,}/', "\n", $string);
		// Convert line break to space
		$string = str_replace("\n", " ", $string);
		// Compact sequential spaces
		$string = preg_replace('/\s{2,}/', ' ', $string);
		// This is normalized text
		return $string;
	}

	/**
	 * Split string
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public function split($string){
		$string = $this->normalize($string);
		if( $this->ready() ){
			return mecab_split($string);
		}else{
			return preg_split('/[\s。、！？\.,]/u', $string);
		}
	}

	public function get_node($string){
		if( !$this->ready()  ){
			return array();
		}

	}

	/**
	 * Detect if MeCab function exists
	 *
	 * @return bool
	 */
	private function ready(){
		return function_exists('mecab_split');
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get($name){
		switch( $name ){
			case 'tagger':
				if( is_null($this->mecab_tagger) && $this->ready() ){
					$this->mecab_tagger = new \MeCab_Tagger();
				}
				return $this->mecab_tagger;
				break;
			default:
				return null;
				break;
		}
	}
} 