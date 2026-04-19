<?php

/**
 * Extension Manager/Repository config file
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Package Politik & Form',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Madj2k\\SitePolitikUndForm\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'author' => 'Steffen Kroggel',
    'author_email' => 'developer@steffenkroggel.de',
    'author_company' => '',
    'version' => '1.0.0',
];
