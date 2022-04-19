<?php
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

if (!class_exists("Meto_BG_Settings")) {

    class Meto_BG_Settings
    {

        public static function init()
        {

            add_action("after_setup_theme", [__CLASS__, 'settings']);
        }
        public static function settings()
        {

            Container::make('theme_options', __('Settings', METO_BG_TEXT_DOMAIN))->set_page_parent('edit.php?post_type=metobook')
                ->add_fields(array(
                    Field::make('select', 'meto_bg_select_version', __('Choose REST API', METO_BG_TEXT_DOMAIN))
                        ->set_options(array(
                            'v1' => __('Version 1', METO_BG_TEXT_DOMAIN),
                            'v2' => __('Version 2', METO_BG_TEXT_DOMAIN),
                        )),
                ));
        }
    }
}

Meto_BG_Settings::init();
