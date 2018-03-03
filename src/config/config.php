<?php

return [
    //default language
    'def_locale' => 'ru',

    //other language
    'alt_langs'  => ['Рус'=>'ru', 'Укр'=>'ua', 'Eng'=>'en'],
    'title_page' => 'Переводы',
    'show_count' => ['20', '40', '60', '100'],

    'languages' => [
        [
            'caption'     => 'ru',
            'postfix'     => '',
            'placeholder' => 'Текст на русском',
        ],
        [
            'caption'     => 'ua',
            'postfix'     => '_ua',
            'placeholder' => 'Текст на украинском',
        ],

        [
            'caption'     => 'en',
            'postfix'     => '_en',
            'placeholder' => 'Текст на английском',
        ],
    ],
];
