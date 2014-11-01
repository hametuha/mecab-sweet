<?php

namespace MeCabSweet\Data;


use MeCabSweet\Pattern\Model;


/**
 * Model class for Full text search
 *
 * @package MecabSweet\Data
 */
class SearchIndex extends Model
{

	/**
	 * Get post types to make index
	 *
	 * @return array
	 */
	public function get_post_types(){
		$post_types = get_post_types(array(
			'publicly_queryable' => true,
			'exclude_from_search' => false,
		));
		return $post_types;
	}

	/**
	 * Detect if current post type should be indexed
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function is_post_type_to_index($post_type){
		$post_types = $this->get_post_types();
		return false !== array_search($post_type, $post_types);
	}

	/**
	 *
	 *
	 * @return mixed
	 */
	public function index_status(){
		$post_types = implode(', ', array_map(function($post_type){
			return $this->db->prepare('%s', $post_type);
		}, $this->get_post_types()));
		$query = <<<SQL
			SELECT COUNT(posts.ID) AS total, COUNT(ind.post_id) AS indexed
			FROM {$this->db->posts} AS posts
			LEFT JOIN {$this->table} AS ind
			ON posts.ID = ind.post_id
			WHERE posts.post_type IN ({$post_types})
SQL;
		return $this->db->get_row($query);
	}

	/**
	 * Add index to post.
	 *
	 * @param \WP_Post $post
	 *
	 * @return bool
	 */
	public function add_index( \WP_Post $post ){
		$title = $this->str->split($post->post_title);
		$excerpt = $this->str->split($post->post_excerpt);
		$content = $this->str->split($post->post_content);
		$text = implode(' ', array_merge($title, $excerpt, $content));
		$query = <<<SQL
			INSERT INTO {$this->table} (post_id, content)
			VALUES (%d, %s)
			ON DUPLICATE KEY
			UPDATE content = %s
SQL;
		/**
		 * mecab_converted_text
		 *
		 * @param string $text Split text consists of post_title, post_excerpt, post_content
		 * @param \WP_Post $post
		 * @param array $tokens Array of tokenized text with key 'title', 'excerpt', 'content'
		 * @return string
		 */
		$text = apply_filters('mecab_converted_text', $text, $post, array(
			'title' => $title,
			'excerpt' => $excerpt,
			'content' => $content,
		));
		return (bool)$this->db->query($this->db->prepare($query, $post->ID, $text, $text));
	}


	/**
	 * Creation query
	 *
	 * @return string
	 */
	protected function create_sql(){
		$engine = $this->mysql_is_new() ? 'InnoDb' : 'MyISAM';
		$query = <<<SQL
			CREATE TABLE {$this->table} (
				post_id BIGINT NOT NULL PRIMARY KEY,
				content LONGTEXT NOT NULL,
				updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FULLTEXT mecab_idx (content)
			) ENGINE = {$engine} DEFAULT CHARSET = utf8  COLLATE utf8_unicode_ci
SQL;
		return $query;
	}

	/**
	 * Filter JOIN clause
	 *
	 * @param string $join
	 * @param \WP_query $wp_query
	 *
	 * @return string
	 */
	public function posts_join($join, \WP_query $wp_query){
		if( $wp_query->get('s') ){
			$join .= <<<SQL
				LEFT JOIN {$this->table}
				ON {$this->db->posts}.ID = {$this->table}.post_id
SQL;
		}
		return $join;
	}

	/**
	 * Filter where query
	 *
	 * @param string $where
	 * @param \WP_Query $wp_query
	 *
	 * @return false|null|string
	 */
	public function posts_search($where, \WP_Query $wp_query){
		if( $wp_query->get('s') && !$wp_query->get('suppress_filters') ){
			$match = <<<SQL
				AND MATCH({$this->table}.content) AGAINST( %s WITH QUERY EXPANSION )
SQL;
			$where = $this->db->prepare($match, implode($wp_query->get('search_terms')));
		}
		return $where;
	}

	/**
	 * Rebuild index on update
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save_post($post_id, $post){
		if( wp_is_post_autosave($post) || wp_is_post_revision($post) ){
			return;
		}
		if( !$this->is_post_type_to_index($post->post_type) ){
			return;
		}
		$this->add_index($post);
	}

	/**
	 * Delete index record on post deletion
	 *
	 * @param int $post_id
	 */
	public function delete_post($post_id){
		$this->db->delete($this->table, array('post_id' => $post_id), array('%d'));
	}

}
