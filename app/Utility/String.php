<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Singleton;

class String extends Singleton
{


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
		if( function_exists('mecab_split') ){
			return mecab_split($string);
		}else{
			return preg_split('/[\s。、！？\.,]/u', $string);
		}
	}

} 