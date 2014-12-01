<?php

namespace MeCabSweet\Pattern;


/**
 * Base Controller
 *
 * @package MeCabSweet\Pattern
 */
abstract class Controller extends Application
{

	/**
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * @var array
	 */
	private static $rewrites = array();

	/**
	 * Prefix
	 *
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * @var array
	 */
	protected $query_vars = array('mecab-api', 'mecab-method');

	/**
	 * @var string
	 */
	protected $mime_type = 'application/json';


	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct( array $settings = array() ) {
		self::$rewrites = array_merge(self::$rewrites, $this->get_rewrite_rules());
		if( !self::$initialized ){
			// Add query vars
			add_filter('query_vars', array($this, 'query_vars'));
			// Customize rewrite rules
			add_filter('rewrite_rules_array', array($this, 'generate_rewrite_rules'));
			// Rewrite rule check and update
			add_action('admin_init', array($this, 'admin_init'));
			// Parse Request
			add_action('pre_get_posts', array($this, 'pre_get_posts'));
			self::$initialized = true;
		}
	}

	/**
	 * Overwrite query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function query_vars($vars){
		foreach( $this->query_vars as $var ){
			if( false === array_search($var, $vars) ){
				$vars[] = $var;
			}
		}
		return $vars;
	}


	/**
	 * Add rewrite rules
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	public function generate_rewrite_rules( array $rules ){
		return array_merge(self::$rewrites, $rules);
	}

	/**
	 * Get rewrite rules
	 *
	 * @return array
	 */
	protected function get_rewrite_rules(){
		return !$this->prefix ? array() : array(
			"{$this->prefix}(/.*/?)$" => 'index.php?mecab-api='.get_called_class().'&mecab-method=$matches[1]',
		);
	}

	/**
	 * Add WP_Query action
	 *
	 * @param \WP_Query $wp_query
	 */
	final public function pre_get_posts( \WP_Query &$wp_query){
		if( $wp_query->is_main_query() && ($class_name = $wp_query->get('mecab-api')) ){
			try{
				// Check class existence
				$class_name = stripslashes($class_name);
				if( !class_exists($class_name) ){
					throw new \RuntimeException($this->i18n->_sp('Class %s doesn\'t exist.', $class_name), 404);
				}
				// Check if it's a proper extension
				$reflection = new \ReflectionClass($class_name);
				if( !$reflection->isSubclassOf('MeCabSweet\\Pattern\\Controller') ){
					throw new \RuntimeException($this->i18n->_sp('Class %s must be sub class of MeCabSweet\\Pattern\\Controller.', $class_name), 500);
				}
				// O.K. Let's make instance
				/** @var self $instance */
				$instance = $class_name::get_instance();
				// build method name and arguments
				$arguments = explode('/', trim($wp_query->get('mecab-method'), '/'));
				if( empty($arguments) ){
					$method_name = 'index';
				}else{
					$method_name = strtolower(str_replace('-', '_', array_shift($arguments)));
				}
				$request_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '';
				switch( $request_method ){
					case 'post':
					case 'put':
					case 'delete':
					case 'get':
					case 'head':
					case 'options':
						$method_name = $request_method.'_'.$method_name;
						break;
					default:
						throw new \RuntimeException($this->i18n->_sp('Request method %s is not allowed.', strtoupper($request_method)), 405);
						break;
				}
				// Check method accessibility
				$method_reflection = new \ReflectionMethod($class_name, $method_name);
				if( !$method_reflection->isPublic() || $method_reflection->isStatic() ){
					throw new \RuntimeException($this->i18n->_sp('%1$s of %2$s must be public and not static method.', $method_name, $class_name), 405);
				}
				// Check method arguments length
				$min = $method_reflection->getNumberOfRequiredParameters();
				$max = $method_reflection->getNumberOfParameters();
				if( count($arguments) < $min || count($arguments) > $max ){
					throw new \RuntimeException($this->i18n->_sp('You requested %1$d arguments for %3$s::%2$s(), but arguments length must be between %4$d and %5$d.', count($arguments), $method_name, $class_name, $min, $max), 405);
				}
				// Nocache header
				switch( $request_method ){
					case 'post':
					case 'put':
					case 'delete':
						$this->nocache_header();
						break;
					default:
						// Do nothing. Depends on method
						break;
				}
				$result = call_user_func_array(array($instance, $method_name), $arguments);
				$instance->do_result($result);
			}catch ( \Exception $e ){
				$message = $this->i18n->__('Sorry, but you can\'t access this URL.');
				if( WP_DEBUG ){
					$message .= ' '.$e->getMessage();
				}
				$this->do_wp_die($message, $e->getCode());
			}
 		}
	}

	/**
	 * Process result
	 *
	 * @param mixed $result
	 */
	public function do_result($result){
		switch( $this->mime_type ){
			case 'application/json':
				wp_send_json($result);
				break;
			case 'text/html':
				echo $result;
				exit;
				break;
			default:
				throw new \RuntimeException($this->i18n->_sp('%s is invalid mime type.', $this->mime_type), 405);
				break;
		}
	}

	/**
	 * Wrapper for nocache headers
	 */
	protected function nocache_header(){
		nocache_headers();
	}

	/**
	 * If not logged in, immediately throw error
	 *
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function auth_force(){
		if( !is_user_logged_in() ){
			throw new \RuntimeException($this->i18n->__('You must be logged in.'), 403);
		}
		return true;
	}

	/**
	 * If condition is false, throw error.
	 *
	 * @param bool $condition
	 * @throw \RuntimeException
	 */
	protected function permission_check($condition = true){
		if( !$condition ){
			throw new \RuntimeException($this->i18n->__('You have no permission'), 403);
		}
	}

	/**
	 * Throw not found error
	 *
	 * @param string $message
	 * @throw \RuntimeException
	 */
	protected function not_found($message = ''){
		if( !$message ){
			$message = $this->i18n->__('Not found.');
		}
		throw new \RuntimeException($message, 404);
	}

	/**
	 * Do wp_die
	 *
	 * @param string $message
	 * @param int $code
	 * @param bool $back_link
	 */
	protected function do_wp_die($message, $code = 500, $back_link = true){
		wp_die($message, get_status_header_desc($code).' | '.get_bloginfo('name'), array(
			'response' => $code,
			'back_link' => true,
		));
	}

	/**
	 * Update rewrite rules if possible
	 */
	public function admin_init(){
		if( ($rewrite_rules = get_option('rewrite_rules')) && current_user_can('manage_options') ){
			foreach( self::$rewrites as $rewrite => $regexp ){
				if( !array_key_exists($rewrite, $rewrite_rules) ){
					flush_rewrite_rules();
					$message = $this->i18n->__('Rewrite rules are updated.');
					add_action('admin_notices', function() use ($message){
						printf('<div class="updated"><p>%s</p></div>', $message);
					});
					break;
				}
			}
		}
	}

}
