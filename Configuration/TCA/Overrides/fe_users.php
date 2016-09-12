<?php
defined('TYPO3_MODE') || die();

$boot = function () {

    $extKey = 'px_hybrid_auth';
    $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);
    $languageFilePrefix = 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:';

    $supportedProviders = [];
    foreach ($extConf['provider.'] as $provider => $config) {
        $provider = str_replace('.', '', $provider);
        if ((Boolean)$config['enabled']) {
            $supportedProviders[$provider] = TRUE;
        }
    }

    if (count($supportedProviders) > 0) {
        $tmp_columns = [
            'tx_pxhybridauth_identities' => [
                'exclude' => 1,
                'label' => $languageFilePrefix . 'tx_pxhybridauth_domain_model_user.identities',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_pxhybridauth_domain_model_identity',
                    'foreign_field' => 'fe_user',
                    'maxitems' => 9999,
                    'appearance' => [
                        'collapseAll' => 1,
                        'levelLinksPosition' => 'top',
                        'showSynchronizationLink' => 1,
                        'showPossibleLocalizationRecords' => 1,
                        'showAllLocalizationLink' => 1
                    ],
                ],
            ]
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;' . $languageFilePrefix . 'fe_users.tabs.px_hybrid_auth, tx_pxhybridauth_identities,', '', '');
    } else {
        $tmp_columns = [
            'tx_pxhybridauth_identities' => [
                'exclude' => 1,
                'label' => $languageFilePrefix . ':tx_pxhybridauth_domain_model_user.identities',
                'config' => [
                    'type' => 'user',
                    'userFunc' => \Portrino\PxHybridAuth\UserFunc\Tca::class . '->showNoProvidersConfiguredMessage',
                ]
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;' . $languageFilePrefix . 'fe_users.tabs.px_hybrid_auth, tx_pxhybridauth_identities,', '', '');
    }

};

$boot();
unset($boot);
