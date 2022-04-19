<?php
// don't call the file directly
if (!defined('ABSPATH')) {

    die('direct access abort ');
}

/**
 * Setup plugin constants.
 *
 * @since 1.0.0
 * @return void
 */
if(!function_exists("meto_setup_constants") ){
    function meto_setup_constants()
    {
        do_action("meto_bg_before_define_constants");
    
        $constants = [
            ['name' => 'METO_BG_DIR', 'value' => __DIR__],
            ['name' => 'METO_BG_INC_DIR', 'value' => __DIR__ . '/includes/'],
            ['name' => 'METO_BG_TEMPLATE_DIR', 'value' => __DIR__ . '/templates/'],
            ['name' => 'METO_BG_URL', 'value' => plugin_dir_url(__FILE__)],
            ['name' => 'METO_BG_VER', 'value' => '1.0.0'],
            ['name' => 'METO_BG_TEXT_DOMAIN', 'value' => 'meto-bg'],
            ['name' => 'METO_BG_REQUIRED_WP_VERSION', 'value' => '5.7'],
        ];
        foreach($constants as $constant ){
            if(!defined($constant['name'])){
                define($constant['name'],$constant['value']);
            }
        }
        do_action("meto_bg_after_define_constants");
    }
    
}
