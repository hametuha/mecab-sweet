<?php

namespace MeCabSweet\Utility;


use MeCabSweet\Pattern\Application;
use MeCabSweet\Expression\Morpheme;

/**
 * Dictionary class
 *
 * @package MeCabSweet\Utility
 * @property-read string|false $dic_dir
 * @property-read string|false $dic_bin
 * @property-read string|false $csv
 * @property-read string|false $dict_index
 * @property-read array $sys_dic
 * @property-read array $user_dic
 * @property-read array $dic_info
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
		// Check target directory
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
	 * Timestamp of last modified
	 *
	 * @param string $which
	 * @return int
	 */
	public function last_compiled($which = 'csv'){
		switch( $which ){
			case 'dic':
				$file = $this->dic_bin;
				break;
			default:
				// Means CSV
				$file = $this->csv;
				break;
		}
		if( !file_exists($file) ){
			return 0;
		}
		return filemtime($file);
	}



	/**
	 * Compile CSV
	 *
	 * @return bool|\WP_Error
	 */
	public function make_csv(){
		$error = new \WP_Error();
		// Check old
		if( $this->csv && !unlink($this->csv) ){
			$error->add(500, $this->i18n->__('Failed to remove old CSV. Please check permission.'));
			return $error;
		}
		// Create handler
		$f = fopen($this->option->user_dic_path, 'w');
		if( !$f ){
			$error->add(500, $this->i18n->__('Failed to create new CSV. Please check permission.'));
			return $error;
		}
		// Retrieve all data and compile them.
		set_time_limit(0);
		$counter = 1;
		$parsed = 0;
		while( $counter > 0 ){
			$terms = $this->models->terms->get_terms(array(
				'page' => $counter,
				'per_page' => 100,
			));
			if( !$terms ){
				$counter = 0;
			}else{
				foreach( $terms as $term ){
					/** @var Morpheme $term */
					fputcsv($f, $term->get_csv_row());
					$parsed++;
				}
				$counter++;
			}
		}
		// Finish creating
		fclose($f);
		return true;
	}

	/**
	 * Make binary dictionary
	 *
	 * @return bool|\WP_Error
	 */
	public function make_binary(){
		try{
			if( !$this->csv ){
				throw new \RuntimeException($this->i18n->__('No CSV found. You must create or upload it.'));
			}
			if( !is_writable($this->dic_dir) || (file_exists($this->dic_bin) && !unlink($this->dic_bin)) ){
				throw new \RuntimeException($this->i18n->__('Directory is not writable or Existing dictionary cannot be deleted. Please check permission.'));
			}
			if( !$this->dict_index || !$this->sys_dic ){
				throw new \RuntimeException($this->i18n->__('System dictionary not found.'));
			}
			// O.k.
			$command = sprintf('%s -d %s -u %s -f utf-8 -t utf-8 %s',
				escapeshellcmd($this->dic->dict_index),
				escapeshellarg(dirname($this->dic->sys_dic['filename'])),
				escapeshellarg($this->dic->dic_bin),
				escapeshellarg($this->dic->csv));
			exec($command, $return);
			return false !== strpos($return, 'done!');
		}catch( \Exception $e ){
			return new \WP_Error($e->getCode(), $e->getMessage());
		}
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
			case 'dict_index':
				$bin = $this->option->dict_index_path;
				if( !$bin || !is_executable($bin) || !preg_match('/mecab-dict-index$/u', $bin) ){
					return false;
				}
				return $bin;
				break;
			case 'sys_dic':
				$dics = $this->dic_info;
				return isset($dics[0]) ? $dics[0] : array();
				break;
			case 'user_dic':
				$dics = $this->dic_info;
				return isset($dics[1]) ? $dics[1] : array();
				break;
			case 'dic_info':
				if( !class_exists('MeCab_Tagger') ){
					return array();
				}
				$tagger = new \MeCab_Tagger();
				return $tagger->dictionaryInfo();
				break;
			default:
				return parent::__get( $key );
				break;
		}
	}

} 