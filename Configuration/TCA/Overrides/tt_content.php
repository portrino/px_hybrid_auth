<?php
defined('TYPO3_MODE') || die();

$boot = function () {
    $extKey = 'px_hybrid_auth';
    $languageFilePrefix = 'LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:';
    $frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';
    $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);

    /**
     * register icons for each plugin
     */
    $pluginSignatures = [
        0 => 'px_hybrid_auth_login'
    ];
    foreach ($pluginSignatures as $pluginSignature) {

        $supportedProviders = [];
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
                $pluginSignature
            );
        } else {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                '*',
                'FILE:EXT:px_hybrid_auth/Configuration/FlexForms/Error.xml',
                $pluginSignature
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            [
                $languageFilePrefix . 'tt_content.CType.' . $pluginSignature,
                'px_hybrid_auth_login',
                'px-hybrid-auth-login'
            ]
        );
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['default'] = $pluginSignature;

        $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$pluginSignature] = 'px-hybrid-auth-login';

        $GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = [
            'showitem' => '
                --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
            --div--;' . $frontendLanguageFilePrefix . 'tabs.plugin,
                pi_flexform,
            --div--;' . $frontendLanguageFilePrefix . 'tabs.access,
                hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
                --palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
            --div--;' . $frontendLanguageFilePrefix . 'tabs.extended
        '
        ];

        // Add category tab when categories column exits
        if (!empty($GLOBALS['TCA']['tt_content']['columns']['categories'])) {
            $GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] .=
                ',--div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category,
                categories';
        }
    }

};

$boot();
unset($boot);
