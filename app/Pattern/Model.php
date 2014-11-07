<?php

namespace MeCabSweet\Pattern;

/**
 * Model Abstract class
 *
 * @package MeCabSweet\Pattern
 * @property-read \wpdb $db
 * @property-read string $current_version
 * @property-read string $table
 */
abstract class Model extends Application
{


	/**
	 * @var string
	 */
	public $table_version = '1.0';

	/**
	 * @var string
	 */
	protected $table_name = 'mecab_index';


	/**
	 * @var string
	 */
	protected $key = 'mecab_index_table_version';

	/**
	 * @var bool
	 */
	protected $only_on_main_blog = false;

	/**
	 * Update table
	 *
	 * @return bool
	 */
	public function update_table(){
		if( $this->current_user_can_update_table() && $this->is_outdated() && ($sql = $this->create_sql()) ){
			require_once ABSPATH.'wp-admin/includes/upgrade.php';
			dbDelta($sql);
			update_option($this->key, $this->version);
			return true;
		}
		return false;
	}

	/**
	 * Creation SQL
	 *
	 * @return string
	 */
	abstract protected function create_sql();

	/**
	 * Check if table version is outdated
	 *
	 * @return bool
	 */
	public function is_outdated(){
		return version_compare($this->version, $this->current_version, '>');
	}

	/**
	 * Detect database install capability
	 *
	 * @return bool
	 */
	public function current_user_can_update_table(){
		if( is_multisite() ){
			return current_user_can('manage_network_options');
		}else{
			return current_user_can('manage_options');
		}
	}

	/**
	 * Check MySQL version
	 *
	 * @return bool
	 */
	protected function mysql_is_new(){
		$version = $this->db->get_var("SELECT version()");
		return version_compare($version, '5.6-*', '>=');
	}

	/**
	 * Detect table is properly installed
	 *
	 * @return bool
	 */
	public function table_exists(){
		return (bool)$this->db->get_row($this->db->prepare('SHOW TABLES LIKE %s', $this->table));
	}

	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return mixed|null|string|void
	 */
	public function __get($key){
		switch( $key ){
			case 'current_version':
				return get_option($this->key, 0);
				break;
			case 'db':
				global $wpdb;
				return $wpdb;
				break;
			case 'table':
				return ($this->only_on_main_blog ? $this->db->base_prefix : $this->db->prefix).$this->table_name;
				break;
			default:
				return parent::__get($key);
				break;
		}
	}

} 