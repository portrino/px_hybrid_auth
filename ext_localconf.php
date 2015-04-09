<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);

// Endpoint-Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Portrino.' . $_EXTKEY,
    'HybridAuth',
    array(
        'HybridAuth' => 'endpoint',
    ),
    // non-cacheable actions
    array(
        'HybridAuth' => 'endpoint',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Portrino.' . $_EXTKEY,
	'Login',
	array(
		'FacebookUser' => 'newLogin',
		'LinkedinUser' => 'newLogin',
		'XingUser' => 'newLogin',

	),
	// non-cacheable actions
	array(
        'FacebookUser' => 'newLogin',
        'LinkedinUser' => 'newLogin',
        'XingUser' => 'newLogin',
	)
);

// Identity-Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Portrino.' . $_EXTKEY,
    'Identity',
    array(
        'Identity' => 'list',
        'Identity\FacebookIdentity' => 'create',
        'Identity\LinkedinIdentity' => 'create',
        'Identity\XingIdentity' => 'create'
    ),
    // non-cacheable actions
    array(
        'Identity' => 'list',
        'Identity\FacebookIdentity' => 'create',
        'Identity\LinkedinIdentity' => 'create',
        'Identity\XingIdentity' => 'create'
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY,'setup',
'[GLOBAL]
tt_content.px_hybrid_auth_login = COA
tt_content.px_hybrid_auth_login {
	10 = < lib.stdheader
	20 >
	20 = < plugin.tx_pxhybridauth_login
}
', TRUE);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'auth' /* sv type */, '\Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService' /* sv key */,
    array(
        'title' => 'PxHybridAuth Social Login',
        'description' => 'Single Sign On via Social Provider',

        'subtype' => 'authUserFE,getUserFE',

        'available' => TRUE,
        'priority' => 82, /* will be called before default typo3 authentication service */
        'quality' => 82,

        'os' => '',
        'exec' => '',

        'className' => '\Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService'
    )
);

// hook for content element preview in backend
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] =
    'Portrino\\PxHybridAuth\\Hooks\\PageLayoutViewDrawItemHook';

if ((boolean)$extConf['facebook.']['enabled'] ||
    (boolean)$extConf['linkedin.']['enabled'] ||
    (boolean)$extConf['xing.']['enabled']) {

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][$_EXTKEY] =
        'Portrino\PxHybridAuth\Hooks\LogOffHook->postProcessing';
}

if (\Portrino\PxLib\Utility\ExtensionManagementUtility::isFeatureEnabled('auto_fe_user_creation', $_EXTKEY)) {
    $signalSlotDispatcher->connect(
        'Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService',
        'getUser',
        'Portrino\PxHybridAuth\Slots\SocialLoginAuthenticationServiceSlot',
        'getUser',
        FALSE
    );
}

$signalSlotDispatcher->connect(
    'tt_content',
    'updateRecordPostProcessFieldArray',
    'Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot',
    'preventEmptyFlexFormValues',
    FALSE
);

$signalSlotDispatcher->connect(
    'tt_content',
    'newRecordPostProcessFieldArray',
    'Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot',
    'preventEmptyFlexFormValues',
    FALSE
);

