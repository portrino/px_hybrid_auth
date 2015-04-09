<?php

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('px_hybrid_auth');

$result = array(
    'Hybrid_Auth' => $extensionPath . 'Resources/Public/Php/Hybrid/Auth.php',
    'Hybrid_Endpoint' => $extensionPath . 'Resources/Public/Php/Hybrid/Endpoint.php',
    'Hybrid_Provider_Adapter' => $extensionPath . 'Resources/Public/Php/Hybrid/Provider_Adapter.php',
    'Hybrid_User_Profile' => $extensionPath . 'Resources/Public/Php/Hybrid/User_Profile.php',
);

return $result;
?>