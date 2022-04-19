<?php


/**
 * What type of request is this?
 *@since 1.0.0
 * @param  string $type admin, ajax, cron or frontend.
 * @return bool
 */
if (!function_exists("meto_bg_is_request")) {

    function meto_bg_is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !$this->is_rest_api_request();
        }
    }
}

/**
 * Get Default Labels
 *
 * @since 1.0.0
 * @return array $defaults Default labels
 */
function meto_bg_get_default_labels()
{
    $defaults = array(
        'singular' => __('Book', METO_BG_TEXT_DOMAIN),
        'plural'   => __('Books', METO_BG_TEXT_DOMAIN)
    );
    return apply_filters('meto_bg_register_book_taxonomy', $defaults);
}
/**
 * Get Plural Label
 *
 * @since 1.0.0
 * @return string $defaults['plural'] Plural label
 */
function meto_bg_get_label_plural($lowercase = false)
{
    $defaults = meto_bg_get_default_labels();
    return ($lowercase) ? strtolower($defaults['plural']) : $defaults['plural'];
}

/**
 * Get Singular Label
 *
 * @since 1.0.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function meto_bg_get_label_singular($lowercase = false)
{
    $defaults = meto_bg_get_default_labels();
    return ($lowercase) ? strtolower($defaults['singular']) : $defaults['singular'];
}
/**
 * Get the singular and plural labels for a book taxonomy
 *
 * @since  1.0.0
 * @param  string $taxonomy The Taxonomy to get labels for
 * @return array            Associative array of labels (name = plural)
 */
function meto_bg_get_taxonomy_labels( $taxonomy = 'metobook_category' ) {
	$allowed_taxonomies = apply_filters( 'meto_bg_taxonomies', array( 'metobook_category', 'metobook_tag' ) );

	if ( ! in_array( $taxonomy, $allowed_taxonomies ) ) {return false;}

	$labels   = array();
	$taxonomy = get_taxonomy( $taxonomy );

	if ( false !== $taxonomy ) {
        
		$singular  = $taxonomy->labels->singular_name;
		$name      = $taxonomy->labels->name;
		$menu_name = $taxonomy->labels->menu_name;

		$labels = array(
			'name'          => $name,
			'singular_name' => $singular,
			'menu_name'     => $menu_name,
		);
	}

	return apply_filters( 'meto_bg_taxonomy_labels', $labels, $taxonomy );
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.0.0
 * @param string|bool $string String to convert. If a bool is passed it will be returned as-is.
 * @return bool
 */
function meto_bg_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Sets up the meto_bg_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function meto_bg_setup_loop( $args = array() ) {
	$default_args = array(
		'loop'         => 0,
		'columns'      => apply_filters("meto_bg_columns_default",20),
		'name'         => '',
		'is_shortcode' => false,
		'is_paginated' => true,
		'is_search'    => false,
		'is_filtered'  => false,
		'total'        => 0,
		'total_pages'  => 0,
		'per_page'     => 0,
		'current_page' => 1,
	);
	// Merge any existing values.
	if ( isset( $GLOBALS['metobook_loop'] ) ) {
		$default_args = array_merge( $default_args, $GLOBALS['metobook_loop'] );
	}

	$GLOBALS['metobook_loop'] = wp_parse_args( $args, $default_args );
}

/**
	 * Output the start of a product loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function meto_bg_product_loop_start( $echo = true ) {
		ob_start();

		meto_bg_set_loop_prop( 'loop', 0 );

		wc_get_template( 'loop/loop-start.php' );

		$loop_start = apply_filters( 'meto_bg_book_loop_start', ob_get_clean() );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $loop_start;
		} else {
			return $loop_start;
		}
	}
	/**
 * Sets a property in the metobook_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function meto_bg_set_loop_prop( $prop, $value = '' ) {
	if ( ! isset( $GLOBALS['metobook_loop'] ) ) {
		meto_bg_setup_loop();
	}
	$GLOBALS['metobook_loop'][ $prop ] = $value;
}

/**
 * Gets a property from the metobook_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function meto_bg_loop_prop( $prop, $default = '' ) {
	return isset( $GLOBALS['metobook_loop'], $GLOBALS['metobook_loop'][ $prop ] ) ? $GLOBALS['metobook_loop'][ $prop ] : $default;
}

/**
	 * Output the end of a product loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function meto_bg_book_loop_end( $echo = true ) {
		ob_start();

		wc_get_template( 'loop/loop-end.php' );

		$loop_end = apply_filters( 'meto_bg_books_loop_end', ob_get_clean() );

		if ( $echo ) {
			echo $loop_end;
		} else {
			return $loop_end;
		}
	}
	/**
 * Resets the meto_bg_loop global.
 *
 * @since 1.0.0
 */
function meto_bg_reset_loop() {
	unset( $GLOBALS['metobook_loop'] );
}

/**
 * Get template part.
 *
 */
function meto_bg_get_template_part( $slug, $name = '' ) {
		if ( $name ) {
		
			if (!isset($template) ) {
				$fallback = METO_BG_TEMPLATE_DIR."{$slug}-{$name}.php";
				$template = file_exists( $fallback ) ? $fallback : '';
			}
		
	$template = apply_filters( 'meto_bg_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}
}

