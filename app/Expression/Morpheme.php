<?php

namespace MeCabSweet\Expression;


use MeCabSweet\Pattern\RowWrapper;

/**
 * Morpheme expression
 *
 * @package MeCabSweet\Expression
 * @property-read int $term_id
 * @property-read string $morpheme
 * @property-read string $ruby
 * @property-read string $pronunciation
 * @property-read int $cost
 * @property-read string $part_of_speech
 * @property-read string $option_1
 * @property-read string $option_2
 * @property-read string $option_3
 * @property-read string $conjugation
 * @property-read string $conjugation_type
 * @property-read int $left_context_id
 * @property-read int $right_context_id
 * @property-read string $updated
 */
class Morpheme extends RowWrapper
{

	/**
	 * Convert object to CSV ready array
	 *
	 * @return array
	 */
	public function get_csv_row(){
		return array(
			$this->morpheme, // 表層系
			'0', //左文脈ID
			'0', //右文脈ID
			$this->cost, //コスト
			$this->part_of_speech, //品詞
			$this->option_1, //品詞細分類1,
			$this->option_2, //品詞細分類2
			$this->option_3, //品詞細分類3,
			$this->conjugation, //活用形,
			$this->conjugation_type, //活用型
			$this->morpheme, //原形,
			$this->ruby, //読み
			$this->pronunciation, //発音
		);
	}

}
