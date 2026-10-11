<?php
$ll = 'LLL:EXT:greens_contact/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_contact_domain_model_contactfunction',
        'label' => 'position',
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
        'sortby' => 'sorting',
        'searchFields' => 'position',
        'iconfile' => 'EXT:greens_contact/Resources/Public/Icons/model-person.svg',
        'hideTable' => true,
    ],
    'types' => [
        '0' => [
            'showitem' => 'position, function_group,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'
        ],
    ],
    'columns' => [
        'person' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'position' => [
            'label' => $ll . 'tx_contact_domain_model_contactfunction.position',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'ShyOnly',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim,required',
            ],
        ],
        'function_group' => [
            'label' => $ll . 'tx_contact_domain_model_contactfunction.function_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_contact_domain_model_contactfunctiongroup',
                'itemsProcFunc' => \Greens\Contact\UserFunc\FunctionGroupItems::class . '->getItems',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
    ],
];
