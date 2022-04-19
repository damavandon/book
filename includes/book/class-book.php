<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;


class Meto_Book extends Meto_Book_Abstract
{
    private $id;
    private $db;
    private $name;
    private $title;
    private $genres;
    private $price;
    private $author_first_name;
    private $author_last_name;
    private $publisher_name;
    private $publishe_date;
    private $publishe_place;

    public function DependencyInjection()
    {
        $this->db = new Meto_Book_DB();
    }
    public function include()
    {
    }
    public function save()
    {
    }

    public static function register_post_type()
    {
        // Set UI labels for book Post Type
        $labels = apply_filters("'meto_bg_register_post_type_book_labels", array(
            'name'                => _x('Books', 'book General Name', METO_BG_TEXT_DOMAIN),
            'singular_name'       => _x('Book', 'book Singular Name', METO_BG_TEXT_DOMAIN),
            'menu_name'           => __('Books', METO_BG_TEXT_DOMAIN),
            'parent_item_colon'   => __('Parent Book', METO_BG_TEXT_DOMAIN),
            'all_items'           => __('All Books', METO_BG_TEXT_DOMAIN),
            'view_item'           => __('View Book', METO_BG_TEXT_DOMAIN),
            'add_new_item'        => __('Add New Book', METO_BG_TEXT_DOMAIN),
            'add_new'             => __('Add New', METO_BG_TEXT_DOMAIN),
            'edit_item'           => __('Edit Book', METO_BG_TEXT_DOMAIN),
            'update_item'         => __('Update Book', METO_BG_TEXT_DOMAIN),
            'search_items'        => __('Search Book', METO_BG_TEXT_DOMAIN),
            'not_found'           => __('Not Found', METO_BG_TEXT_DOMAIN),
            'not_found_in_trash'  => __('Not found in Trash', METO_BG_TEXT_DOMAIN),
        ));
        // Set other options for book Post Type
        $args = apply_filters("meto_bg_register_post_type_book_args", array(
            'label'               => __('Books', METO_BG_TEXT_DOMAIN),
            'description'         => __('Book ', METO_BG_TEXT_DOMAIN),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon' => 'dashicons-book',
            'show_in_nav_menus'   => true,
            'query_var'           => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,
        ));
        // Registering your Custom Post Type
        register_post_type('metobook', $args);
    }
    public static function register_fields()
    {
        $fields = self::fields();

        foreach ($fields as $field) {
            if (!isset($field['title'])) {
                continue;
            }
            $Container = Container::make('post_meta', $field['title'])->where('post_type', '=', 'metobook');

            $carbon_fields = [];
            if (!isset($field['fields'])) {
                continue;
            }
            foreach ($field['fields'] as $item) {
                array_push($carbon_fields, Field::make($item['type'], $field['slug'] . $item['name'], $item['att']['title']));
            }
            $Container->add_fields($carbon_fields);
        }
    }
    public static function  fields()
    {
        include_once __DIR__ . '/book-fields.php';
        return $fields;
    }
    public  function  get_name()
    {
        return $this->name;
    }
    public  function  get_title()
    {
        return $this->title;
    }
    public  function  get_genres()
    {
        return $this->genres;
    }
    public  function  get_price()
    {
        return $this->price;
    }
    public  function  get_author_first_name()
    {
        return $this->author_first_name;
    }
    public  function  get_author_last_name()
    {
        return $this->author_last_name;
    }
    public  function  get_publisher_name()
    {
        return $this->publisher_name;
    }
    public  function  get_publishe_date()
    {
        return $this->publishe_date;
    }
    public  function  get_publishe_place()
    {
        return $this->publishe_place;
    }

    public  function  set_name(string $name)
    {
        $this->name = $name;
    }
    public  function  set_title(string $title)
    {
        $this->title = $title;
    }
    public  function  set_genres(string $genres)
    {
        $this->genres = $genres;
    }
    public  function  set_price(string $price)
    {
        $this->price = $price;
    }
    public  function  set_author_first_name(string $author_first_name)
    {
        $this->author_first_name = $author_first_name;
    }
    public  function  set_author_last_name(string $author_last_name)
    {
        $this->author_last_name = $author_last_name;
    }
    public  function  set_publisher_name(string $publisher_name)
    {
        $this->publisher_name = $publisher_name;
    }
    public  function  set_publishe_date(string $publishe_date)
    {
        $this->publishe_date = $publishe_date;
    }
    public  function  set_publishe_place(string $publishe_place)
    {
        $this->publishe_place = $publishe_place;
    }

    public  function get_custom_property($custom_name)
    {
    }
    public  function set_custom_property($custom_name)
    {
    }
}
