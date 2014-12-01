<?php

namespace MeCabSweet;


use MeCabSweet\Controllers\DictionaryController;
use MeCabSweet\Pattern\Application;
use MeCabSweet\UI\Screen\FullTextSearch;
use MeCabSweet\UI\Screen\Setting;
use MeCabSweet\UI\Screen\Taxonomy;
use MeCabSweet\UI\Screen\TaxonomyAdd;
use MeCabSweet\UI\Screen\UserDictionary;

/**
 * Main Routine
 *
 * @package MeCabSweet
 */
class Main extends Application
{


	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct( array $settings = array() ) {

		// Register admin screen
		Setting::register(array(
			'menu_title' => $this->i18n->__('Setting'),
			'slug' => 'mecab-setting',
			'title' => $this->i18n->__('MeCab Sweet Setting'),
			'parent_menu_title' => 'MeCab Sweet',
			'icon_url' => $this->base_url.'/assets/img/icon.png',
		));
		FullTextSearch::register(array(
			'slug' => 'mecab-fulltext-search',
			'title' => $this->i18n->__('Full Text Search'),
			'menu_title' => $this->i18n->__('Full Text Search'),
			'parent_slug' => 'mecab-setting',
		));
		UserDictionary::register(array(
			'slug' => 'mecab-user-dic',
			'title' => $this->i18n->__('User Dictionary'),
			'menu_title' => $this->i18n->__('User Dictionary'),
			'parent_slug' => 'mecab-setting',
		));
		if( $this->option->user_dic ){
			// Enable User dictionary
			$this->dic->register_user_dic();
		}
		if( $this->option->taxonomy && $this->library_exists ){
			// Register admin screen
			Taxonomy::register(array(
				'slug' => 'mecab-dic-registered',
				'title' => $this->i18n->__('Registered Words'),
				'menu_title' => $this->i18n->__('Registered Words'),
				'parent_slug' => 'mecab-setting',
			));
			TaxonomyAdd::register(array(
				'slug' => 'mecab-dic-add',
				'title' => $this->i18n->__('Add Word'),
				'menu_title' => $this->i18n->__('Add Words'),
				'parent_slug' => 'mecab-setting',
			));

			// Add rest API
			DictionaryController::get_instance();
		}



		// If extension doesn't exist, show message.
		if( !$this->library_exists ){
			$message = $this->i18n->_sp('Plugin MeCabSweet requires <a href="%s">PHP-MeCab extension</a>.', 'http://pecl.opendogs.org');
			add_action('admin_notices', function() use ($message){
				printf('<div class="error"><p>%s</p></div>', $message);
			});
		}

		// Add Script and css
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		// Fix icon size
		add_action('admin_head', function(){
			echo <<<HTML
<style>
.toplevel_page_mecab-setting .wp-menu-image img{
	width: 20px;
	height: 20px;
}
.mecab-sweet-admin-wrap .nav-tab-wrapper > span{
	margin-right: 10px;
}
</style>
HTML;
		});

		add_action('init', array($this, 'init'));

	}

	/**
	 * Admin scripts
	 *
	 * @param string $page
	 */
	public function admin_enqueue_scripts($page){
		if( false !== strpos($page, 'mecab') ){
			wp_enqueue_style('mecab-sweet-admin', $this->base_url.'assets/css/mecab-admin.css', array(), $this->version);
			wp_enqueue_script('mecab-sweet-admin', $this->base_url.'assets/js/mecab-admin.min.js', array('jquery-form', 'jquery-effects-highlight'), $this->version, true);
		}
	}


	/**
	 * Init hook
	 */
	public function init(){
		// Add filter if fulltext search is on
		if( $this->option->fulltext_search ){
			// Query filter
			add_filter('posts_join', array($this->models->search_index, 'posts_join'), 10, 2);
			add_filter('posts_search', array($this->models->search_index, 'posts_search'), 10, 2);
			add_filter('posts_orderby', array($this->models->search_index, 'posts_orderby'), 10, 2);
			// On update post
			add_action('save_post', array($this->models->search_index, 'save_post'), 10, 2);
			add_action('delete_post', array($this->models->search_index, 'delete_post'));
		}
	}


}
