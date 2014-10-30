<?php
/*
Plugin Name: MecabSweet
Plugin URI: http://wordpress.org/extend/plugins/mecab-sweet/
Description: A suite of Mecab powered utilities. Tokenizer, Full text search and so on.
Author: Hametuha inc.
Version: 1.0
Author URI: http://hametuha.co.jp
Text Domain: mecab-sweet
Domain Path: /language/
License: MIT
*/

/**
 * Version of MecabSweet
 */
define('MECAB_SWEET_VERSION', '1.0');

/**
 * Plugin's text domain
 */
define('MECAB_SWEET_DOMAIN', 'mecab-sweet');


// Setup hook
add_action( 'plugins_loaded', '_mecabsweet_setup_after_plugins_loaded');


/**
 * Setup Plugin
 *
 * @ignore
 */
function _mecabsweet_setup_after_plugins_loaded(){
	// Add i18n
	load_plugin_textdomain(MECAB_SWEET_DOMAIN, false, 'mecab-sweet/language');
	// Load Bootstrap if possible
	if( version_compare(phpversion(), '5.3.0', '<') ) {
		// Requires PHP >= 5.3
		add_action('admin_notices', '_mecabsweet_php_version_error');
	}else{
		require dirname(__FILE__).DIRECTORY_SEPARATOR.'bootstrap.php';
	}
}


/**
 * PHP version error message
 *
 * @ignore
 */
function _mecabsweet_php_version_error(){
	printf('<div class="error"><p>%s</p></div>', esc_html(sprintf(__('Your PHP version %s is too old. Plugin MecabSweet requires PHP 5.3 and over.', MECAB_DOMAIN), phpversion())));
}



// For PoEdit scraping.
if( false ){
	__('A suite of Mecab powered utilities. Tokenizer, Full text search and so on.', MECAB_DOMAIN);
}
