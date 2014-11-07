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
		// TODO: Validation

		// TODO: existance check

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
		if( $result ){
			return array(
				'success' => true,
				'message' => $this->i18n->_sp('%s is successfully added.', esc_html($this->input->post('morpheme'))),
				'reset' => true,
			);
		}else{
			return array(
				'success' => false,
				'message' => $this->i18n->__('Sorry, but failed to save terms.')
			);
		}
	}

} 