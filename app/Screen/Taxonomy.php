<?php

namespace MeCabSweet\Screen;


use MeCabSweet\Pattern\AdminScreen;


class Taxonomy extends AdminScreen
{

	/**
	 *
	 */
	public function admin_init() {
		if( !$this->is_ajax() ){
			// Update table
			if( $this->models->terms->update_table() ){
				$this->set_message($this->i18n->__('Dictionary table has been updated.'));
			}
		}
	}


} 