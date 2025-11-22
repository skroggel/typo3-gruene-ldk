<?php
defined('TYPO3') or die('Access denied.');
call_user_func(
	function($extKey)
	{

		$GLOBALS['TCA']['pages']['columns']['categories']['config']['behaviour']['allowLanguageSynchronization']= true;

        // set title-field with editor
        $GLOBALS['TCA']['pages']['columns']['nav_title']['config'] = [
            'type' => 'text',
            'cols' => 80,
            'rows' => 15,
            'softref' => 'typolink_tag,email[subst],url',
            'enableRichtext' => true,
            'richtextConfiguration' => 'ShyOnly'
        ] ;

        $colors = ['orange', 'cyan', 'dark-green', 'green', 'grey', 'yellow'];
        $colorItems = [
            [
                'label' => '---',
                'value' => ''
            ]
        ];
        foreach ($colors as $color) {
            $colorItems[] = [
                'label' => $color,
                'value' => $color
            ];
        }

        //===========================================================================
		// Add fields
		//===========================================================================
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',
			[
				'tx_sitedefault_icon_class' => [
					'exclude' => true,
					'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_icon_class',
					'config' => [
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim',
						'behaviour' => [
							'allowLanguageSynchronization' => false
						]
					],
				],
                'tx_sitedefault_icon' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_icon',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'fileFolderConfig' => [
                            'folder' => 'EXT:site_default/Resources/Public/Icons/Selectable',
                            'allowedExtensions' => 'svg,png,jpg',
                            'depth' => 1,
                        ],
                        'size' => 1,
                        'maxitems' => 1,
                        'items' => [
                            [
                                'label' => '---',
                                'value' => ''
                            ]
                        ],
                        'default' => '',
                    ],
                ],
                'tx_sitedefault_color' => [
                    'exclude' => true,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_color',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => $colorItems,
                        'size' => 1,
                        'maxitems' => 1,
                    ],
                ],
                'tx_sitedefault_subline' => [
                    'exclude' => 0,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_subline',
                    'config' => [
                        'type' => 'input',
                        'size' => 30,
                        'eval' => 'trim',
                        'behaviour' => [
                            'allowLanguageSynchronization' => true
                        ]
                    ],
                ],
                'tx_sitedefault_image_teaser' => [
                    'exclude' => 0,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_image_teaser',
                    'config' => [
                        'type' => 'file',
                        'minitems' => 0,
                        'maxitems' => 1,
                        'allowed' => ['jpeg','jpg','png','gif','svg','webp'],
                        'overrideChildTca' => [
                            'columns' => [
                                'crop' => [
                                    'config' => [
                                        'cropVariants' => [
                                            'Preview' => [
                                                'title' => 'Preview',
                                                'allowedAspectRatios' => [
                                                    'portrait' => [
                                                        'title' => 'Preview',
                                                        'value' => 316 / 364
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'tx_sitedefault_image_flyout' => [
                    'exclude' => 0,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_image_flyout',
                    'config' => [
                        'type' => 'file',
                        'minitems' => 0,
                        'maxitems' => 1,
                        'allowed' => ['jpeg','jpg','png','gif','svg','webp'],
                        'overrideChildTca' => [
                            'columns' => [
                                'crop' => [
                                    'config' => [
                                        'cropVariants' => [
                                            'Preview' => [
                                                'title' => 'Flyout',
                                                'allowedAspectRatios' => [
                                                    'portrait' => [
                                                        'title' => 'Flyout',
                                                        'value' => 405 / 192
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'tx_sitedefault_headline_flyout' => [
                    'exclude' => 0,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_headline_flyout',
                    'config' => [
                        'type' => 'input',
                        'size' => 30,
                        'eval' => 'trim',
                        'behaviour' => [
                            'allowLanguageSynchronization' => true
                        ]
                    ],
                ],
                'tx_sitedefault_label_flyout' => [
                    'exclude' => 0,
                    'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.tx_sitedefault_label_flyout',
                    'config' => [
                        'type' => 'input',
                        'size' => 30,
                        'eval' => 'trim',
                        'behaviour' => [
                            'allowLanguageSynchronization' => true
                        ]
                    ],
                ],
            ]
		);

        // remove abstract in order to place it somewhere else
        $searchStrings = [
            '--palette--;;abstract,',
        ];
        foreach ($searchStrings as $searchString) {
            foreach ($GLOBALS['TCA']['pages']['types'] as $type => $array) {
                $GLOBALS['TCA']['pages']['types'][$type]['showitem'] = str_replace($searchString, '', ($GLOBALS['TCA']['pages']['types'][$type]['showitem']));
            }
        }


        //  add icon_class AND an empty palette for the flyout
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
			'pages',
			'tx_sitedefault_icon, tx_sitedefault_color, --palette--;LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:pages.palettes.flyout;flyout',
			'',
			'after:layout'
		);

        // add fields to new palette
        /*
        $GLOBALS['TCA']['pages']['palettes']['flyout'] = [
            'showitem' => 'tx_sitedefault_headline_flyout, tx_sitedefault_label_flyout, --linebreak--, tx_sitedefault_image_flyout'
        ];
        */

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'pages',
            '--palette--;;abstract',
            '',
            'after:title'
        );

        /*
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'pages',
            'tx_sitedefault_subline',
            '',
            'after:abstract'
        );*/

        /*
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'pages',
            'tx_sitedefault_image_teaser',
            '',
            'after:media'
        );*/


        //======================================================================================================
        // croppings
        //======================================================================================================
        $GLOBALS['TCA']['pages']['columns']['media']['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'] = [

            'desktop' => [
                'title' => 'Desktop',
                'allowedAspectRatios' => [
                    'desktop' => [
                        'title' => 'Desktop',
                        'value' => 1920 / 300
                    ]
                ],
            ],
            'tablet' => [
                'title' => 'Tablet',
                'allowedAspectRatios' => [
                    'tablet' => [
                        'title' => 'Tablet',
                        'value' => 768 / 300
                    ]
                ]
            ],
            'mobile' => [
                'title' => 'Mobile',
                'allowedAspectRatios' => [
                    'mobile' => [
                        'title' => 'Mobile',
                        'value' => 390 / 300
                    ]
                ]
            ],
        ];

	},
	'site_default'
);
