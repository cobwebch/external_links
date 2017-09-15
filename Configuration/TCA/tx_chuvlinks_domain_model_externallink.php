<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:external_links/Resources/Private/Language/tx_externallinks_domain_model_externallink.xlf:externalLink',
        'label' => 'url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'url',
        'iconfile' => 'EXT:external_links/Resources/Public/Icons/tx_externallinks_domain_model_externallink.png',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, url, note'],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'url' => [
            'label' => 'LLL:EXT:external_links/Resources/Private/Language/tx_externallinks_domain_model_externallink.xlf:url',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim'
            ],
        ],
        'note' => [
            'label' => 'LLL:EXT:external_links/Resources/Private/Language/tx_externallinks_domain_model_externallink.xlf:note',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2,
                'eval' => 'trim'
            ],
        ],
    ],
];