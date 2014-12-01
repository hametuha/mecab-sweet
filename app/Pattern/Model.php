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
		return version_compare($this->table_version, $this->current_version, '>');
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
	 * Validate arguments.
	 *
	 * @param array $args
	 * @param bool $need_required
	 *
	 * @return true|\WP_Error
	 */
	protected function validate($args, $need_required = true){
		$error = new \WP_Error();
		foreach( $this->get_validation_config() as $key => $config ){
			$config = wp_parse_args($config, array(
				'label' => $key,
				'required' => true,
				'format' => 'string',
				'list' => array(),
				'regexp' => '',
			));
			if( !isset($args[$key]) || ('' === $args[$key] ) ) {
				// Argument is not set.
				if( $need_required ){
					$error->add(500, $this->i18n->_sp('%s is required.', $config['label']));
				}
			}else{
				// Argument found. Let's validate
				$value = $args[$key];
				foreach( $config as $name => $setting ){
					switch( $name ){
						case 'format':
							switch( $setting ){
								case 'numeric':
									if( !is_numeric($value) ){
										$error->add(500, $this->i18n->_sp('%s should be numeric', $config['label']));
									}
									break;
								case 'int':
									if( !is_int($value) ){
										$error->add(500, $this->i18n->_sp('%s should be integer', $config['label']));
									}
									break;
								case 'regexp':
									if( !preg_match($config['regexp'], $value) ){
										$error->add(500, $this->i18n->_sp('%s is invalid format.', $config['label']));
									}
									break;
								case 'date':
									if( !preg_match('/\A[0-9]{4}-[0-9]{4}-[0-9]{2}\z/u', $value) ){
										$error->add(500, $this->i18n->_sp('%s should be YYYY-MM-DD.', $config['label']));
									}
									break;
								case 'datetime':
									if( !preg_match('/\A[0-9]{4}-[0-9]{4}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\z/u', $value) ){
										$error->add(500, $this->i18n->_sp('%s should be YYYY-MM-DD HH:MM:SS.', $config['label']));
									}
									break;
								case 'enum':
									if( false === array_search($value, $config['list']) ){
										$error->add(500, $this->i18n->_sp('%1$s must be one of %2$s.', $config['label'], implode(', ', $config['list'])));
									}
									break;
								case 'email':
									if( !is_email($value) ){
										$error->add(500, $this->i18n->_sp('%s should be email format.', $config['label']));
									}
									break;
								case 'url':
									if( !preg_match('/\A(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\z/u', $value) ){
										$error->add(500, $this->i18n->_sp('%s should be URL.', $config['label']));
									}
									break;
								default:
									// No validation.
									break;
							}
							break;
						case 'more_than':
							if( !( is_numeric($value) && $value > $setting ) ){
								$error->add(500, $this->i18n->_sp('%1$s should be more than %2$s.', $config['label'], $setting));
							}
							break;
						case 'less_than':
							if( !( is_numeric($value) && $value < $setting ) ){
								$error->add(500, $this->i18n->_sp('%1$s should be more than %2$s.', $config['label'], $setting));
							}
							break;
						default:
							// Do nothing.
							break;
					}
				}
			}
		}
		return $error->get_error_messages() ? $error : true;
	}


	/**
	 * Override this function to validate
	 * 
	 * @return array
	 */
	protected function get_validation_config(){
		return array();
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