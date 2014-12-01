<?php

namespace MeCabSweet\Controllers;


use MeCabSweet\Pattern\Controller;


class DictionaryController extends Controller
{

	/**
	 * Prefix
	 *
	 * @var string
	 */
	protected $prefix = 'mecab-terms';

	/**
	 * Get term information
	 *
	 * @return array
	 */
	public function get_info(){
		$term = $this->input->get('s');
		if( empty($term) ){
			throw new \RuntimeException($this->i18n->__('String is not specified.'), 400);
		}
		$return = array(
			'tokens' => array(),
			'message' => '',
		);
		$nodes = $this->str->tagger->parseToNode($term);
		foreach( $nodes as $node ){
			/** @var \Mecab_Node $node */
			if( false !== array_search($node->getStat(), array(0, 1)) ){
				$return['tokens'][] = $node->toArray();
			}
		}
		if( empty($return['tokens']) ){
			$return['message'] = $this->i18n->__('No word is found.');
		}
		return $return;
	}

	/**
	 * Add morpheme
	 */
	public function post_add(){
		$this->auth_force();
		$this->permission_check( $this->input->verify_nonce('mecab_term_edit') );
		// TODO: existence check
		$result = $this->models->terms->insert(array(
			'morpheme' => $this->input->post('morpheme'),
			'base_id' => 0,
			'ruby' => $this->input->post('ruby'),
			'pronunciation' => $this->input->post('pronunciation'),
			'cost' => $this->input->post('cost'),
			'part_of_speech' => $this->input->post('part_of_speech'),
			'option_1' => $this->input->post('option_1'),
			'option_2' => $this->input->post('option_2'),
			'option_3' => $this->input->post('option_3'),
		));
		if( is_wp_error($result) ){
			// Show Error
			return array(
				'success' => false,
				'message' => $result->get_error_messages()
			);
		}else{
			return array(
				'success' => true,
				'message' => $this->i18n->_sp('%s is successfully added.', esc_html($this->input->post('morpheme'))),
				'reset' => true,
			);
		}
	}

	/**
	 * Edit term
	 *
	 * @param int $term_id
	 *
	 * @return array
	 */
	public function post_edit($term_id){
		$this->auth_force();
		$this->permission_check($this->input->verify_nonce('mecab_term_edit'));
		$term = $this->models->terms->get_term($term_id);
		if( !$term ){
			$this->not_found();
		}
		$result = $this->models->terms->update($term->term_id, array(
			'morpheme' => $this->input->post('morpheme'),
			'base_id' => 0,
			'ruby' => $this->input->post('ruby'),
			'pronunciation' => $this->input->post('pronunciation'),
			'cost' => $this->input->post('cost'),
			'part_of_speech' => $this->input->post('part_of_speech'),
			'option_1' => $this->input->post('option_1'),
			'option_2' => $this->input->post('option_2'),
			'option_3' => $this->input->post('option_3'),
		));
		if( is_wp_error($result) ){
			// Show Error
			return array(
				'success' => false,
				'message' => $result->get_error_messages()
			);
		}else{
			return array(
				'success' => true,
				'message' => $this->i18n->_sp('%s is successfully edited.', esc_html($this->input->post('morpheme'))),
				'reset' => false,
			);
		}
	}

	/**
	 * Delete word
	 *
	 * @return array
	 */
	public function post_delete(){
		$this->auth_force();
		$this->permission_check($this->input->verify_nonce('mecab_term_edit'));
		$term_id = $this->input->post('term_id');
		if( !is_array($term_id) ){
			$term_id = array($term_id);
		}
		$term_id = array_filter($term_id, 'is_numeric');
		if( count($term_id) < 1 ){
			return array(
				'success' => false,
				'message' => $this->i18n->__('No term is selected.'),
			);
		}
		$deleted = array();
		foreach( $term_id as $id ){
			if( $this->models->terms->delete($id) ){
				$deleted[] = $id;
			}
		}
		if( count($deleted) == count($term_id) ){
			return array(
				'success' => true,
				'message' => false,
				'deleted' => $deleted,
			);
		}else{
			return array(
				'success' => count($deleted) > 0,
				'message' => $this->i18n->_sp('Tried to delete %1$d words, but %1$d succeeded.', count($term_id), count($deleted)),
				'deleted' => $deleted,
			);
		}
	}

	/**
	 * Compile dictionary
	 *
	 * @return array
	 */
	public function post_compile(){
		$this->auth_force();
		$this->permission_check($this->input->verify_nonce('mecab_term_edit') && current_user_can('manage_options'));
		// If file exits, delete
		$result = $this->dic->make_csv();
		if( is_wp_error($result) ){
			return array(
				'success' => false,
				'message' => implode(', ', $result->get_error_messages()),
			);
		}else{
			return array(
				'success' => true,
				'message' => $this->i18n->__('Congrats! CSV has been compiled.'),
			);
		}
	}

	/**
	 * Make binary user dictionary
	 *
	 * @return array
	 */
	public function post_binary(){
		$this->auth_force();
		$this->permission_check($this->input->verify_nonce('mecab_term_edit') && current_user_can('manage_options'));
		// If file exits, delete
		$result = $this->dic->make_binary();
		if( is_wp_error($result) ){
			return array(
				'success' => false,
				'message' => implode(', ', $result->get_error_messages()),
			);
		}else{
			return array(
				'success' => true,
				'message' => $this->i18n->__('Congrats! Dictionary has been compiled.'),
			);
		}
	}

	public function get_test(){
		$this->auth_force();
		$string = $this->input->get('s');
	}

} 