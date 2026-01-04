<?php
return [
    'BE' => [
        'installToolPassword' => '$argon2i$v=19$m=65536,t=16,p=1$UzdCTVlhRTJjT1BGaktYVQ$e5n/qHfd9B2Rpv20b1mtRkG8hDHI4qVCF5mv8LnnKhU',
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8',
                'driver' => 'mysqli',
            ],
        ],
    ],
    'EXTCONF' => [
        'lang' => [
            'availableLanguages' => [
                'de',
            ],
        ],
    ],
    'EXTENSIONS' => [
        'accelerator' => [
            'proxyCachingMode' => 'default',
        ],
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => '',
            'loginFootnote' => '',
            'loginHighlightColor' => '#008939',
            'loginLogo' => 'EXT:site_default/Resources/Public/Images/logo-desktop.svg',
            'loginLogoAlt' => '',
        ],
        'dr_serp' => [
            'humanizeAiContentEnable' => '1',
            'humanizeAiContentSignsRemove' => '200B, 2060',
            'humanizeAiContentSignsSpace' => '202F',
            'pageTitleCombineFields' => '0',
            'pageTitleCombineFieldsNews' => '0',
            'pageTitleFields' => 'seo_title, title, subtitle',
            'pageTitleFieldsNews' => 'alternative_title, title',
            'pageTitleIncludePageName' => '1',
            'pageTitleIncludePageNameNews' => '1',
            'pageTitleSeparator' => '–',
            'pageTitleSeparatorNews' => '–',
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
        'forminator' => [
            'flexformExtensionFiles' => '*|EXT:forminator/Configuration/FlexForms/Extend/PrivacyExtend.xml',
        ],
        'ke_search' => [
            'additionalWordCharacters' => '',
            'allowEmptySearch' => '1',
            'enableExplicitAnd' => '0',
            'enablePartSearch' => '1',
            'finishNotification' => '0',
            'indexTagTitlesAsHiddenContent' => '1',
            'loglevel' => 'ERROR',
            'multiplyValueToTitle' => '1',
            'notificationRecipient' => '',
            'notificationSender' => 'no_reply@domain.com',
            'notificationSubject' => '[KE_SEARCH INDEXER NOTIFICATION]',
            'pathCatdoc' => '/usr/bin/',
            'pathPdfinfo' => '/usr/bin/',
            'pathPdftotext' => '/usr/bin/',
            'searchWordLength' => '4',
        ],
        'news' => [
            'advancedMediaPreview' => '1',
            'archiveDate' => 'date',
            'categoryBeGroupTceFormsRestriction' => '0',
            'categoryRestriction' => '',
            'contentElementRelation' => '1',
            'dateTimeNotRequired' => '0',
            'hidePageTreeForAdministrationModule' => '0',
            'manualSorting' => '0',
            'pageTreePluginPreview' => '1',
            'prependAtCopy' => '1',
            'resourceFolderImporter' => '/news_import',
            'rteForTeaser' => '0',
            'showAdministrationModule' => '1',
            'slugBehaviour' => 'unique',
            'storageUidImporter' => '1',
            'tagPid' => '1',
        ],
        'schema' => [
            'allowOnlyOneBreadcrumbList' => '0',
            'automaticBreadcrumbExcludeAdditionalDoktypes' => '',
            'automaticBreadcrumbSchemaGeneration' => '0',
            'automaticWebPageSchemaGeneration' => '1',
            'embedMarkupInBodySection' => '0',
            'embedMarkupOnNoindexPages' => '1',
        ],
    ],
    'FE' => [
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'GFX' => [],
    'LOG' => [
        'TYPO3' => [
            'CMS' => [
                'deprecations' => [
                    'writerConfiguration' => [
                        'notice' => [
                            'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'MAIL' => [
        'layoutRootPaths' => [
            1718443574 => 'EXT:site_default/Resources/Private/Email/Layouts/',
        ],
        'partialRootPaths' => [
            1718443574 => 'EXT:site_default/Resources/Private/Email/Partials/',
        ],
        'transport' => 'sendmail',
        'transport_sendmail_command' => '/usr/local/bin/mailpit sendmail -t --smtp-addr 127.0.0.1:1025',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'caching' => [
            'cacheConfigurations' => [
                'hash' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                ],
                'imagesizes' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'pages' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'rootline' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
            ],
        ],
        'encryptionKey' => '88370f4494d020a94d4200052a9d58a5b82364b32db2ec67152d5834c12d179ca8b92627aa8101b326292143920f1e19',
        'sitename' => 'BÜNDNIS 90 / DIE GRÜNEN Lahn-Dill-Kreis',
        'systemMaintainers' => [
            1,
            2,
        ],
    ],
];
