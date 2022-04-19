<?php

if (!defined('ABSPATH')) {
	exit;
}


if (!class_exists("Meto_BG_Meta_Box")) {

	class Meto_BG_Meta_Box
	{
		public static function init()
		{

			add_action("after_setup_theme", [__CLASS__, 'register_meta_box']);
		}

		public static function register_meta_box()
		{
			Carbon_Fields\Carbon_Fields::boot();
			do_action('"meto_bg_meta_box_before_book');
			Meto_Book::register_fields();
			Meto_Book_Order::register_fields();
			do_action('"meto_bg_meta_box_after_book');
		}
	}
}

Meto_BG_Meta_Box::init();
