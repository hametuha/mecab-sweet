<?php

namespace MeCabSweet\UI\Screen;


use MeCabSweet\Pattern\AdminScreen;
use MeCabSweet\UI\Table\WordList;


class Taxonomy extends AdminScreen
{

	/**
	 * Executed on admin screen
	 */
	public function admin_init() {
		if( !$this->is_ajax() ){
			// Update table
			if( $this->models->terms->update_table() ) {
				$this->set_message( $this->i18n->__( 'Dictionary table has been updated.' ) );
			}

			// Add Script
			if( $this->is_page($this->settings['slug']) ){
				wp_enqueue_script('mecab-word-list', $this->base_url.'assets/js/mecab-admin-word-list.min.js', array('jquery-form', 'jquery-effects-highlight'), $this->version);
				wp_localize_script('mecab-word-list', 'MeCabWordList', array(
					'endpoint' => home_url('/mecab-terms/delete/', force_ssl_admin() ? 'https' : 'http'),
					'nonce' => wp_create_nonce('mecab_term_edit'),
					'confirm' => $this->i18n->__('This action is not cancelable. Are you sure to delete?')
				));
			}
		}
	}

	/**
	 * Show Table
	 */
	protected function show_table() {

		$action = admin_url('admin.php');
		echo <<<HTML
<form action="{$action}" method="get" id="mecab-term-list">
<input type="hidden" name="page" value="mecab-dic-registered" />
HTML;
		wp_nonce_field('mecab_sweet', '_wpnonce', false);
		WordList::render();
		echo '</form>';

	}


}