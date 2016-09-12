<?php
defined('TYPO3_MODE') || die();

$boot = function ($_EXTKEY) {

    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
        $_EXTKEY,
        'auth',
        \Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService::class,
        [
            'title' => 'PxHybridAuth Social Login',
            'description' => 'Single Sign On via Social Provider',

            'subtype' => 'authUserFE,getUserFE',

            'available' => true,
            'priority' => 82, /* will be called before default typo3 authentication service */
            'quality' => 82,

            'os' => '',
            'exec' => '',

            'className' => \Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService::class
        ]
    );

    if (TYPO3_MODE === 'FE') {
        // Endpoint-Plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Portrino.' . $_EXTKEY,
            'HybridAuth',
            [
                'HybridAuth' => 'endpoint',
            ],
            // non-cacheable actions
            [
                'HybridAuth' => 'endpoint',
            ]
        );
        // Login-Plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Portrino.' . $_EXTKEY,
            'Login',
            [
                'FacebookUser' => 'newLogin',
                'LinkedinUser' => 'newLogin',
                'XingUser' => 'newLogin',

            ],
            // non-cacheable actions
            [
                'FacebookUser' => 'newLogin',
                'LinkedinUser' => 'newLogin',
                'XingUser' => 'newLogin',
            ]
        );

        // Identity-Plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Portrino.' . $_EXTKEY,
            'Identity',
            [
                'Identity' => 'list,remove',
                'Identity\FacebookIdentity' => 'create,remove',
                'Identity\LinkedinIdentity' => 'create,remove',
                'Identity\XingIdentity' => 'create,remove'
            ],
            // non-cacheable actions
            [
                'Identity' => 'list,remove',
                'Identity\FacebookIdentity' => 'create,remove',
                'Identity\LinkedinIdentity' => 'create,remove',
                'Identity\XingIdentity' => 'create,remove'
            ]
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup',
            '[GLOBAL]
                tt_content.px_hybrid_auth_login = COA
                tt_content.px_hybrid_auth_login {
                    10 = < lib.stdheader
                    20 >
                    20 = < plugin.tx_pxhybridauth_login
                }
        ', true);

        if ((boolean)$extConf['provider.']['facebook.']['enabled'] ||
            (boolean)$extConf['provider.']['linkedin.']['enabled'] ||
            (boolean)$extConf['provider.']['xing.']['enabled']
        ) {

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing']) === false ) {
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'] = [];
            }
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][$_EXTKEY] =
                \Portrino\PxHybridAuth\Hooks\LogOffHook::class . '->postProcessing';
        }

        if ((boolean)$extConf['auto_fe_user_creation.']['enabled']) {
            $signalSlotDispatcher->connect(
                \Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService::class,
                'getUser',
                \Portrino\PxHybridAuth\Slots\SocialLoginAuthenticationServiceSlot::class,
                'getUser',
                false
            );
        }
    }

    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] =
            \Portrino\PxHybridAuth\Hooks\PageLayoutViewDrawItemHook::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
            \Portrino\PxHybridAuth\Hooks\ProcessDataMapDispatcher::class;

        $signalSlotDispatcher->connect(
            'tt_content',
            'updateRecordPostProcessFieldArray',
            \Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot::class,
            'preventEmptyFlexFormValues',
            false
        );

        $signalSlotDispatcher->connect(
            'tt_content',
            'newRecordPostProcessFieldArray',
            \Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot::class,
            'preventEmptyFlexFormValues',
            false
        );


        /**
         * register icons for each plugin
         */
        $pluginSignatures = [
            'px_hybrid_auth_login',
            'px_hybrid_auth_identity'
        ];

        foreach ($pluginSignatures as $pluginSignature) {
            $iconRegistry->registerIcon(
                str_replace('_', '-', $pluginSignature),
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/' . $pluginSignature . '.svg'
                ]
            );
        }

        /**
         * register icons for each identity types
         */
        $identityTypes = [
            'facebook',
            'xing',
            'linkedin'
        ];

        foreach ($identityTypes as $identityType) {
            $iconRegistry->registerIcon(
                'identity-' . $identityType,
                \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
                [
                    'name' => $identityType
                ]
            );
        }
    }
};

$boot($_EXTKEY);
unset($boot);