<?php

/**
 * Shortcodes
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

/**
 *  Shortcodes class.
 */
class Meto_Book_Shortcodes
{

    public static function init()
    {
        $shortcodes = array(
            'books' => __CLASS__ . '::books',
        );
        foreach ($shortcodes as $shortcode => $function) {
            $tag = "{$shortcode}_shortcode_tag";
            add_shortcode(apply_filters($tag, $shortcode), $function);
        }
    }

    public static function books($attributes)
    {
        require_once __DIR__ . '/shortcodes/class-meto-shortcode-books.php';
        $type = '';
        $attributes = empty($attributes) ? "list_books" : $attributes;

        switch ($attributes) {
            case "list_books":
                $type = "list_books";
                break;
            case @$attributes["list_books"]:
                $type = "list_books";
                break;
            default:
                $type = "list_books";
        }
        $shortcode = new Meto_Book_Shortcode_Books($attributes, $type);

        $shortcode->content();
    }
}
