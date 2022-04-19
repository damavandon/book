<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists("Meto_BG_Admin")) {
    class Meto_BG_Admin
    {
        public function __construct()
        {
            add_action( 'admin_init', array( $this, 'buffer' ), 1 );
            $this->includes();
        }

        public function buffer(){
            ob_start();
        }

        public function includes(){

            if(!meto_bg_is_request("ajax")){
				require_once METO_BG_INC_DIR.'admin/class-meto-post-type.php';
                require_once METO_BG_INC_DIR.'admin/meta-boxes/class-book-meta-box-data.php';
                require_once METO_BG_INC_DIR.'admin/class-meto-settings.php';
			}
        }
    
    }
}
new Meto_BG_Admin();
