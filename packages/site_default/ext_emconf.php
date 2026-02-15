<?php

/**
 * Extension Manager/Repository config file
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Default Site-Package',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'fluid_styled_content' => '13.4.0-13.4.99',
            'rte_ckeditor' => '13.4.0-13.4.99',
            'content_blocks' => '1.3.15-1.3.99',
            'media_utils' => '13.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Madj2k\\SiteDefault\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'author' => 'Steffen Kroggel',
    'author_email' => 'developer@steffenkroggel.de',
    'author_company' => '',
    'version' => '1.0.0',
];
