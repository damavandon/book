<?php

defined( 'ABSPATH' ) || exit;

global $post;


if ( empty( $post )) {
	return;
}
?>
<li>
	<?php
	$link = get_the_permalink();

    echo '<a href="' . esc_url( $link ) . '" class="metobook">';

	echo get_the_post_thumbnail();

    echo '<h2 class="' . esc_attr( apply_filters( 'metobook_post_loop_title_classes', 'metobook-loop-post__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    $price_html =get_post_field('_meto_bg_bookprice');
	
    echo '<span class="price">'. $price_html.'</span>';
	?>
</li>
