<?php
defined('TYPO3') or die('Access denied.');
call_user_func(
	function($extensionKey)
	{

		//===========================================================================
		// Add fields
		//===========================================================================
		// Change editor settings
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['bodytext']['config']['richtextConfiguration'] = 'Default';

		// add editor for shy
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['title']['config']['type'] = 'text';
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['title']['config']['enableRichtext'] = true;
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['title']['config']['richtextConfiguration'] = 'ShyOnly';
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['teaser']['config']['enableRichtext'] = true;
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['teaser']['config']['richtextConfiguration'] = 'Reduced';

        // define allowed files for media
        $GLOBALS['TCA']['tx_news_domain_model_news']['columns']['fal_media']['config']['allowed'] = ['jpeg','jpg','png','gif','svg','webp'];

        // add max items for fal_media
        $GLOBALS['TCA']['tx_news_domain_model_news']['columns']['fal_media']['config']['maxitems'] = 1;

        // add another field for slug - it is updated via hook. This to be able to enable richtext-editor for title!
		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['path_segment']['config']['generatorOptions'] = [
			'fields' => [['tx_sitedefault_title_cleaned', 'title']],
			'replacements' => [
				'/' => '-',
			],
		];

		// remove globally unused fields
//        $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0'] = str_replace(
//            '--div--;LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.content_elements,',
//            '',
//            $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']
//        );
//        $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0'] = str_replace(
//            'content_elements,',
//            '',
//            $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']
//        );
        $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0'] = str_replace(
            'related_links,tags,',
            '',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']
        );
        $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0'] = str_replace(
            'related,related_from,',
            '',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']
        );
		$GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
			'fal_related_files,',
			'',
			$GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
		);
		$GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
			'--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,',
			'',
			$GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
		);
        $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,',
            '',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
        );
        $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
            '--palette--;;paletteCore,',
            'type,',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
        );
        $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
            'related_links',
            '',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
        );
        $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0] = str_replace(
            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;paletteAuthor',
            '',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][0]
        );

		// remove unused types
		unset($GLOBALS['TCA']['tx_news_domain_model_news']['columns']['type']['config']['items'][1]);
		unset($GLOBALS['TCA']['tx_news_domain_model_news']['columns']['type']['config']['items'][2]);

        $ll = 'LLL:EXT:site_default/Resources/Private/Language/locallang_db.xlf:';
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',
			[
				'tx_sitedefault_title_cleaned' => [
					'exclude' => 0,
					'label' =>  $ll  . 'tx_news_domain_model_news.tx_sitedefault_title_cleaned',
					'config' => [
						'type' => 'input',
						'size' => 150,
						'eval' => 'trim',
                        'readOnly' => true
                    ]
				],
                'tx_sitedefault_datetime_end' => [
                    'exclude' => false,
                    'label' => $ll . 'tx_news_domain_model_news.tx_sitedefault_datetime_end',
                    'config' => [
                        'type' => 'datetime',
                        'eval' => 'datetime',
                    ],
                ],
                'tx_sitedefault_location' => [
                    'exclude' => 0,
                    'label' =>  $ll  . 'tx_news_domain_model_news.tx_sitedefault_location',
                    'config' => [
                        'type' => 'input',
                        'size' => 150,
                        'eval' => 'trim',
                    ]
                ],
                'tx_sitedefault_introduction' => [
                    'exclude' => 0,
                    'label' =>  $ll  . 'tx_news_domain_model_news.tx_sitedefault_introduction',
                    'config' => [
                        'type' => 'text',
                        'cols' => 40,
                        'rows' => 10,
                        'eval' => 'trim',
                        'enableRichtext' => true,
                        'richtextConfiguration' => 'Reduced'
                    ]
                ],
                'tx_sitedefault_image_preview' => [
					'exclude' => 0,
					'label' => $ll . 'tx_news_domain_model_news.tx_sitedefault_image_preview',
					'config' => [
						'type' => 'file',
						'allowed' => ['jpeg','jpg','png','gif','svg','webp'],
						'minitems' => 1,
						'maxitems' => 1,
					],
				],
			],
		);

		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
			'tx_news_domain_model_news',
			'tx_sitedefault_title_cleaned',
			'',
			'before:path_segment'
		);
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
			'tx_news_domain_model_news',
			'tx_sitedefault_image_preview',
			'',
			'before:fal_media'
		);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tx_news_domain_model_news',
            'tx_sitedefault_datetime_end',
            '',
            'after:datetime'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tx_news_domain_model_news',
            'tx_sitedefault_location',
            '',
            'before:teaser'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tx_news_domain_model_news',
            'tx_sitedefault_introduction',
            '',
            'before:bodytext'
        );

		$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['tx_sitedefault_image_preview']['config']['overrideChildTca']['columns']['crop']['config'] = [
			'cropVariants' => [
				'default' => [
					'title' => 'Default',
					'allowedAspectRatios' => [
						'preview' => [
							'title' => 'Default',
							'value' => 450/ 300
						],
					],
				],
			],
		];

	},
	'site_default'
);
