<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("Meto_BG_Post_Type")) {

    class Meto_BG_Post_Type
    {
        public static function init()
        {

            add_action('init', array(__CLASS__, 'register_posttype'));
            add_action('init', array(__CLASS__, 'register_taxonomies'));

            add_action('post_updated_messages', array(__CLASS__, 'book_updated_messages'), 10, 1);
        }
        /**
         * Register book post type.
         * @since 1.0.0
         * @return void
         */
        public static function register_posttype()
        {
            if (!is_blog_installed() || post_type_exists('metobook')) {
                return;
            }
            do_action('meto_bg_before_register_post_type');
            Meto_Book::register_post_type();
            Meto_Book_Order::register_post_type();
            do_action('meto_bg_after_register_post_type');
        }
        /**
         * Book update messages.
         *
         * @since  1.0.0
         *
         * @param  array $messages Existing post update messages.
         *
         * @return array           Amended post update messages with new CPT update messages.
         */
        public static function book_updated_messages($messages)
        {
            $post             = get_post();
            $post_type        = get_post_type($post);
            $post_type_object = get_post_type_object($post_type);

            if ('metobook' !== $post_type) {
                return $messages;
            }

            $messages[$post_type] = apply_filters("meto_bg_post_type_messages", array(
                0  => '', // Unused. Messages start at index 1.
                1  => __('Book updated.', METO_BG_TEXT_DOMAIN),
                4  => __('Book updated.', METO_BG_TEXT_DOMAIN),
                /* translators: %s: date and time of the revision */
                5  => isset($_GET['revision']) ? sprintf(__('Book restored to revision from %s', METO_BG_TEXT_DOMAIN), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6  => __('Book published.', METO_BG_TEXT_DOMAIN),
                7  => __('Book saved.', METO_BG_TEXT_DOMAIN),
                8  => __('Book submitted.', METO_BG_TEXT_DOMAIN),
                9  => sprintf(
                    __('Book scheduled for: <strong>%1$s</strong>.', METO_BG_TEXT_DOMAIN),
                    // translators: Publish box date format, see http://php.net/date
                    date_i18n(__('M j, Y @ G:i', METO_BG_TEXT_DOMAIN), strtotime($post->post_date))
                ),
                10 => __('Book draft updated.', METO_BG_TEXT_DOMAIN)
            ));

            if ($post_type_object->publicly_queryable) {
                $permalink = get_permalink($post->ID);

                $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View Book', METO_BG_TEXT_DOMAIN));
                $messages[$post_type][1] .= $view_link;
                $messages[$post_type][6] .= $view_link;
                $messages[$post_type][9] .= $view_link;

                $preview_permalink = add_query_arg('preview', 'true', $permalink);
                $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview Book', METO_BG_TEXT_DOMAIN));
                $messages[$post_type][8]  .= $preview_link;
                $messages[$post_type][10] .= $preview_link;
            }

            return $messages;
        }

        public static  function register_taxonomies()
        {
            $slug     = 'metobook';
            /** Categories */
            $category_labels = array(
                'name'              => sprintf(_x('%s Categories', 'taxonomy general name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'singular_name'     => sprintf(_x('%s Category', 'taxonomy singular name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'search_items'      => sprintf(__('Search %s Categories', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'all_items'         => sprintf(__('All %s Categories', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'parent_item'       => sprintf(__('Parent %s Category', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'parent_item_colon' => sprintf(__('Parent %s Category:', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'edit_item'         => sprintf(__('Edit %s Category', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'update_item'       => sprintf(__('Update %s Category', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'add_new_item'      => sprintf(__('Add New %s Category', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'new_item_name'     => sprintf(__('New %s Category Name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'menu_name'         => __('Categories', METO_BG_TEXT_DOMAIN),
            );

            $category_args = apply_filters(
                'meto_book_category_args',
                array(
                    'hierarchical' => true,
                    'labels'       => apply_filters('meto_bg_category_labels', $category_labels),
                    'show_ui'      => true,
                    'query_var'    => 'metobook_category',
                    'rewrite'      => array('slug' => $slug . '/category', 'with_front' => false, 'hierarchical' => true),
                )
            );
             register_taxonomy('metobook_category', array('book'), $category_args);
             register_taxonomy_for_object_type('metobook_category', 'metobook');

            /** Tags */
            $tag_labels = array(
                'name'                  => sprintf(_x('%s Tags', 'taxonomy general name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'singular_name'         => sprintf(_x('%s Tag', 'taxonomy singular name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'search_items'          => sprintf(__('Search %s Tags', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'all_items'             => sprintf(__('All %s Tags', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'parent_item'           => sprintf(__('Parent %s Tag', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'parent_item_colon'     => sprintf(__('Parent %s Tag:', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'edit_item'             => sprintf(__('Edit %s Tag', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'update_item'           => sprintf(__('Update %s Tag', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'add_new_item'          => sprintf(__('Add New %s Tag', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'new_item_name'         => sprintf(__('New %s Tag Name', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
                'menu_name'             => __('Tags', METO_BG_TEXT_DOMAIN),
                'choose_from_most_used' => sprintf(__('Choose from most used %s tags', METO_BG_TEXT_DOMAIN), meto_bg_get_label_singular()),
            );

            $tag_args = apply_filters(
                'meto_bg_tag_args',
                array(
                    'hierarchical' => false,
                    'labels'       => apply_filters('meto_bg_tag_labels', $tag_labels),
                    'show_ui'      => true,
                    'query_var'    => 'metobook_tag',
                    'rewrite'      => array('slug' => $slug . '/tag', 'with_front' => false, 'hierarchical' => true),
                )
            );
            register_taxonomy('metobook_tag', array('metobook'), $tag_args);
            register_taxonomy_for_object_type('metobook_tag', 'metobook');
        }

 
    }
}

Meto_BG_Post_Type::init();
