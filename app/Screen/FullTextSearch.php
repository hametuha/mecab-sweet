<?php

namespace MecabSweet\Screen;


use MeCabSweet\Pattern\AdminScreen;


/**
 * Full Text Search Class
 *
 * @package MecabSweet\Screen
 */
class FullTextSearch extends AdminScreen
{

	/**
	 * Admin init action
	 *
	 */
	public function admin_init() {
		if( !$this->is_ajax() ){
			// Update table
			if( current_user_can('manage_options') && $this->index_table->update_table() ){
				$this->set_message($this->i18n->__('Full Text Search Table has been updated.'));
			}
		}else{
			add_action('wp_ajax_mecab_index_build', array($this, 'ajax'));
			add_action('wp_ajax_mecab_performance_check', array($this, 'performance'));
		}
	}

	/**
	 * Build Ajax action
	 */
	public function ajax(){
		$json = array(
			'finished' => false,
			'offset' => 0,
		);
		try{
			if( !$this->option->fulltext_search
			    || !current_user_can('manage_options')
			    || !$this->input->verify_nonce('mecab_index_build')
			){
				throw new \Exception('No permission', 403);
			}
			$offset = (int) $this->input->post('offset');
			$per_page = 100;
			// Get recently updated post
			$query = new \WP_Query(array(
				'post_type' => $this->index_table->get_post_types(),
				'post_status' => 'any',
				'posts_per_page' => $per_page,
				'offset' => $per_page * $offset,
				'order' => 'ASC',
				'orderby' => 'modified',
			));
			$total = $query->post_count + ($offset * $per_page);
			if( $query->have_posts() ){
				while( $query->have_posts() ){
					$query->the_post();
					$this->index_table->add_index(get_post());
				}
				$json['offset'] = $offset + 1;
				$json['message'] = $this->i18n->_sp('%1$d of %2$d posts have been processed.', $total, $query->found_posts);
				$json['ratio'] = floor(100 * $total / $query->found_posts).'%';
			}else{
				$json['message'] = $this->i18n->__('Now, all posts have been indexed! Reload this page.');
				$json['finished'] = true;
				$json['ratio'] = '100%';
			}
		}catch ( \Exception $e ){
			status_header($e->getCode());
		}
		wp_send_json($json);
	}

	/**
	 * Check performance
	 */
	public function performance(){
		$json = array(
			'message' => $this->i18n->__('You have no permission.')
		);
		if( current_user_can('manage_options') ){
			$term = $this->input->get('s');
			$args = array(
				'post_type' => $this->index_table->get_post_types(),
				'post_status' => 'any',
				'posts_per_page' => 100,
				'order' => 'ASC',
				'orderby' => 'modified',
				'suppress_filters' => true,
				's' => $term,
			);
			// Get normal
			$start_normal = microtime(true);
			$normal_query = new \WP_Query($args);
			$normal_passed = number_format_i18n( (microtime(true) - $start_normal) * 1000, 2);
			// With fulltext
			$args['suppress_filters'] = false;
			$start_ft = microtime(true);
			$ft_query = new \WP_Query($args);
			$ft_passed = number_format_i18n( (microtime(true) - $start_ft) * 1000, 2);
			// Build message
			$messages = array();
			$messages[] = $this->i18n->_sp('Search Query: %s', $term);
			$messages[] = $this->i18n->_sp('Posts Per Page: %d', 100);
			$messages[] = '============';
			$messages[] = $this->i18n->_sp('%1$s: %2$d of %3$d posts found in %4$sms', 'LIKE', $normal_query->post_count, $normal_query->found_posts, $normal_passed);
			$messages[] = '------------';
			$messages[] = $this->i18n->_sp('%1$s: %2$d of %3$d posts found in %4$sms', 'FullText', $ft_query->post_count, $ft_query->found_posts, $ft_passed);
			$json['message'] = implode("\n", $messages);
		}
		wp_send_json($json);
	}

}
