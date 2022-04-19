<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 * The base version API class
 *
 * @since  1.0.0
 */
class Meto_Book_API_V2 extends Meto_Book_API_V1 {

	/**
	 * Process Get Books API Request
	 *
	 * @since 1.0.0
	 * @param array $args Query arguments
	 * @return array $customers Multidimensional array of the books
	 */
	public function get_books( $args = array() ) {

		$books = array();
		$error    = array();

		if ( empty( $args['book'] ) ) {

			$books['books'] = array();

			$query_args = array(
				'post_type'        => 'metobook',
				'posts_per_page'   => $this->per_page(),
				'suppress_filters' => true,
				'paged'            => $this->get_paged(),
				'order'            => $args['order'],
			);

			if( ! empty( $args['s'] ) ) {
				$query_args['s'] = sanitize_text_field( $args['s'] );
			}

			switch ( $query_args['orderby'] ) {
				case 'price':
					$query_args['meta_key'] = '_meto_bg_bookprice';
					$query_args['orderby']  = 'meta_value_num';
					break;

			}

			if( ! empty( $args['category'] ) ) {
				if ( is_string( $args[ 'categrory' ] ) ) {
					$args['category'] = explode( ',', $args['category'] );
				}

				if ( is_numeric( $args['category'] ) ) {
					$query_args['tax_query'] = array(
						array(
							'taxonomy' => 'metobook_category',
							'field'    => 'ID',
							'terms'    => (int) $args['category']
						),
					);
				} else if ( is_array( $args['category'] ) ) {

					foreach ( $args['category'] as $category ) {


						$field = is_numeric( $category ) ? 'ID': 'slug';

						$query_args['tax_query'][] = array(
							'taxonomy' => 'metobook_category',
							'field'    => $field,
							'terms'    => $category,
						);

					}

				} else {
					$query_args['metobook_category'] = $args['category'];
				}
			}

			if( ! empty( $args['tag'] ) ) {
				if ( strpos( $args['tag'], ',' ) ) {
					$args['tag'] = explode( ',', $args['tag'] );
				}

				if ( is_numeric( $args['tag'] ) ) {
					$query_args['tax_query'] = array(
						array(
							'taxonomy' => 'metobook_tag',
							'field'    => 'ID',
							'terms'    => (int) $args['tag']
						),
					);
				} else if ( is_array( $args['tag'] ) ) {

					foreach ( $args['tag'] as $tag ) {


						$field = is_numeric( $tag ) ? 'ID': 'slug';

						$query_args['tax_query'][] = array(
							'taxonomy' => 'metobook_tag',
							'field'    => $field,
							'terms'    => $tag,
						);

					}

				} else {
					$query_args['metobook_tag'] = $args['tag'];
				}
			}

			if ( ! empty( $query_args['tax_query'] ) ) {

				$relation = ! empty( $args['term_relation'] ) ? sanitize_text_field( $args['term_relation'] ) : 'OR';
				$query_args['tax_query']['relation'] = $relation;

			}

			$book_list = get_posts( $query_args );

			if ( $book_list ) {
				$i = 0;
				foreach ( $book_list as $book_info ) {
					$books['books'][$i] = $this->get_book_data( $book_info );
					$i++;
				}
			}

		} else {

			if ( get_post_type( $args['book'] ) == 'metobook' ) {
				$book_info = get_post( $args['book'] );

				$books['books'][0] = $this->get_book_data( $book_info );

			} else {
				$error['error'] = sprintf( __( 'Book %s not found!', METO_BG_TEXT_DOMAIN ), $args['book'] );
				return $error;
			}
		}

		return apply_filters( 'meto_bg_api_books', $books );
	}

	/**
	 * Given a metobook post object, generate the data for the API output
	 *
	 * @since  1.0.0
	 * @param  object $book_info The Metobook Post Object
	 * @return array                Array of post data to return back in the API
	 */
	public function get_book_data( $book_info ) {

		// Use the parent's get_book_data to reduce code duplication
		$book = parent::get_book_data( $book_info );
		return apply_filters( 'meto_bg_api_books_book_v2', $book );

	}

	

}
