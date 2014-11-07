<?php

namespace MeCabSweet\Traits;


/**
 * Shortcut for MeCab_Tagger
 *
 * @package MeCabSweet\Traits
 */
Trait Tagger
{

	/**
	 * @var \MeCab_Tagger
	 */
	private static $mecab_tagger = null;

	/**
	 * Get MeCab Tagger
	 *
	 * @return \MeCab_Tagger
	 */
	public function tagger(){
		if( is_null(self::$mecab_tagger) ){
			self::$mecab_tagger = new \MeCab_Tagger();
		}
		return self::$mecab_tagger;
	}

}
