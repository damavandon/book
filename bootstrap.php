<?php

/**
 * @package   Torkaman
 * @link     https://trkm.kdevs.org/
 *
 * Plugin Name:      Books Gallery      
 * Plugin URI:       https://trkm.kdevs.org/
 * Description:      This is a Test plugin 
 * Version:          1.0.0
 * Author:           Mehdi torkaman
 * Author URI:       https://trkm.kdevs.org/
 * Text Domain:      meto-bg
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt  
 * Domain Path:      /languages
 * Requires PHP: 7.0
 */
defined('ABSPATH') || exit;

if (!defined('METO_BG_PLUGIN_FILE')) {
   define('METO_BG_PLUGIN_FILE', plugin_dir_path(__FILE__));
}

require_once __DIR__ .'/Define-constants.php';
require_once __DIR__ .'/vendor/autoload.php';

/**
 * Returns the main instance of Meto_Book_Gallery.
 *
 * @since  1.0.0
 * @return Meto_Book_Gallery
 */

function METOBG()
{
   if (!class_exists("Meto_Book_Gallery", false)) {

      meto_setup_constants();

      require_once __DIR__ . '/includes/class-book-gallery.php';
      require_once __DIR__.'/includes/helper-functions.php';
      Meto_Book_Gallery::instance();
   }

}

METOBG();
