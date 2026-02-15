<?php

/**
 * Extension Manager/Repository config file
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Greens: Contacts',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Greens\\Contacts\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'author' => 'Steffen Kroggel',
    'author_email' => 'developer@steffenkroggel.de',
    'author_company' => '',
    'version' => '1.0.0',
];
