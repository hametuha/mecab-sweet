<?php

namespace MeCabSweet;


use MeCabSweet\Pattern\Application;
use MeCabSweet\Screen\FullTextSearch;
use MeCabSweet\Screen\Setting;
use MeCabSweet\Screen\UserDictionary;

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
			wp_enqueue_script('mecab-sweet-admin', $this->base_url.'assets/js/mecab-admin.min.js', array('jquery-form'), $this->version, true);
		}
	}


	/**
	 * Init hook
	 */
	public function init(){
		// Add filter if fulltext search is on
		if( $this->option->fulltext_search ){
			// Query filter
			add_filter('posts_join', array($this->index_table, 'posts_join'), 10, 2);
			add_filter('posts_search', array($this->index_table, 'posts_search'), 10, 2);
			// On update post
			add_action('save_post', array($this->index_table, 'save_post'), 10, 2);
			add_action('delete_post', array($this->index_table, 'delete_post'));
		}
	}


}
