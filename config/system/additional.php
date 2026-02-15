<?php
/*
 *  IMPORTANT: DO NOT INCLUDE ANY PASSWORDS OR ENCRYPTION-KEYS IN THIS FILE!!!!
 */
$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
    $GLOBALS['TYPO3_CONF_VARS'],
    [
        'BE' => [
            'cookieDomain' => '',                       // uses [SYS][cookieDomain] if empty
            'cookieSameSite' => 'strict',               // lock cookie to current domain, cookie unavailable through iFrames
            'debug' => false,                           // If enabled, the login refresh is disabled and pageRenderer is set to debug mode. Furthermore the fieldname is appended to the label of fields.
            'disable_exec_function' => false,           // Dont use exec() function. If set, all file operations are done by the default PHP-functions
            'lockBeUserToDBmounts' => 1,                // lock BE-Users to their page-mounts
            'lockIP' => 4,                              // lock BE-Users to full IP-address of their session
            'lockIPv6' => 8,                            // lock FE-Users to full IP-address of their session
            'lockSSL' => true,                          // lock BE to SSL
            'loginSecurityLevel' => 'normal',
            'sessionTimeout' => 28800,                  // set session timeout to 8 hours
            'versionNumberInFilename' => false,         // use filemtime appended to the query-string instead of timestamp
            'warning_email_addr' => '',                 // sent warnings on failed BE-logins and InstallTool-Logins
            'requireMfa' => 0                           // Multi-Factor-Authentification
        ],
        'FE' => [
            'cookieDomain' => '',                       // uses [SYS][cookieDomain] if empty
            'cookieSameSite' => 'lax',                  // lock cookie to current domain, cookie unavailable through iFrames - SHOULD BE STRICT!!!
            'debug' => false,                           // If enabled, the total parse time of the page is added as HTTP response header
            'disableNoCacheParameter' =>  true,         // disable &no_cache=1 param, relevant for performance and security
            'lockIP' => 2,                              // lock FE-Users to first two parts IP-address of their session
            'lockIPv6' => 4,                            // lock FE-Users to first four parts of the IP-address of their session
            'loginSecurityLevel' => 'normal',
            'pageNotFoundOnCHashError' => false,        // simply show page if cHash is invalid
            'sessionDataLifetime' => 86400,             // delete anonymous session data after 24 hours
            'versionNumberInFilename' => false,         // deactivate version numbers on css/js-files
            'cacheHash' => [
                'enforceValidation' => true,
                'excludedParameters' => [
                    'L',
                    'no_cache',
                    'type',
                    'v',
                    'pk_campaign',
                    'pk_kwd',
                    'utm_source',
                    'utm_medium',
                    'utm_campaign',
                    'utm_term',
                    'utm_content',
                    'gclid',
                    'fbclid'
                ]
            ]
        ],
        'GFX' => [
            'gdlib_png' => true,                            // use GD-Lib for PNGs
            'imagefile_ext' => 'gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg,webp',
            'jpg_quality' => 85,                            // Default JPEG generation quality
            'processor' => 'ImageMagick',                   // Use Image- or GraphicsMagick
            'processor_allowTemporaryMasksAsPng' => true,   // Using PNGs as masks, usually faster
            'processor_colorspace' => 'sRGB',               // Colorspace to use
            'processor_effects' => true,                    // Allow blur and sharpening in ImageMagick.
            'processor_enabled' => true,                    // Enables the use of Image- or GraphicsMagick.
            'processor_path' => '/usr/bin/',                // Path to the IM tools 'convert', 'combine', 'identify'.
            'processor_path_lzw' => '/usr/bin/',            // Path to the IM tool 'convert' with LZW enabled
            'processor_stripColorProfileByDefault' => true, // Remove existing color profiles.
            'processor_stripColorProfileCommand' => '+profile \'*\'', // command to strip the profile information
        ],
        'MAIL' => [
            'defaultMailFromAddress' => '',
            'defaultMailFromName' => '',
            'defaultMailReplyToAddress' => '',
            'defaultMailReplyToName' => '',
            'defaultMailReturnAddress' => '',
            'layoutRootPaths' => [
                'EXT:core/Resources/Private/Layouts/',
                'EXT:backend/Resources/Private/Layouts/',
                'EXT:site_default/Resources/Private/Email/Layouts/',
            ],
        ],
        'SYS' => [
            'UTF8filesystem' => true,                   // use utf-8 to store file names
            'cookieDomain' => '',
            'cookieSecure' => 2,                        // the cookie uses the secure flag if a secure (HTTPS) connection exists
            'fileCreateMask' => '0664',                 // File mode mask for Unix file systems
            'folderCreateMask' => '2775',               // Folder mode mask for Unix file systems
            'generateApacheHtaccess' => true,           // create .htaccess files for protection
            'ipAnonymization' => 2,                     // Mask the last two bytes for IPv4 addresses / Mask the Interface ID and SLA ID for IPv6 addresses
            'systemLocale' => 'de_DE.UTF-8',            // locale used for certain system related functions
            'trustedHostsPattern' => '.*',
            'features' => [
                'security.backend.enforceContentSecurityPolicy' => true,
                'security.usePasswordPolicyForFrontendUsers' => true,
            ],
        ]
    ]
);

switch (\TYPO3\CMS\Core\Core\Environment::getContext()) {

    case 'Production':
        $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
            $GLOBALS['TYPO3_CONF_VARS'],
            [
                'DB' => [
                    'Connections' => [
                        'Default' => [
                            'dbname' => '###DB_NAME###',
                            'driver' => 'mysqli',
                            'host' => '###DB_HOST###',
                            'password' => '###DB_PASSWORD###',
                            'port' => '3306',
                            'user' => '###DB_USER###',
                        ],
                    ],
                ],
                'SYS' => [
                    //'reverseProxyIP' => '10.10.1.2',            // revers proxy IP
                    //'reverseProxyHeaderMultiValue' => 'first',
                    'encryptionKey' => '###ENCRYPTION_KEY###',
                ],

            ]
        );

        break;
    case 'Production\Staging':
        $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
            $GLOBALS['TYPO3_CONF_VARS'],
            [
                'FE' => [
                    'debug' => true,                            // If enabled, the total parse time of the page is added as HTTP response header
                ],
                'BE' => [
                    'debug' => true,                            // If enabled, the login refresh is disabled and pageRenderer is set to debug mode. Furthermore the fieldname is appended to the label of fields.
                    //'lockSSL' => 0,                           // lock BE to SSL
                ],
                'SYS' => [
                    'cookieDomain' => '',
                    'trustedHostsPattern' => '.*',
                    //'reverseProxyIP' => '10.10.1.2',            // revers proxy IP
                    //'reverseProxyHeaderMultiValue' => 'first',
                    'displayErrors' => 1,
                    'errorHandler' => 'TYPO3\\CMS\\Core\\Error\\ErrorHandler',
                    'errorHandlerErrors' => E_ALL ^ E_NOTICE,
                    'exceptionalErrors' => E_ALL ^ E_NOTICE ^ E_WARNING ^ E_USER_ERROR ^ E_USER_NOTICE ^ E_USER_WARNING,
                    'debugExceptionHandler' => 'TYPO3\\CMS\\Core\\Error\\DebugExceptionHandler',
                    'productionExceptionHandler' => 'TYPO3\\CMS\\Core\\Error\\DebugExceptionHandler',
                    'encryptionKey' => '###ENCRYPTION_KEY###',
                ],

            ]
        );

        break;

    case 'Development/Local':
        if (getenv('IS_DDEV_PROJECT') == 'true') {

            // deactivate cache completely
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'] as $cacheName => $cacheConfiguration) {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheName]['backend'] = \TYPO3\CMS\Core\Cache\Backend\NullBackend::class;
            }

            $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
                $GLOBALS['TYPO3_CONF_VARS'],
                [
                    'DB' => [
                        'Connections' => [
                            'Default' => [
                                'dbname' => 'db',
                                'driver' => 'mysqli',
                                'host' => 'db',
                                'password' => 'db',
                                'port' => '3306',
                                'user' => 'db',
                            ],
                        ],
                    ],
                    'FE' => [
                        'debug' => true,                            // If enabled, the total parse time of the page is added as HTTP response header
                    ],
                    'BE' => [
                        'debug' => true,                            // If enabled, the login refresh is disabled and pageRenderer is set to debug mode. Furthermore the fieldname is appended to the label of fields.
                        'lockSSL' => 0,                             // lock BE to SSL
                        'sessionTimeout' => 86400,                  // set session timeout to 24 hours
                        'warning_email_addr' => '',                 // sent warnings on failed BE-logins and InstallTool-Logins
                        'requireMfa' => 0                           // Multi-Factor-Authentification
                    ],
                    'GFX' => [
                        'processor' => 'ImageMagick',
                        'processor_path' => '/usr/bin/',
                        'processor_path_lzw' => '/usr/bin/',
                    ],
                    'MAIL' => [
                        'transport' => 'smtp',
                        'transport_smtp_encrypt' => false,
                        'transport_smtp_server' => 'localhost:1025', // use mailpit
                    ],
                    'SYS' => [
                        'cookieDomain' => '',
                        'debugExceptionHandler' => 'TYPO3\\CMS\\Core\\Error\\DebugExceptionHandler',
                        'devIPmask' => '*',
                        'displayErrors' => 1,
                        'errorHandler' => 'TYPO3\\CMS\\Core\\Error\\ErrorHandler',
                        'errorHandlerErrors' => E_ALL ^ E_NOTICE,
                        'exceptionalErrors' => E_ALL ^ E_NOTICE ^ E_WARNING ^ E_USER_ERROR ^ E_USER_NOTICE ^ E_USER_WARNING,
                        'productionExceptionHandler' => 'TYPO3\\CMS\\Core\\Error\\DebugExceptionHandler',
                        'trustedHostsPattern' => '.*',
                    ],
                    'EXTCONF' => [
                        'filefill' => [
                            'storages' => [
                                1 => [
                                    [
                                        'identifier' => 'domain',
                                        'configuration' => 'https://www.example.com/',
                                    ],
                                    [
                                        'identifier' => 'placeholder',
                                    ],
                                ]
                            ]
                        ]
                    ],
                    'LOG' => [
                        'TYPO3' => [
                            'CMS' => [
                                'deprecations' => [
                                    'writerConfiguration' => [
                                        \TYPO3\CMS\Core\Log\LogLevel::NOTICE => [
                                            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                                                'disabled' => false,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );
            break;
        }
}
