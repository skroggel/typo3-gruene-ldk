<?php
defined('TYPO3') or die('Access denied.');
call_user_func(
	function($extKey) {


        // labels for list view in BE madj2k_header
        $GLOBALS['TCA']['tt_content']['ctrl']['label_alt_force'] = true;
        $GLOBALS['TCA']['tt_content']['ctrl']['label_alt'] = 'madj2k_hero,madj2k_header,madj2k_link_label, subheader,' .
            'bodytext,list_type,CType';


        /**
         * Remove fields from plugins, that we don't need
         */
        $pluginList = ['ke_search_pi1', 'ke_search_pi2'];
        foreach ($pluginList as $pluginName) {
            $GLOBALS['TCA']['tt_content']['types'][$pluginName]['showitem'] = '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;general, ' .
                // --palette--;;headers,
                '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
                    pi_flexform,' .
                 ($pluginName != 'ke_search_pi2' ? 'pages, recursive,' : '') .
                '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                    rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ';
        }

        /**
         * Remove fields from news_newsliststicky
         */
        $GLOBALS['TCA']['tt_content']['types']['news_newsliststicky']['showitem'] = '
		  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
		  	--palette--;;general,
            subheader,
          --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform';


        /**
         * remove types we don't need at all
         */
        $removeTypes = [
            'bullets','textpic','textmedia','image','menu_categorized_pages','menu_categorized_content',
            'menu_pages','menu_subpages','menu_sitemap','menu_section','menu_abstract','menu_recently_updated',
            'menu_related_pages','menu_section_pages','menu_sitemap_pages','table' ,'uploads'
        ];
        foreach ($removeTypes as $type) {
            unset($GLOBALS['TCA']['tt_content']['types'][$type]);
        }

		/**
		 * CropVariants
		 * table --> cType -> fieldName
		 */
        $cropVariants = [
            'tt_content' => [
                'madj2k_stage' => [
                    'assets' => [
                        'desktop' => [
                            'title' => 'Desktop',
                            'allowedAspectRatios' => [
                                '16/9' => [
                                    'title' => 'Desktop (16:9)',
                                    'value' => 16/9
                                ],
                                /*'NaN' => [
                                    'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                    'value' => 0.0
                                ],*/
                            ],
                        ],
                        'tablet' => [
                            'title' => 'Tablet',
                            'allowedAspectRatios' => [
                                '4/3' => [
                                    'title' => 'Tablet (4:3)',
                                    'value' => 4/3
                                ],
                                /*'NaN' => [
                                    'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                    'value' => 0.0
                                ],*/
                            ],
                        ],
                        'mobile' => [
                            'title' => 'Mobile',
                            'allowedAspectRatios' => [
                                '9/16' => [
                                    'title' => 'Mobile (9:16)',
                                    'value' => 9/16
                                ],
                                /*'NaN' => [
                                    'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                    'value' => 0.0
                                ],*/
                            ],
                        ],
                    ],
                ],
                'madj2k_textimage' => [
                    'assets' => [
                        'default' => [
                            'title' => 'Default',
                            'allowedAspectRatios' => [
                                '4/3' => [
                                    'title' => 'Default (4:3)',
                                    'value' => 4/3
                                ],
                                'NaN' => [
                                    'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                    'value' => 0.0
                                ],
                            ],
                        ],
                    ],
                ]
            ]
        ];

		foreach ($cropVariants as $table => $tableConfig) {
			foreach ($tableConfig as $cType => $cTypeConfig) {
				foreach ($cTypeConfig as $column => $cropConfig) {
					$GLOBALS['TCA'][$table]['types'][$cType]['columnsOverrides'][$column]['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'] = $cropConfig;
				}
			}
		}
	},

	'site_default'
);
