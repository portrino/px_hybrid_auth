<?php
// load extConf
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);

$supportedProviders = array();
foreach ($extConf['provider.'] as $provider => $config) {
    $provider = str_replace('.', '', $provider);
    if ((Boolean)$config['enabled']) {
        $supportedProviders[$provider] = TRUE;
    }
}

if (count($supportedProviders) > 0) {
    $tmp_columns = array(
        'tx_pxhybridauth_identities' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_user.identities',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_pxhybridauth_domain_model_identity',
                'foreign_field' => 'fe_user',
                'maxitems' => 9999,
                'appearance' => array(
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ),
            ),
        )
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:fe_users.tabs.px_hybrid_auth, tx_pxhybridauth_identities,', '', '');
} else {
    $tmp_columns = array(
        'tx_pxhybridauth_identities' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_user.identities',
            'config' => array (
                'type' => 'user',
                'userFunc' => 'Portrino\\PxHybridAuth\\UserFunc\\Tca->showNoProvidersConfiguredMessage',
            )
        ),
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:fe_users.tabs.px_hybrid_auth, tx_pxhybridauth_identities,', '', '');
}