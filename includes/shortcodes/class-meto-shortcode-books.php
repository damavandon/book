<?php

/**
 * books shortcode
 *
 * @version  1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

class Meto_Book_Shortcode_Books
{

	/**
	 * Shortcode type.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $type = 'books';

	/**
	 * Attributes.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $attributes = array();

	/**
	 * Query args.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $query_args = array();

	/**
	 * Set custom visibility.
	 *
	 * @since 1.0.0
	 * @var   bool
	 */
	/**
	 * Initialize shortcode.
	 *
	 * @since 3.2.0
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type       Shortcode type.
	 */
	public function __construct($attributes = array(), $type = 'books')
	{
		$this->type       = $type;
		$this->attributes = $this->parse_attributes($attributes);
		$this->query_args = $this->parse_query_args();
	}

	/**
	 * Get shortcode content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function content()
	{
		return $this->loop();
	}

	/**
	 * Get wrapper classes.
	 *
	 * @since  1.0.0
	 * @param  int $columns Number of columns.
	 * @return array
	 */
	protected function wrapper_classes($columns)
	{
		$classes = array('metobook');

		if ('product' !== $this->type) {
			$classes[] = 'columns-' . $columns;
		}

		$classes[] = $this->attributes['class'];

		return $classes;
	}
	protected function loop()
	{
		$columns  = absint($this->attributes['columns']);
		$classes  = $this->wrapper_classes($columns);
		$books    = $this->get_query_results();

		ob_start();

		if ($books && $books->ids) {
			if (is_callable('_prime_post_caches')) {
				_prime_post_caches($books->ids);
			}

			// Setup the loop.
			meto_bg_setup_loop(
				array(
					'columns'      => $columns,
					'name'         => $this->type,
					'is_shortcode' => true,
					'is_search'    => false,
					'is_paginated' => meto_bg_to_bool($this->attributes['paginate']),
					'total'        => $books->total,
					'total_pages'  => $books->total_pages,
					'per_page'     => $books->per_page,
					'current_page' => $books->current_page,
				)
			);

			$original_post = $GLOBALS['post'];

			do_action("meto_bg_shortcode_before_{$this->type}_loop", $this->attributes);

			// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
			if (meto_bg_to_bool($this->attributes['paginate'])) {
				do_action('meto_bg_before_shop_loop');
			}

			if (meto_bg_loop_prop('total')) {
				foreach ($books->ids as $product_id) {
					$GLOBALS['post'] = get_post($product_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata($GLOBALS['post']);

					meto_bg_get_template_part('content', 'metobook');
				}
			}

			$GLOBALS['post'] = $original_post; 
		

			wp_reset_postdata();
			meto_bg_reset_loop();
		}

		return '<div class="' . esc_attr(implode(' ', $classes)) . '">' ."". '</div>';
	}

	/**
	 * Run the query and return an array of data, including queried ids and pagination information.
	 *
	 * @since  1.0.0
	 */
	protected function get_query_results()
	{

		$query = new WP_Query($this->query_args);

		$paginated = !$query->get('no_found_rows');

		$results = (object) array(
			'ids'          => wp_parse_id_list($query->posts),
			'total'        => $paginated ? (int) $query->found_posts : count($query->posts),
			'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
			'per_page'     => (int) $query->get('posts_per_page'),
			'current_page' => $paginated ? (int) max(1, $query->get('paged', 1)) : 1,
		);

		return apply_filters('meto_bg_shortcode_books_query_results', $results, $this);
	}

	/**
	 * Parse attributes.
	 *
	 * @since  1.0.0
	 * @param  array $attributes Shortcode attributes.
	 * @return array
	 */
	protected function parse_attributes($attributes)
	{

		$attributes = $this->legacy_attributes($attributes);
		$attributes = shortcode_atts(
			array(
				'limit'          => '-1',
				'columns'        => '',
				'rows'           => '',
				'orderby'        => '',
				'ids'            => '',
				'category'       => '',
				'cat_operator'   => 'IN',
				'attribute'      => '',
				'tag'            => '',
				'tag_operator'   => 'IN',
				'class'          => '',
				'page'           => 1,
				'paginate'       => false,
				'cache'          => true,
			),
			$attributes,
			$this->type
		);

		if (!absint($attributes['columns'])) {
			$attributes['columns'] = apply_filters("meto_bg_books_shortcode_columns", 100);
		}

		return $attributes;
	}
	/**
	 * Parse legacy attributes.
	 *
	 * @since 1.0.0
	 * @param  array $attributes Attributes.
	 * @return array
	 */
	protected function legacy_attributes($attributes)
	{
		$mapping = array(
			'operator' => 'cat_operator',
			'per_page' => 'limit',
		);

		foreach ($mapping as $old => $new) {
			if (isset($attributes[$old])) {
				$attributes[$new] = $attributes[$old];
				unset($attributes[$old]);
			}
		}

		return $attributes;
	}

	/**
	 * Parse query args.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	protected function parse_query_args()
	{

		$query_args = array(
			'post_type'           => 'metobook',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false === meto_bg_to_bool($this->attributes['paginate']),
			'orderby'             => empty($_GET['orderby']) ? $this->attributes['orderby'] : sanitize_text_field(wp_unslash($_GET['orderby'])), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		);
	

		if (meto_bg_to_bool($this->attributes['paginate'])) {
			$this->attributes['page'] = absint(empty($_GET['book-page']) ? 1 : $_GET['book-page']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if (!empty($this->attributes['rows'])) {
			$this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
		}

		$query_args['posts_per_page'] = intval($this->attributes['limit']);
		if (1 < $this->attributes['page']) {
			$query_args['paged'] = absint($this->attributes['page']);
		}
		$query_args['tax_query']  = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query


		// IDs.
		$this->set_ids_query_args($query_args);

		// Set specific types query args.
		if (method_exists($this, "set_{$this->type}_query_args")) {
			$this->{"set_{$this->type}_query_args"}($query_args);
		}

		// Attributes.
		$this->set_attributes_query_args($query_args);

		// Categories.
		$this->set_categories_query_args($query_args);

		// Tags.
		$this->set_tags_query_args($query_args);

		$query_args = apply_filters('meto_bg_books_shortcode_query', $query_args, $this->attributes, $this->type);

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}
	/**
	 * Set ids query args.
	 *
	 * @since 1.0.0
	 * @param array $query_args Query args.
	 */
	protected function set_ids_query_args(&$query_args)
	{
		if (!empty($this->attributes['ids'])) {
			$ids = array_map('trim', explode(',', $this->attributes['ids']));

			if (1 === count($ids)) {
				$query_args['p'] = $ids[0];
			} else {
				$query_args['post__in'] = $ids;
			}
		}
	}
	/**
	 * Set categories query args.
	 *
	 * @since 1.0.0
	 * @param array $query_args Query args.
	 */
	protected function set_categories_query_args(&$query_args)
	{

		if (!empty($this->attributes['category'])) {
			$categories = array_map('sanitize_title', explode(',', $this->attributes['category']));
			$field      = 'slug';

			if (is_numeric($categories[0])) {
				$field      = 'term_id';
				$categories = array_map('absint', $categories);
				// Check numeric slugs.
				foreach ($categories as $cat) {
					$the_cat = get_term_by('slug', $cat, 'metobook_category');
					if (false !== $the_cat) {
						$categories[] = $the_cat->term_id;
					}
				}
			}

			$query_args['tax_query'][] = array(
				'taxonomy'         => 'metobook_category',
				'terms'            => $categories,
				'field'            => $field,
				'operator'         => $this->attributes['metobook_category'],

				/*
				 * When cat_operator is AND, the children categories should be excluded,
				 * as only books belonging to all the children categories would be selected.
				 */
				'include_children' => 'AND' === $this->attributes['cat_operator'] ? false : true,
			);
		}
	}

	/**
	 * Set attributes query args.
	 *
	 * @since 1.0.0
	 * @param array $query_args Query args.
	 */
	protected function set_attributes_query_args(&$query_args)
	{
		if (!empty($this->attributes['attribute']) || !empty($this->attributes['terms'])) {
			$taxonomy = strstr($this->attributes['attribute'], 'pa_') ? sanitize_title($this->attributes['attribute']) : 'pa_' . sanitize_title($this->attributes['attribute']);
			$terms    = $this->attributes['terms'] ? array_map('sanitize_title', explode(',', $this->attributes['terms'])) : array();
			$field    = 'slug';

			if ($terms && is_numeric($terms[0])) {
				$field = 'term_id';
				$terms = array_map('absint', $terms);
				// Check numeric slugs.
				foreach ($terms as $term) {
					$the_term = get_term_by('slug', $term, $taxonomy);
					if (false !== $the_term) {
						$terms[] = $the_term->term_id;
					}
				}
			}

			// If no terms were specified get all books that are in the attribute taxonomy.
			if (!$terms) {
				$terms = get_terms(
					array(
						'taxonomy' => $taxonomy,
						'fields'   => 'ids',
					)
				);
				$field = 'term_id';
			}

			// We always need to search based on the slug as well, this is to accommodate numeric slugs.
			$query_args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'terms'    => $terms,
				'field'    => $field,
				'operator' => $this->attributes['terms_operator'],
			);
		}
	}
	/**
	 * Set tags query args.
	 *
	 * @since 1.0.0
	 * @param array $query_args Query args.
	 */
	protected function set_tags_query_args(&$query_args)
	{
		if (!empty($this->attributes['tag'])) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'metobook_category',
				'terms'    => array_map('sanitize_title', explode(',', $this->attributes['tag'])),
				'field'    => 'slug',
				'operator' => $this->attributes['tag_operator'],
			);
		}
	}
}
