<?php
  $fields =apply_filters("meto_bg_book_fields",[
    'book' => [
        'title' =>  __("Book Information", METO_BG_TEXT_DOMAIN),
        'slug' => 'meto_bg_book',
        'fields' => [
            [
                'name' => 'name',
                'type' => 'text',
                'att' => [
                    'title' => __('Name', METO_BG_TEXT_DOMAIN),
                ],
            ],
            [
                'name' => 'genres',
                'type' => 'text',
                'att' => [
                    'title' => __('Genres', METO_BG_TEXT_DOMAIN),
                ],
            ],
            [
                'name' => 'price',
                'type' => 'text',
                'att' => [
                    'title' => __('Price', METO_BG_TEXT_DOMAIN),
                ],
            ],
        ],
    ],
    'author' => [
        'title' => __("Author Information", METO_BG_TEXT_DOMAIN),
        'slug' => 'meto_bg_author',
        'fields' => [
            [
                'name' => 'first_name',
                'type' => 'text',
                'att' => [
                    'title' => __('First name', METO_BG_TEXT_DOMAIN),
                ],
            ],
            [
                'name' => 'last_name',
                'type' => 'text',
                'att' => [
                    'title' => __('Last name', METO_BG_TEXT_DOMAIN),
                ],
            ],
        ],

    ],
    'publisher' => [
        'title' =>  __("Publisher", METO_BG_TEXT_DOMAIN),
        'slug' => 'meto_bg_publisher',
        'fields' => [
            [
                'name' => 'name',
                'type' => 'text',
                'att' => [
                    'title' => __('Name', METO_BG_TEXT_DOMAIN),
                ]
            ],
            [
                'name' => 'place_publication',
                'type' => 'text',
                'att' => [
                    'title' => __('Place publication', METO_BG_TEXT_DOMAIN),
                ]
            ],
            [
                'name' => 'date_publication',
                'type' => 'date',
                'att' => [
                    'title' => __('Date publication', METO_BG_TEXT_DOMAIN),
                ]
            ],
        ],
    ],
]);
   