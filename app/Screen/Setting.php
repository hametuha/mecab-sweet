<?php

namespace MeCabSweet\Screen;


use MeCabSweet\Pattern\AdminScreen;

/**
 * Setting screen
 *
 * @package MecabSweet\Screen
 */
class Setting extends AdminScreen
{

	protected $is_main_menu = true;

	protected $position = 150;

	/**
	 * Update option
	 *
	 */
	public function admin_init() {
		if( !$this->is_ajax()  ){
			if( $this->input->verify_nonce('mecab-setting')){
				try{
					if( !current_user_can('manage_options') ){
						throw new \Exception($this->i18n->__('You have no permission.'));
					}
					$this->option->update();
					$this->set_message($this->i18n->__('Setting updated.'));
				}catch ( \Exception $e ){
					$this->set_message($e->getMessage(), true);
				}
				wp_safe_redirect($this->url('mecab-setting'));
				exit;
			}
		}
	}


} 