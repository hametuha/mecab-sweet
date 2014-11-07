<?php

namespace MeCabSweet\Data;


use MeCabSweet\Expression\Morpheme;
use MeCabSweet\Pattern\Model;

/**
 * Term Table
 *
 * @package MeCabSweet\Data
 */
class Terms extends Model
{

	/**
	 * @var string
	 */
	public $table_version = '1.0';

	/**
	 * @var string
	 */
	protected $table_name = 'mecab_terms';


	/**
	 * @var string
	 */
	protected $key = 'mecab_terms_table_version';

	/**
	 * @var bool
	 */
	protected $only_on_main_blog = true;

	/**
	 * Get Term object
	 *
	 * @param int $term_id
	 *
	 * @return Morpheme|null
	 */
	public function get_term($term_id){
		$query = <<<SQL
			SELECT * FROM {$this->table} WHERE term_id = %d
SQL;
		return Morpheme::convert($this->db->get_row($query, $term_id));
	}

	/**
	 * Insert term
	 *
	 * @param array $data
	 *
	 * @return false|int
	 */
	public function insert( $data = array() ){
		$data = wp_parse_args($data, array(
			'morpheme' => '',
			'base_id' => 0,
			'ruby' => '',
			'pronunciation' => '',
			'cost' => 0,
			'part_of_speech' => '',
			'option_1' => '',
			'option_2' => '',
			'option_3' => '',
		));
		return $this->db->insert($this->table, $data, array(
			'%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
		));
	}

	/**
	 * Creation SQL
	 *
	 * @return string
	 */
	protected function create_sql() {
		$engine = $this->mysql_is_new() ? 'InnoDb' : 'MyISAM';
		$query = <<<SQL
			CREATE TABLE {$this->table} (
				term_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				base_id BIGINT NOT NULL,
				morpheme TEXT NOT NULL,
				ruby TEXT NOT NULL,
				pronunciation TEXT NOT NULL,
				cost BIGINT NOT NULL,
				part_of_speech VARCHAR(48) NOT NULL DEFAULT "名詞",
				option_1 VARCHAR(48) NOT NULL DEFAULT "一般",
				option_2 VARCHAR(48) NOT NULL DEFAULT "*",
				option_3 VARCHAR(48) NOT NULL DEFAULT "*",
				conjugation VARCHAR(256) NOT NULL DEFAULT "*",
				conjugation_type VARCHAR(256) NOT NULL DEFAULT "*",
				left_context_id VARCHAR(256) NOT NULL,
				right_context_id VARCHAR(256) NOT NULL,
				updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FULLTEXT term_index (morphem, ruby)
			) ENGINE = {$engine} DEFAULT CHARSET = utf8  COLLATE utf8_unicode_ci
SQL;
		return $query;
	}


} 