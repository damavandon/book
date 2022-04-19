<?php

/**
 * Product Details Widget.
 *
 * Displays a product's details in a widget.
 *
 * @since 1.0.0
 * @return void
 */
class Meto_Book_Number_Of_Books_Widget extends WP_Widget
{

	/** Constructor */
	public function __construct()
	{
		parent::__construct(
			'metobook_number_of_books',
			sprintf(__('Number of %s', METO_BG_TEXT_DOMAIN), meto_bg_get_label_plural()),
			array(
				'description' => sprintf(esc_html__('Display Number of %s', METO_BG_TEXT_DOMAIN), 'Books'),
			)
		);
	}

	/** @see WP_Widget::widget */
	public function widget($args, $instance)
	{
		// before widget arguments are defined by themes
		echo $args['before_widget'];
		#-----------------------------------------
		echo "<h2>";echo $instance['title'];echo "</h2>"; 
		echo Meto_Book_DB::number_of_books();
		#-----------------------------------------
		// after widget arguments are defined by themes
		echo $args['after_widget'];
	}


	/** @see WP_Widget::form */
	public function form($instance)
	{
		// Set up some default widget settings.
		$defaults = array(
			'title'           => sprintf(__('Number of %s', METO_BG_TEXT_DOMAIN), meto_bg_get_label_plural()));

		$instance = wp_parse_args((array) $instance, $defaults); ?>

		<!-- Title -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', METO_BG_TEXT_DOMAIN) ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>

		<?php do_action('meto_bg_number_of_books', $instance); ?>
<?php }

	/** @see WP_Widget::update */
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title']           = strip_tags($new_instance['title']);
		$instance['categories']      = isset($new_instance['categories']) ? $new_instance['categories'] : '';

		do_action('meto_bg_number_of_widget_update', $instance);
		return $instance;
	}
}

/**
 * Register Widgets.
 *
 * Registers the MetoBook Widgets.
 *
 * @since 1.0
 * @return void
 */
function meto_book_register_widget()
{

	register_widget('Meto_Book_Number_Of_Books_Widget');
}
add_action('widgets_init', 'meto_book_register_widget');
