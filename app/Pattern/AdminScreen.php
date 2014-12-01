<?php

namespace MeCabSweet\Pattern;


/**
 * Admin screen class
 *
 * @package MecabSweet\Pattern
 */
abstract class AdminScreen extends Application
{

	const SESSION_KEY = 'mecab_message';

	/**
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var bool
	 */
	protected $is_main_menu = false;

	/**
	 * @var null
	 */
	protected $position = null;

	/**
	 * @var string
	 */
	protected $template_name = '';

	/**
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * @param array $settings
	 *      menu_title => string
	 *      title => string
	 *      menu_title => string
	 *      parent_slug => string
	 *      icon_url => string
	 *      parent_menu_title => string
	 */
	protected function __construct( array $settings = array() ) {
		$this->settings = wp_parse_args($settings, array(
			'menu_title' => '',
			'slug' => '',
			'title' => '',
			'parent_slug' => '',
			'parent_menu_title' => '',
			'icon_url' => '',
		));
		if( empty($this->template_name) ){
			$class_name = explode('\\', get_called_class());
			$this->template_name = strtolower(preg_replace_callback('/(.)([A-Z])/u', function($matches){
				return $matches[1].'-'.strtolower($matches[2]);
			}, $class_name[count($class_name) - 1]));
		}
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_init', array($this, 'admin_init'));
		if( !self::$initialized ){
			self::$initialized = true;
			$self = $this;
			add_action('admin_init', function() use ($self) {
				if( !$self->is_ajax() ){
					if( !session_id() ){
						session_start();
					}
				}
			}, 10000);
			add_action('admin_notices', array($this, 'admin_notices'));
		}
	}

	/**
	 * Admin inti hook
	 */
	public function admin_init(){
		// Override this function if required.
	}

	/**
	 * Add menu
	 */
	public function admin_menu(){
		if( $this->is_main_menu ){
			add_menu_page($this->settings['parent_menu_title'], $this->settings['parent_menu_title'], $this->capability, $this->settings['slug'], array($this, 'render'), $this->settings['icon_url'], $this->position);
			add_submenu_page($this->settings['slug'], $this->settings['title'], $this->settings['menu_title'], $this->capability, $this->settings['slug'], array($this, 'render'));
		}else{
			add_submenu_page($this->settings['parent_slug'], $this->settings['title'], $this->settings['menu_title'], $this->capability, $this->settings['slug'], array($this, 'render'));
		}
	}

	/**
	 * Render admin screen
	 */
	public function render(){
		echo '<div class="wrap mecab-sweet-admin-wrap">';
		include $this->base_dir.'/templates/header.php';
		$this->render_main_content();
		include $this->base_dir.'/templates/footer.php';
		echo '</div>';
	}

	/**
	 * Get main content
	 */
	protected function render_main_content(){
		$template = $this->get_template();
		if( is_wp_error($template) ){
			printf('<div class="error"><p>%s</p></div>', $template->get_error_message());
		}else{
			include $template;
		}
	}

	/**
	 * Get template
	 *
	 * @return string|\WP_Error If failed to get Template, return WP_Error
	 */
	protected function get_template(){
		$template_path = $this->base_dir.'/templates/'.$this->template_name.'.php';
		if( file_exists($template_path) ){
			return $template_path;
		}else{
			return new \WP_Error(404, $this->i18n->_sp('File <code>%s</code> doesn\'t exist.', $template_path));
		}
	}

	/**
	 * Include template
	 *
	 * @param string $template
	 */
	protected function load_template($template){
		$template_path = $this->base_dir.'/templates/'.$template.'.php';
		if( file_exists($template_path) ){
			include $template_path;
		}
	}

	/**
	 * Detect current page
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function is_page($slug){
		return $slug == $this->input->get('page');
	}

	/**
	 * Admin URL
	 *
	 * @param string $slug
	 * @return string
	 */
	public function url($slug){
		return admin_url('admin.php?page='.$slug);
	}


	/**
	 * Set admin message
	 *
	 * @param string $message
	 * @param bool $error
	 */
	protected function set_message($message, $error = false){
		if( !session_id() ){
			session_start();
		}
		if( !isset($_SESSION[self::SESSION_KEY]) ){
			$_SESSION[self::SESSION_KEY] = array();
		}
		$_SESSION[self::SESSION_KEY][] = array(
			'error' => $error,
			'message' => $message,
		);
	}

	/**
	 * Show admin message
	 */
	public function admin_notices(){
		if( isset($_SESSION[self::SESSION_KEY]) && !empty($_SESSION[self::SESSION_KEY]) ){
			$messages = array('error' => array(), 'updated' => array());
			foreach( $_SESSION[self::SESSION_KEY] as $message ){
				$key = $message['error'] ? 'error' : 'updated';
				$messages[$key][] = $message['message'];
			}
			foreach( $messages as $key => $message ){
				if( !empty($message) ){
					printf('<div class="%s"><p>%s</p></div>', $key, implode('<br />', $message));
				}
			}
			$_SESSION[self::SESSION_KEY] = array();
		}
	}


	/**
	 * Register Admin screen
	 *
	 * @param array $settings
	 */
	public static function register( array $settings = array() ){
		self::get_instance($settings);
	}

	/**
	 * Whether if this request is ajax
	 *
	 * @return bool
	 */
	public function is_ajax(){
		return defined('DOING_AJAX') && DOING_AJAX;
	}


}
