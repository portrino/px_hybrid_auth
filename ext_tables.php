<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
    // Identity-Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Identity',
    'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:plugins.identity.name'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'HybridAuth');

\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('tt_content', 'px_hybrid_auth_login', '../typo3conf/ext/px_hybrid_auth/Resources/Public/Icons/px_hybrid_auth_login_form_no_bg.gif');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:px_hybrid_auth/Configuration/TypoScript/PageTSConfig.txt">'
);
