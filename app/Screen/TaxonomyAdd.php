<?php

namespace MeCabSweet\Screen;


use MeCabSweet\Expression\Morpheme;
use MeCabSweet\Pattern\AdminScreen;

class TaxonomyAdd extends AdminScreen
{


	/**
	 * @var Morpheme|null
	 */
	protected $editing_term = null;


	/**
	 * Admin init
	 */
	public function admin_init(){
		if( !$this->is_ajax() && $this->is_page('mecab-dic-add') ){
			// Not Ajax
			$this->assign_morpheme();
		}else{
			// Ajax
		}
	}

	/**
	 * Assign current morpheme
	 */
	protected function assign_morpheme(){
		$term_id = $this->input->request('term_id');
		if( $term_id && ($morpheme = $this->models->terms->get_term($term_id)) ){
			$this->editing_term = $morpheme;
		}
	}

	/**
	 * Detect current screen is editing
	 *
	 * @return bool
	 */
	public function is_editing(){
		return !is_null($this->editing_term);
	}

	/**
	 * Echo morpheme attribute for form
	 *
	 * @param string $key
	 */
	public function morpheme_attr($key){
		echo esc_attr($this->get_morpheme_attr($key));
	}

	/**
	 * Get morpheme attribute if exist
	 *
	 * @param string $key
	 *
	 * @return mixed|string
	 */
	public function get_morpheme_attr($key){
		if( $this->is_editing() && isset($this->editing_term->{$key}) ){
			return $this->editing_term->{$key};
		}else{
			return '';
		}
	}

	protected function assign_term($term_id){

	}

} 