<?php
/**
 * Bootstrap for MecabSweet
 */


defined('MECAB_SWEET_VERSION') or die();


// Register auto loader
spl_autoload_register(function($class_name){
	$class_name = ltrim($class_name, '\\');
	if( 0 === strpos($class_name, 'MeCabSweet') ){
		// O.K. This is my name space
		$basedir = __DIR__.DIRECTORY_SEPARATOR.'app' ;
		$path = str_replace('MeCabSweet', $basedir, str_replace('\\', DIRECTORY_SEPARATOR, $class_name)).'.php';
		if( file_exists($path) ){
			require_once $path;
		}
	}
});

// Load functions
require __DIR__.DIRECTORY_SEPARATOR.'functions.php';

// Initialize
MeCabSweet\Main::get_instance();
