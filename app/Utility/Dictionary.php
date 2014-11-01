<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Application;

/**
 * Dictionary class
 *
 * @package MeCabSweet\Utility
 * @property-read string|false $dic_dir
 * @property-read string|false $dic_bin
 * @property-read string|false $csv
 */
class Dictionary extends Application
{

	/**
	 * Register user dictionary
	 */
	public function register_user_dic(){
		if( $this->dic_bin ){
			// Specify default user dictionary
			ini_set('mecab.default_userdic', $this->dic_bin);
		}
	}

	/**
	 * Check directory permission
	 *
	 * @return bool
	 *
	 * @throws \RuntimeException
	 */
	public function check_permission(){
		$dir = $this->dic_dir;
		if( !$dir || !is_dir($dir) ){
			throw new \RuntimeException($this->i18n->__('Directory doesn\'t exist.'), 404);
		}
		if( !is_writable($dir) ){
			throw new \RuntimeException($this->i18n->_sp('Directory %s isn\'t writable.', $dir), 403);
		}
		if( $this->option->taxonomy && ($this->csv && !is_writable($this->csv)) ){
			throw new \RuntimeException($this->i18n->_sp('CSV %s isn\'t writable.', $this->csv), 403);
		}
		if( !$this->option->taxonomy && !$this->csv ){
			throw new \RuntimeException($this->i18n->__('CSV doesn\'t exist.'), 403);
		}
		return true;
	}

	/**
	 * Create pseudo CSV file.
	 *
	 * @return string
	 */
	public function install_csv(){
		$upload_dir = wp_upload_dir();
		$base_dir = $upload_dir['basedir'];
		// Check if upload directory exists.
		if( !is_dir($upload_dir['basedir']) || !is_writable($base_dir) ){
			throw new \RuntimeException($this->i18n->_sp('Permission denied. %s is not writable.', $base_dir), 403);
		}
		// Check target diretory
		$target_dir = $base_dir.DIRECTORY_SEPARATOR.'mecab';
		if( is_dir($target_dir) && !is_writable($target_dir) ){
			throw new \RuntimeException($this->i18n->_sp('Directory %s exists but is not writable.', $target_dir), 403);
		}
		// Try create directory
		if( !is_dir($target_dir) && !mkdir($target_dir, 0755) ){
			throw new \RuntimeException($this->i18n->_sp('Sorry, but failed to create directory. Check permission of %s.', dirname($target_dir)), 403);
		}
		// Now, we have target directory
		$csv_path = $target_dir.DIRECTORY_SEPARATOR.'user-dic.csv';
		if( !file_exists($csv_path) && !touch($csv_path) ) {
			throw new \RuntimeException($this->i18n->_sp('Sorry, but failed to create CSV file %s.', $csv_path), 400);
		}
		return $csv_path;
	}


	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return bool|mixed|null|string
	 */
	public function __get( $key ) {
		switch( $key ){
			case 'csv':
				if( file_exists($this->option->user_dic_path) ){
					return $this->option->user_dic_path;
				}else{
					return false;
				}
				break;
			case 'dic_bin':
				if( $this->csv ){
					return $this->csv.'.dic';
				}
				return false;
				break;
			case 'dic_dir':
				$csv = $this->option->user_dic;
				if( !$csv ){
					return false;
				}
				return dirname($csv);
				break;
			default:
				return parent::__get( $key );
				break;
		}
	}

} 