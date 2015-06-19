<?php

$types = array();
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
foreach ($extConf['provider.'] as $provider => $config) {
    $provider = str_replace('.', '', $provider);
    if ((Boolean)$config['enabled']) {
        $types['Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity'] = array('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.tx_extbase_type.Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity','Tx_PxHybridAuth_Domain_Model_Identity_' . ucfirst($provider) . 'Identity');
    }
}
return array(
    'ctrl' => array(
        'title'	=> 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity',
        'label' => 'identifier',
        'label_userFunc' => 'Portrino\PxHybridAuth\UserFunc\Label->getIdentityLabel',
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
        'enablecolumns' => array(
            'disabled' => 'hidden',

        ),
        'searchFields' => 'identifier,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('px_hybrid_auth') . 'Resources/Public/Icons/tx_pxhybridauth_domain_model_identity.gif'
    ),
    'types' => array(
        'Tx_PxHybridAuth_Domain_Model_Identity_FacebookIdentity' => array('showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'),
        'Tx_PxHybridAuth_Domain_Model_Identity_XingIdentity' => array('showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'),
        'Tx_PxHybridAuth_Domain_Model_Identity_LinkedinIdentity' => array('showitem' => 'tx_extbase_type, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier'),

    ),
	'interface' => array(
		'showRecordFieldList' => 'tx_extbase_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, identifier',
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
        'tx_extbase_type' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.tx_extbase_type',
            'config' => array(
                'type' => 'select',
                'default' => count($types) > 0 ? array_shift(array_keys($types)) : '',
                'items' => $types
            )
        ),
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_pxhybridauth_domain_model_identity',
				'foreign_table_where' => 'AND tx_pxhybridauth_domain_model_identity.pid=###CURRENT_PID### AND tx_pxhybridauth_domain_model_identity.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),

		'identifier' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),

		'fe_user' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);
