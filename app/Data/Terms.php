<?php

namespace MeCabSweet\Data;


use MeCabSweet\Constants\PartOfSpeech;
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
		return Morpheme::convert($this->db->get_row($this->db->prepare($query, $term_id)));
	}

	/**
	 * Get terms
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_terms( $args = array() ){
		$args = wp_parse_args($args, array(
			'page' => 1,
			'per_page' => 10,
			'orderby' => 'pronunciation',
			'order' => 'ASC',
			's' => '',
		));
		$per_page = intval($args['per_page']);
		$offset = $per_page * ( max(1, $args['page']) - 1 );

		switch( $args['orderby'] ){
			case 'pronunciation':
			case 'cost':
			case 'updated':
			case 'morpheme':
				$orderby = $args['orderby'];
				break;
			default:
				$orderby = 'ruby';
				break;
		}
		$order = 'DESC' == strtoupper($args['order']) ? 'DESC' : 'ASC';
		$wheres = array();
		if( $args['s'] ){
			$search = '%'.$args['s'].'%';
			$wheres[] = $this->db->prepare("(morpheme LIKE %s OR ruby LIKE %s OR pronunciation LIKE %s )", $search, $search, $search);
		}
		$where_clause = empty($wheres) ? '' : ' WHERE '.implode(' AND ', $wheres);
		$query = <<<SQL
			SELECT * FROM {$this->table}
			{$where_clause}
			ORDER BY {$orderby} {$order}
			LIMIT {$offset}, {$per_page}
SQL;
		$result = array();
		foreach( $this->db->get_results($query) as $row ){
			$result[] = Morpheme::convert($row);
		}
		return $result;
	}

	/**
	 * Insert term
	 *
	 * @param array $data
	 *
	 * @return true|\WP_Error
	 */
	public function insert( $data = array() ){
		$errors = $this->validate($data);
		if( is_wp_error($errors) ){
			return $errors;
		}
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
			'left_context_id' => '',
			'right_context_id' => '',
		));
		if( $this->db->insert($this->table, $data, array(
			'%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
		)) ){
			return true;
		}else{
			return new \WP_Error(500, $this->i18n->__('Sorry, but failed to save term.'));
		}
	}

	/**
	 * Update term
	 *
	 * @param int $term_id
	 * @param array $data
	 *
	 * @return bool|\WP_Error
	 */
	public function update($term_id, $data = array()){
		$errors = $this->validate($data);
		if( is_wp_error($errors) ){
			return $errors;
		}
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
		if( $this->db->update($this->table, $data, array( 'term_id' => $term_id ), array(
			'%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
		), array('%d')) ){
			return true;
		}else{
			return new \WP_Error(500, $this->i18n->__('Sorry, but failed to update term.'));
		}
	}

	/**
	 * Delete row
	 *
	 * @param int $term_id
	 *
	 * @return int Deleted rows
	 */
	public function delete($term_id){
		return (int) $this->db->delete($this->table, array('term_id' => $term_id), array('%d'));
	}

	/**
	 * Validation setting
	 *
	 * @return array
	 */
	protected function get_validation_config() {
		return array(
			'morpheme' => array(
				'label' => $this->i18n->__('Surface'),
				'required' => true,
			),
			'ruby' => array(
				'label' => $this->i18n->__('Ruby'),
				'required' => true,
				'format' => 'regexp',
				'regexp' => '/\A[ァ-ヶー]+\z/u',
			),
			'pronunciation' => array(
				'label' => $this->i18n->__('Pronunciation'),
				'required' => true,
				'format' => 'regexp',
				'regexp' => '/\A[ァ-ヶー]+\z/u',
			),
			'cost' => array(
				'label' => $this->i18n->__('Cost'),
				'required' => true,
				'format' => 'numeric',
				'more_than' => 0,
			),
			'part_of_speech' => array(
				'label' => $this->i18n->__('Part of Speech'),
				'required' => true,
				'format' => 'enum',
				'list' => PartOfSpeech::get_non_conjugatives(),
			),
		);
	}

	/**
	 * Registered word count
	 *
	 * @return int
	 */
	public function word_count(){
		return (int)$this->db->get_var("SELECT COUNT(term_id) FROM {$this->table}");
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
				part_of_speech VARCHAR(48) NOT NULL DEFAULT '',
				option_1 VARCHAR(48) NOT NULL DEFAULT '',
				option_2 VARCHAR(48) NOT NULL DEFAULT '',
				option_3 VARCHAR(48) NOT NULL DEFAULT '',
				conjugation VARCHAR(256) NOT NULL DEFAULT '',
				conjugation_type VARCHAR(256) NOT NULL DEFAULT '',
				left_context_id VARCHAR(256) NOT NULL,
				right_context_id VARCHAR(256) NOT NULL,
				updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FULLTEXT term_index  (morpheme, ruby)
			) ENGINE = {$engine} DEFAULT CHARSET = utf8  COLLATE utf8_unicode_ci
SQL;
		return $query;
	}


} 