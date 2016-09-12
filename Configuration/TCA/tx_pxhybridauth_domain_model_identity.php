<?php

$types = [];
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
foreach ($extConf['provider.'] as $provider => $config) {
    $provider = str_replace('.', '', $provider);
    if ((Boolean)$config['enabled']) {
        $types['Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity'] =
            ['LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.tx_extbase_type.Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity', 'Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity'];
    }
}
return [
    'ctrl' => [
        'title'    => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity',
        'label' => 'identifier',
        'label_userFunc' => \Portrino\PxHybridAuth\UserFunc\Label::class . '->getIdentityLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'hideTable' => TRUE,

        'type' => 'tx_extbase_type',

        'versioningWS' => 2,
        'versioning_followPages' => TRUE,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',

        ],
        'typeicon_column' => 'tx_extbase_type',
        'typeicon_classes' => [
            'Tx_PxHybridAuth_Domain_Model_Identity_FacebookIdentity' => 'identity-facebook',
            'Tx_PxHybridAuth_Domain_Model_Identity_XingIdentity' => 'identity-xing',
            'Tx_PxHybridAuth_Domain_Model_Identity_LinkedinIdentity' => 'identity-linkedin'
        ],
        'searchFields' => 'identifier,',
    ],
    'types' => [
        'Tx_PxHybridAuth_Domain_Model_Identity_FacebookIdentity' => ['showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'],
        'Tx_PxHybridAuth_Domain_Model_Identity_XingIdentity' => ['showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'],
        'Tx_PxHybridAuth_Domain_Model_Identity_LinkedinIdentity' => ['showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'],

    ],
    'interface' => [
        'showRecordFieldList' => 'tx_extbase_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, identifier',
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'tx_extbase_type' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.tx_extbase_type',
            'config' => [
                'type' => 'select',
                'default' => count($types) > 0 ? array_shift(array_keys($types)) : '',
                'items' => $types
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxhybridauth_domain_model_identity',
                'foreign_table_where' => 'AND tx_pxhybridauth_domain_model_identity.pid=###CURRENT_PID### AND tx_pxhybridauth_domain_model_identity.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],

        'identifier' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.identifier',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],

        'fe_user' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
