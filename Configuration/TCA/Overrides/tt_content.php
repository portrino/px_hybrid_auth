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
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:px_hybrid_auth/Configuration/FlexForms/Login.xml',
        'px_hybrid_auth_login'
    );
} else {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:px_hybrid_auth/Configuration/FlexForms/Error.xml',
        'px_hybrid_auth_login'
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    array(
        'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:CType.I.px_hybrid_auth_login',
        'px_hybrid_auth_login',
        'EXT:px_hybrid_auth/Resources/Public/Icons/px_hybrid_auth_login_form.gif'
    ),
    'login',
    'after'
);

$GLOBALS['TCA']['tt_content']['types']['px_hybrid_auth_login']['showitem'] =
    '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,'
    . '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.header;header,'
    . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.plugin,pi_flexform;;;;1-1-1,'
    . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,'
    . '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.visibility;visibility,'
    . '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access,'
    . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance,'
    . '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.frames;frames,'
    . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.behaviour,'
    . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';