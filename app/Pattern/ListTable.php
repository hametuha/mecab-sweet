<?php

namespace MeCabSweet\Pattern;


use MeCabSweet\Utility\Dictionary;
use MeCabSweet\Utility\i18n;
use MeCabSweet\Utility\Input;
use MeCabSweet\Utility\String;

/**
 * List table
 *
 * @package MeCabSweet\Pattern
 * @property-read Input $input
 * @property-read i18n $i18n
 * @property-read \MeCabSweet\Utility\String $str
 * @property-read Dictionary $dic
 * @property-read \wpdb $db
 * @property-read ModelAccessor $models
 * @property-read bool $library_exists
 */
abstract class ListTable extends \WP_List_Table
{

	/**
	 * Singular name of item
	 *
	 * @var string
	 */
	protected $singular = 'item';

	/**
	 * Plural name
	 *
	 * @var string
	 */
	protected $plural = 'items';

	/**
	 * @var bool
	 */
	protected $use_ajax = false;

	/**
	 * @var bool
	 */
	protected $show_search_form = false;

	/**
	 * @var int
	 */
	protected $per_page = 10;

	/**
	 * Constructor
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ){
		$args = wp_parse_args($args, array(
			'plural' => $this->plural,
			'singular' => $this->singular,
			'ajax' => $this->use_ajax,
			'screen' => null,
		));
		parent::__construct($args);
	}

	/**
	 * Prepare items
	 */
	public function prepare_items(){
		// Set header
		$this->_column_headers = array(
			$this->get_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns(),
		);
		// Get Data
		$this->items = $this->retrieve();
		// Set args
		$this->set_pagination_args(array(
			'total_items' => $this->get_total_count(),
			'per_page' => $this->per_page,
		));
	}

	/**
	 * Retrieve table data to display
	 *
	 * @return array
	 */
	abstract protected function retrieve();

	/**
	 * Get total count of record
	 *
	 * @return int
	 */
	protected function get_total_count(){
		return (int) $this->db->get_var("SELECT FOUND_ROWS()");
	}

	/**
	 * List of hidden column
	 *
	 * @return array
	 */
	public function get_hidden_columns(){
		return array();
	}

	/**
	 * Render table
	 *
	 * @param array $args
	 */
	public static function render( $args = array() ){
		$class_name = get_called_class();
		/** @var self $instance */
		$instance = new $class_name($args);
		$instance->prepare_items();
		if( $instance->show_search_form ){
			$instance->search_box($instance->i18n->__('Search'), 's');
		}
		$instance->display();
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get($name){
		switch( $name ){
			case 'input':
				return Input::get_instance();
				break;
			case 'i18n':
				return i18n::get_instance(array('domain' => MECAB_SWEET_DOMAIN));
				break;
			case 'str':
				return String::get_instance();
				break;
			case 'dic':
				return Dictionary::get_instance();
				break;
			case 'db':
				global $wpdb;
				return $wpdb;
				break;
			case 'models':
				return ModelAccessor::get_instance();
				break;
			default:
				return parent::__get($name);
				break;
		}
	}
}
