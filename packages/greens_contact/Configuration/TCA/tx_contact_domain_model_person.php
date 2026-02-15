<?php
$ll = 'LLL:EXT:greens_contact/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_contact_domain_model_person',
        'label' => 'last_name',
        'label_alt' => 'first_name',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'first_name,last_name,title,position,vita',
        'versioningWS' => true,
        'hideAtCopy' => true,
        'iconfile' => 'EXT:greens_contact/Resources/Public/Icons/model-person.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => '
                salutation, title, first_name, last_name, email, phone, position, category,
                    --palette--;;palette_detail_link, sorting,
                --div--;' . $ll . 'tx_contact_domain_model_person.tab.meta, job, vita, description,
                --div--;' . $ll . 'tx_contact_domain_model_person.tab.media, image_small, image_big,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime
            ',
        ],
    ],
    'palettes' => [
        'palette_detail_link' => [
            'label' => $ll . 'tx_contact_domain_model_person.palette.detail_link',
            'showitem' => 'show_detail, --linebreak--, slug, --linebreak--, detail_link, detail_link_label',
        ],
    ],
    'columns' => [
        'salutation' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.salutation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => $ll . 'tx_contact_domain_model_person.salutation.mr',
                        'value' => '0',
                    ],
                    [
                        'label' => $ll . 'tx_contact_domain_model_person.salutation.ms',
                        'value' => '1',
                    ],
                    [
                        'label' => $ll . 'tx_contact_domain_model_person.salutation.other',
                        'value' => '99',
                    ],
                ],
                'eval' => 'trim',
                'required' => true,
                'default' => '99',

            ],
        ],
        'title' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'first_name' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.first_name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'last_name' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.last_name',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'job' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.job',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'ShyOnly',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'position' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.position',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'ShyOnly',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'category' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_contact_domain_model_person.category',
            'config' => [
                'type' => 'category',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 999,
                'foreign_table_where' => 'AND {#sys_category}.{#sys_language_uid} IN (-1, 0) AND {#sys_category}.{#pid} = ###CURRENT_PID###',
                'treeConfig' => [
                    'appearance' => [
                        'nonSelectableLevels' => '0'
                    ],
                ]
            ],
        ],
        'email' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.email',
            'config' => [
                'type' => 'email',
            ],
        ],
        'phone' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.phone',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'vita' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.vita',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'Reduced',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'description' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'Reduced',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],
        'show_detail' => [
            'label' => $ll . 'tx_contact_domain_model_person.show_detail',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'detail_link' => [
            'exclude' => 1,
            'label' => $ll . 'tx_contact_domain_model_person.detail_link',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['page', 'url'],
                'required' => false,
                'size' => 30,
            ],
        ],
        'detail_link_label' => [
            'exclude' => false,
            'label' => $ll . 'tx_contact_domain_model_person.detail_link_label',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'size' => 30,
            ],
        ],
        'image_small' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_contact_domain_model_person.image_small',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
                'minitems' => 1,
                'overrideChildTca' => [
                    'columns' => [
                        'crop' => [
                            'config' => [
                                'cropVariants' => [
                                    'default' => [
                                        'title' => 'Default',
                                        'allowedAspectRatios' => [
                                            '4x5' => [
                                                'title' => 'Default (4:5)',
                                                'value' => 4/5
                                            ],
                                            /*'NaN' => [
                                                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                                'value' => 0.0
                                            ],*/
                                        ],
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ],
        'image_big' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_contact_domain_model_person.image_big',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
                'overrideChildTca' => [
                    'columns' => [
                        'crop' => [
                            'config' => [
                                'cropVariants' => [
                                    'default' => [
                                        'title' => 'Default',
                                        'allowedAspectRatios' => [
                                            '1.1x1' => [
                                                'title' => 'Desktop (1.1:1)',
                                                'value' => 1.1/1
                                            ],
                                            /*'NaN' => [
                                                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                                'value' => 0.0
                                            ],*/
                                        ],
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ],
        'sorting' => [
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_contact_domain_model_person.sorting',
            'config' => [
                'type' => 'number',
                'default' => 0,
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'Slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'eval' => 'uniqueInSite',
                'generatorOptions' => [
                    'fields' => ['first_name', 'last_name'],
                    'fieldSeparator' => '-',
                    'prefixParentPageSlug' => false,
                ],
                'prependSlash' => false,
                'fallbackCharacter' => '-',
            ],
        ],

    ],
];
