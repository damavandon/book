<?php

/**
 * Gallery setup
 *
 * @package Torkaman
 * @since   1.0.0
 */

/**
 * Main Gallery Class.
 *

 */
if (!defined('ABSPATH')) {
	exit;
}


final class Meto_Book_Gallery
{

	/**
	 * Gallery version.
	 *
	 * @var string
	 */
	public $version = METO_BG_VER;

	public $api;
	/**
	 * The single instance of the class.
	 *
	 * @var Meto_Book_Gallery
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Meto_Book_Gallery Instance.
	 *
	 * Ensures only one instance of Meto_Book_Gallery is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see meto_book_gallery()
	 * @return Meto_Book_Gallery - Main instance.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		wc_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', METO_BG_TEXT_DOMAIN), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		wc_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', METO_BG_TEXT_DOMAIN), '1.0.0');
	}

	public function __construct()
	{
		$this->includes();
		$this->init_class();
		$this->init_hooks();
	}

	private function includes()
	{
		require_once METO_BG_INC_DIR . 'book/interface-book.php';
		require_once METO_BG_INC_DIR . 'book/interface-db-book.php';
		require_once METO_BG_INC_DIR . 'book/class-abstract-book.php';
		require_once METO_BG_INC_DIR . 'book/class-book-db.php';
		require_once METO_BG_INC_DIR . 'book/class-book.php';
		require_once METO_BG_INC_DIR . 'book/class-factory-book.php';
		require_once METO_BG_INC_DIR . 'book/class-order.php';
		require_once METO_BG_INC_DIR . 'class-book-shortcodes.php';
		require_once METO_BG_INC_DIR . 'api/endpoint.php';
		
		
		if (is_admin()) {
			require_once METO_BG_INC_DIR . 'admin/class-book-admin.php';
		}
		require_once METO_BG_INC_DIR . 'class-widgets.php';

	}
	private function init_class()
	{
		$this->api=new Meto_Book_End_Point();
	}
	private function init_hooks(){

		add_action( 'init', array( 'Meto_Book_Shortcodes', 'init' ) );
		add_filter("template_include",array($this,'single_template'),10.1);
		add_action( 'pre_get_posts',array($this,'add_my_post_types_to_query')  );
	}
	public function single_template($single_template){

		global $post;
		if(!is_null($post)){
			if ( 'metobook' === $post->post_type ) {
				$single_template = METO_BG_TEMPLATE_DIR . 'single-metobook.php';
			}
		}
	
		return $single_template;
	}
	function add_my_post_types_to_query( $query ) {
		if ( is_home() && $query->is_main_query() )
			$query->set( 'post_type', array( 'post', 'metobook' ) );
		return $query;
	}
}
