<?php

namespace MeCabSweet\Constants;

/**
 * Represents Part of Speech
 *
 * @package MeCabSweet\Constants
 */
class PartOfSpeech
{

	const NOUN = '名詞';

	const ADVERB = '副詞';

	const PRENOMINAL_ADJECTIVE = '連体詞';

	const PARTICLE = '助詞';

	const INTERJECTION = '感動詞';

	const CONJUNCTION = '接続詞';

	const VERB = '動詞';

	const ADJECTIVE = '形容詞';

	const ADJECTIVAL_VERB = '形容動詞';

	const AUXILIARY_VERB = '助動詞';

	/**
	 * Get all constants
	 *
	 * @return array
	 */
	public static function get_all(){
		$reflection = new \ReflectionClass(__CLASS__);
		return $reflection->getConstants();
	}

	/**
	 * Get conjugatives
	 *
	 * @return array
	 */
	public static function get_conjugatives(){
		$conjugatives = array();
		foreach( self::get_all() as $key => $val ){
			if( self::is_conjugative($val) ){
				$conjugatives[$key] = $val;
			}
		}
		return $conjugatives;
	}

	/**
	 * Get non-conjugatives
	 *
	 * @return array
	 */
	public static function get_non_conjugatives(){
		$non_conjugatives = array();
		foreach( self::get_all() as $key => $val ){
			if( !self::is_conjugative($val) ){
				$non_conjugatives[$key] = $val;
			}
		}
		return $non_conjugatives;
	}

	/**
	 * Detect if passed string is conjugative.
	 *
	 * @param string $part_of_speech
	 *
	 * @return bool
	 */
	public static function is_conjugative($part_of_speech){
		return false !== array_search($part_of_speech, array(
			self::VERB,
			self::ADJECTIVAL_VERB,
			self::ADJECTIVE,
			self::AUXILIARY_VERB,
		));
	}

} 