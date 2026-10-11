<?php
$ll = 'LLL:EXT:greens_contact/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_contact_domain_model_contactfunctiongroup',
        'label' => 'title',
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
        'searchFields' => 'title',
        'iconfile' => 'EXT:greens_contact/Resources/Public/Icons/model-person.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'title,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'
        ],
    ],
    'columns' => [
        'title' => [
            'label' => $ll . 'tx_contact_domain_model_contactfunctiongroup.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
            ],
        ],
    ],
];
