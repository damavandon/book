<?php


if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists("Meto_Book_End_Point")) {

	class Meto_Book_End_Point
	{
		/**
		 * Latest API Version
		 */
		const VERSION = 2;
		/**
		 * Log API requests?
		 *
		 * @var bool
		 * @access private
		 * @since 1.0.0
		 */
		public $log_requests = true;

		/**
		 * Is this a valid request?
		 *
		 * @var bool
		 * @access private
		 * @since 1.0.0
		 */
		private $is_valid_request = false;

		/**
		 * User ID Performing the API Request
		 *
		 * @var int
		 * @access private
		 * @since 1.0.0
		 */
		public $user_id = 0;


		/**
		 * Response data to return
		 *
		 * @var array
		 * @access private
		 * @since 1.0.0
		 */
		private $data = array();

		/**
		 * Endpoints routes
		 *
		 * @var object
		 * @since 1.0.0
		 */
		private $routes;

		/**
		 * All versions of the API
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $versions = array();

		/**
		 * Setup the METO_BG_API  API
		 *
		 * @author Mahdi Torkaman
		 * @since 1.0.0
		 */
		public function __construct()
		{
			$this->versions = array(
				'v1' => 'Meto_Book_API_V1',
				'v2' => 'Meto_Book_API_V2'
			);
			foreach ($this->get_versions() as $version => $class) {
				$name = 'class-book-api-' . $version;
				$path = __DIR__ . '/' . $name . '.php';
				require_once $path;
			}

			add_action('init', array($this, 'add_endpoint'));
			add_action('wp', array($this, 'process_query'), -1);
		}

		/**
		 * Registers a new rewrite endpoint for accessing the API
		 *
		 * @author Mahdi Torkaman
		 * @param array $rewrite_rules WordPress Rewrite Rules
		 * @since 1.0.0
		 */
		public function add_endpoint($rewrite_rules)
		{
			add_rewrite_endpoint('metobg-api', EP_ALL);
		}
		/**
		 * Retrieve the API versions
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_versions()
		{
			return $this->versions;
		}

		/**
		 * Retrieve the API version that was queried
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_queried_version()
		{
			return $this->queried_version;
		}
		/**
		 * Registers query vars for API access
		 *
		 * @since 1.0.0
		 * @author Meahdi Torkaman
		 * @param array $vars Query vars
		 * @return string[] $vars New query vars
		 */
		public function query_vars($vars)
		{

			$vars[] = 'username';
			$vars[] = 'password';
			$vars[] = 'query';
			$vars[] = 'type';
			$vars[] = 'book';
			$vars[] = 'category';
			$vars[] = 'tag';
			$vars[] = 'number';
			$vars[] = 'format';
			$vars[] = 'id';
			$vars[] = 'info';

			return apply_filters("meto_bg_api_query_vars", $vars);
		}

		/**
		 * Sets the version of the API that was queried.
		 *
		 * Falls back to the default version if no version is specified
		 *
		 * @access private
		 * @since 1.0.0
		 */
		private function set_queried_version()
		{

			global $wp_query;

			$version = $wp_query->query_vars['metobg-api'];

			if (strpos($version, '/')) {

				$version = explode('/', $version);
				$version = strtolower($version[0]);

				$wp_query->query_vars['metobg-api'] = str_replace($version . '/', '', $wp_query->query_vars['metobg-api']);

				if (array_key_exists($version, $this->versions)) {

					$this->queried_version = $version;
				} else {

					$this->is_valid_request = false;
					$this->invalid_version();
				}
			}
			 else {

				$this->queried_version = $this->get_default_version();
			}
		}
		/**
		 * Displays an invalid version error if the version number passed isn't valid
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function invalid_version()
		{
			$error = array();
			$error['error'] = __('Invalid API version!', METO_BG_TEXT_DOMAIN);

			$this->data = $error;
			$this->output(404);
		}
		/**
		 * Listens for the API and then processes the API requests
		 *
		 * @global $wp_query
		 * @since 1.0.0
		 * @return void
		 */
		public function process_query()
		{
			global $wp_query;

			// Start logging how long the request takes for logging
			$before = microtime(true);

			// Check for metobg-api var. Get out if not present
			if (empty($wp_query->query_vars['metobg-api'])) {
				return;
			}

			// Determine which version was queried
			$this->set_queried_version();

			// Determine the kind of query
			$this->set_query_mode();

			// Check for a valid user and set errors if necessary
			$this->validate_request();

			// Only proceed if no errors have been noted
			if (!$this->is_valid_request) {
				exit;
			}

			$data = array();
			require_once __DIR__ . '/class-book-api-v1.php';
			require_once __DIR__ . '/class-book-api-v2.php';
			$this->routes = new $this->versions[$this->get_queried_version()];
			$this->routes->validate_request();

			switch ($this->endpoint):

				case 'books':

					$args = array(
						'book'       => isset($wp_query->query_vars['book'])       ? absint($wp_query->query_vars['book']) : null,
						'category'      => isset($wp_query->query_vars['category'])      ? $this->sanitize_request_term($wp_query->query_vars['category']) : null,
						'tag'           => isset($wp_query->query_vars['tag'])           ? $this->sanitize_request_term($wp_query->query_vars['tag']) : null,
						'order'         => isset($wp_query->query_vars['order'])         ? $wp_query->query_vars['order'] : 'DESC',
						'orderby'       => isset($wp_query->query_vars['orderby'])       ? $wp_query->query_vars['orderby'] : 'date',
					);

					$data = $this->routes->get_books($args);
					break;

				case 'info':

					$data = $this->routes->get_info();

					break;

			endswitch;

			// Allow other plugins to setup their own return data
			$this->data = apply_filters('meto_bg_api_output_data', $data, $this->endpoint, $this);

			$after                       = microtime(true);
			$request_time                = ($after - $before);
			$this->data['request_speed'] = $request_time;

			$this->log_request($this->data);

			// Send out data to the output function
			$this->output();
		}

		/**
		 * Retrieves the default version of the API to use
		 *
		 * @access private
		 * @since 1.0.0
		 * @return string
		 */
		public function get_default_version()
		{
			
			$version =get_option('meto_bg_select_version') ;
			if($version===false){
				$version='v1';
			} 
			return $version;
		}

		/**
		 * Determines the kind of query requested and also ensure it is a valid query
		 *
		 * @access private
		 * @since 1.0.0
		 * @global $wp_query
		 */
		public function set_query_mode()
		{

			global $wp_query;

			// Whitelist our query options
			$accepted = apply_filters('meto_bg_api_valid_query_modes', array('books', 'info'));

			$query = isset($wp_query->query_vars['metobg-api']) ? $wp_query->query_vars['metobg-api'] : null;
			$query = str_replace($this->queried_version . '/', '', $query);

			$error = array();

			// Make sure our query is valid
			if (!in_array($query, $accepted)) {
				$error['error'] = __('Invalid query!', METO_BG_TEXT_DOMAIN);

				$this->data = $error;
				// 400 is Bad Request
				$this->output(400);
			}

			$this->endpoint = $query;
		}

		/**
		 * Log each API request, if enabled
		 *
		 * @access private
		 * @since  1.0.0
		 * @param array $data
		 * @return void
		 */
		private function log_request($data = array())
		{
			#this is plugin test. You complete this feature
			do_action("meto_bg_api_log_request", $this->data);
		}

		/**
		 * Output Query in either JSON. The query data is outputted as JSON
		 * by default
		 *
		 * @author Mahdi Torkaman
		 * @since 1.0.0
		 * @param int $status_code
		 */
		public function output($status_code = 200)
		{
			$format = $this->get_output_format();

			status_header($status_code);

			do_action('meto_bg_api_output_before', $this->data, $this, $format);

			switch ($format):

				case 'json':

					header('Content-Type: application/json');
					if (!empty($this->pretty_print))
						echo json_encode($this->data, $this->pretty_print);
					else
						echo json_encode($this->data);

					break;
				default:

					// Allow other formats to be added via othere plugins
					do_action('meto_bg_api_output_' . $format, $this->data, $this);

					break;

			endswitch;

			do_action('meto_bg_api_after', $this->data, $this, $format);
			exit;
		}

		/**
		 * Retrieve the output format
		 *
		 * Determines whether results should be displayed in XML or JSON
		 *
		 * @since 1.0.0
		 *
		 * @return mixed|void
		 */
		public function get_output_format()
		{
			global $wp_query;

			$format = isset($wp_query->query_vars['format']) ? $wp_query->query_vars['format'] : 'json';

			return apply_filters('meto_bg_api_output_format', $format);
		}

		/**
		 * Validate the API request
		 * @access private
		 * @global object $wp_query WordPress Query
		 * @since 1.0.0
		 * @return bool
		 */
		private function validate_request()
		{
			global $wp_query;
			$valid = false;

			$this->override = false;
			$token=(isset($wp_query->query_vars['token']) && is_null($wp_query->query_vars['token']))?$wp_query->query_vars['token']:"";
			if(isset($_GET['token'])){
				$token=_sanitize_text_fields($_GET['token']);
			}
			$api=is_null($wp_query->query_vars['metobg-api'])?"":$wp_query->query_vars['metobg-api'];
			
			if (!empty($api) && (!empty($token))) {

				if (empty($token)) {
					$this->missing_auth();
					$valid =  false;
				}
				if (!($user = $this->get_user($token))) {
					$this->invalid_token();
					$valid = false;
				} else {
					$valid = true;
					$this->is_valid_request = true;
				}
			} elseif (!empty($wp_query->query_vars['metobg-api'])) {
				$this->is_valid_request = false;
				$this->missing_auth();
				$valid = false;
			}
			return $valid;
		}

		
		public function get_user($key = '')
		{

			if (empty($key)) {
				return false;
			}
			$user_info = explode('-', $key);
			if (isset($user_info[1]) && isset($user_info[0])) {
				$authenticate = wp_authenticate($user_info[0], $user_info[1]);
			} else {
				return false;
			}
			if (!($authenticate instanceof WP_User)) {
				return false;
			}
			$user = get_user_by('login', $user_info[0]);
			if ($user != NULL) {
				$this->user_id = $user;
				return $user;
			}

			return false;
		}

		/**
		 * Displays a missing authentication error if all the parameters aren't
		 * provided
		 *
		 * @access private
		 * @author Mehdi Torkaman
		 * @since 1.0.0
		 */
		private function missing_auth()
		{
			$error = array();
			$error['error'] = __('You must specify token', METO_BG_TEXT_DOMAIN);

			$this->data = $error;
			$this->output(401);
		}
		/**
		 * Displays an invalid API key error if the API key provided couldn't be
		 * validated
		 *
		 * @access private
		 * @author Mahdi Torkaman
		 * @since 1.0.0
		 * @return void
		 */
		private function invalid_token()
		{
			$error = array();
			$error['error'] = __('Invalid API token!', METO_BG_TEXT_DOMAIN);

			$this->data = $error;
			$this->output(403);
		}
		/**
		 * Displays an invalid API key error if the API key provided couldn't be
		 * validated
		 *
		 * @access private
		 * @author Mahdi Torkaman
		 * @since 1.0.0
		 * @return void
		 */
		private function invalid_per_page()
		{
			$error = array();
			$error['error'] = __('Invalid API Per Page Request!', METO_BG_TEXT_DOMAIN);

			$this->data = $error;
			$this->output(403);
		}
		/**
		 * Sanitizes category and tag terms
		 *
		 * @access private
		 * @since 1.0.0
		 * @param mixed $term Request variable
		 * @return mixed Sanitized term/s
		 */
		public function sanitize_request_term($term)
		{

			if (is_array($term)) {
				$term = array_map('sanitize_text_field', $term);
			} else if (is_int($term)) {
				$term = absint($term);
			} else {
				$term = sanitize_text_field($term);
			}

			return $term;
		}
		/**
		 * Get page number
		 *
		 * @access private
		 * @since 1.0.0
		 * @global $wp_query
		 * @return int $wp_query->query_vars['page'] if page number returned (default: 1)
		 */
		public function get_paged()
		{
			global $wp_query;

			return isset($wp_query->query_vars['page']) ? $wp_query->query_vars['page'] : 1;
		}
		/**
		 * Number of results to display per page
		 *
		 * @access private
		 * @since 1.0.0
		 * @global $wp_query
		 * @return int $per_page Results to display per page (default: 10)
		 */
		public function per_page()
		{
			global $wp_query;

			$per_page = isset($wp_query->query_vars['number']) ? $wp_query->query_vars['number'] : 20;

			if ($per_page < 0 || $per_page > 10000) {
				$this->invalid_per_page();
			}

			return apply_filters('edd_api_results_per_page', $per_page);
		}
		/**
		 * Process Get books API Request
		 *
		 * @author Mahdi Torkaman
		 * @since 1.0.0
		 * @return array $customers Multidimensional array of the books
		 */
		public function get_books($args = array())
		{

			$books = array();
			$error = array();

			if (empty($args['book'])) {

				$books['books'] = array();

				$parameters = array(
					'post_type'        => 'metobook',
					'posts_per_page'   => $this->per_page(),
					'suppress_filters' => true,
					'paged'            => $this->get_paged(),
				);

				if (isset($args['s']) && !empty($args['s'])) {
					$parameters['s'] = $args['s'];
				}

				$book_list = get_posts($parameters);

				if ($book_list) {
					$i = 0;
					foreach ($book_list as $book_info) {
						$books['books'][$i] = $this->get_book_data($book_info);
						$i++;
					}
				}
			} else {

				if (get_post_type($args['book']) == 'metobook') {
					$book_info = get_post($args['book']);

					$books['books'][0] = $this->get_book_data($book_info);
				} else {
					$error['error'] = sprintf(__('book %s not found!', METO_BG_TEXT_DOMAIN), $args['book']);
					return $error;
				}
			}

			return apply_filters('meto_bg_api_products', $books, $this);
		}

		/**
		 * Given a metobook post object, generate the data for the API output
		 *
		 * @since  1.0.0
		 * @param  object $book_info The Metobook Post Object
		 * @return array                Array of post data to return back in the API
		 */
		public function get_book_data($book_info)
		{
			$book = array();

			$book['info']['id']                           = $book_info->ID;
			$book['info']['slug']                         = $book_info->post_name;
			$book['info']['price']                        = $book_info->price;
			$book['info']['title']                        = $book_info->post_title;
			$book['info']['create_date']                  = $book_info->post_date;
			$book['info']['modified_date']                = $book_info->post_modified;
			$book['info']['status']                       = $book_info->post_status;
			$book['info']['link']                         = html_entity_decode($book_info->guid);
			$book['info']['content']                      = $book_info->post_content;
			$book['info']['excerpt']                      = $book_info->post_excerpt;
			$book['info']['thumbnail']                    = wp_get_attachment_url(get_post_thumbnail_id($book_info->ID));
			$book['info']['category']                     = get_the_terms($book_info, 'metobook_category');
			$book['info']['tags']                         = get_the_terms($book_info, 'metobook_tag');

			return apply_filters('meto_bg_api_books_data', $book);
		}
	}
}
