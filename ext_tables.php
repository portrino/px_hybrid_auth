<?php
defined('TYPO3_MODE') || die();

$boot = function ($_EXTKEY) {

    // Identity-Plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $_EXTKEY,
        'Identity',
        'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:plugins.identity.name'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'HybridAuth');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:px_hybrid_auth/Configuration/TypoScript/PageTSConfig.txt">'
    );

};

$boot($_EXTKEY);
unset($boot);
