<?php
namespace Portrino\PxHybridAuth\Service;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 André Wuttig <wuttig@portrino.de>, portrino GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use Portrino\PxHybridAuth\Utility\SingleSignOnUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Sv\AbstractAuthenticationService;

/**
 * Class SocialLoginAuthenticationService
 *
 * @package Portrino\PxHybridAuth\Service
 */
class SocialLoginAuthenticationService extends AbstractAuthenticationService
{

    /**
     * 100 / 101 Authenticated / Not authenticated -> in each case go on with additonal auth
     */
    const STATUS_AUTHENTICATION_SUCCESS_CONTINUE = 100;

    const STATUS_AUTHENTICATION_FAILURE_CONTINUE = 101;

    /**
     * 200 - authenticated and no more checking needed - useful for IP checking without password
     */
    const STATUS_AUTHENTICATION_SUCCESS_BREAK = 200;

    /**
     * FALSE - this service was the right one to authenticate the user but it failed
     */
    const STATUS_AUTHENTICATION_FAILURE_BREAK = 0;

    /**
     * 100 - just go on. User is not authenticated but there's still no reason to stop.
     */
    const STATUS_USER_ACTIVE = 'active';

    /**
     * Object manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * SingleSignOnUtility
     *
     * @var \Portrino\PxHybridAuth\Utility\SingleSignOnUtility
     */
    public $singleSignOnUtility;

    /**
     * @var string
     */
    public $provider = '';

    /**
     * @var string
     */
    public $redirectUrl = '';

    /**
     * @var int
     */
    public $redirectPid;

    /**
     * @var array
     */
    public $extConf;

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public $db;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @var string
     */
    protected $extensionName = 'px_hybrid_auth';

    public function init()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
        $this->db = $this->getDatabaseConnection();

        return parent::init();
    }

    public function initAuth($mode, $loginData, $authInfo, $pObj)
    {
        $this->singleSignOnUtility = $this->objectManager->get(SingleSignOnUtility::class);
        if (isset($_REQUEST['pid'])) {
            $this->db_user['check_pid_clause'] = ' AND pid IN (' .
                $this->db->cleanIntList($_REQUEST['pid']) . ')';
        }
        if (isset($_REQUEST['tx_pxhybridauth_login']['redirect_url'])) {
            $this->redirectUrl = $_REQUEST['tx_pxhybridauth_login']['redirect_url'];
        }
        if (isset($_REQUEST['tx_pxhybridauth_login']['redirect_pid'])) {
            $this->redirectPid = $_REQUEST['tx_pxhybridauth_login']['redirect_pid'];
        }
        if (isset($_REQUEST['tx_pxhybridauth_login']['provider'])) {
            $this->provider = $_REQUEST['tx_pxhybridauth_login']['provider'];
        }

        parent::initAuth($mode, $loginData, $authInfo, $pObj);
    }

    public function getUser()
    {
        $user = false;
        if ($this->isServiceResponsible()) {
            $loginPid = $this->extConf['basic.']['login_pid'];
            $urlParts = [
                'scheme' => GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http',
                'host' => GeneralUtility::getIndpEnv('HTTP_HOST'),
            ];

            $additionalUrlParts = [
                'query' => 'id=' . $loginPid . '&no_cache=1&logintype=login&tx_pxhybridauth_login[provider]=' . $this->provider . '&pid=' . $this->userRecordStoragePage . '&tx_pxhybridauth_login[redirect_url]=' . $this->redirectUrl . '&tx_pxhybridauth_login[redirect_pid]=' . $this->redirectPid
            ];
            ArrayUtility::mergeRecursiveWithOverrule($urlParts, $additionalUrlParts);
            $returnUrl = HttpUtility::buildUrl($urlParts);
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'returnUrl', [&$returnUrl, $this]);

            $additionalUrlParts = [
                'query' => 'id=' . $loginPid . '&no_cache=1&tx_pxhybridauth_login[login_error]=1&tx_pxhybridauth_login[provider]=' . $this->provider
            ];
            ArrayUtility::mergeRecursiveWithOverrule($urlParts, $additionalUrlParts);
            $returnUrlNoUser = HttpUtility::buildUrl($urlParts);
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'returnUrlNoUser', [&$returnUrlNoUser, $this]);

            $socialUser = $this->singleSignOnUtility->authenticate($this->provider, $returnUrl);
            $user = $this->fetchUserRecordByIdentifier($socialUser->identifier);
            if (isset($user['username'])) {
                $this->login['uname'] = $user['username'];
            }
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'getUser', [&$user, $socialUser, $this]);

            // redirect to px_hybrid_auth login box, when no user found
            if (!$user) {
                HttpUtility::redirect($returnUrlNoUser);
            }
        }
        return $user;
    }

    /**
     * @param array $user
     *
     * @return int
     */
    public function authUser(array $user)
    {
        if (!$this->isServiceResponsible()) {
            return self::STATUS_AUTHENTICATION_FAILURE_CONTINUE;
        }
        $result = self::STATUS_AUTHENTICATION_FAILURE_CONTINUE;
        if ($user) {
            $result = self::STATUS_AUTHENTICATION_SUCCESS_BREAK;
        }
        $this->signalSlotDispatcher->dispatch(__CLASS__, 'authUser', [$user, &$result, $this]);

        return $result;
    }

    /**
     * Returns TRUE if single sign on for the given provider is enabled in ext_conf and is available
     *
     * @return boolean
     */
    protected function isServiceResponsible()
    {
        return (Boolean)$this->extConf['provider.'][strtolower($this->provider) . '.']['enabled'];
    }

    /**
     * Get global database connection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Get a user from DB by social identifier
     *
     * @param string $identifier social identifier
     * @param string $extraWhere Additional WHERE clause: " AND ...
     * @param array $dbUserSetup User db table definition: $this->db_user
     *
     * @return mixed User array or FALSE
     */
    public function fetchUserRecordByIdentifier($identifier, $extraWhere = '', $dbUserSetup = '')
    {
        $result = false;
        $identityClassName = 'Portrino\\PxHybridAuth\\Domain\\Model\Identity\\' . ucfirst($this->getServiceProvider()) . 'Identity';


        if (class_exists($identityClassName) && defined($identityClassName . '::EXTBASE_TYPE')) {
            $extbaseType = constant($identityClassName . '::EXTBASE_TYPE');
            $identityClause = 'deleted=0 AND hidden=0 AND identifier=' . $this->db->fullQuoteStr($identifier,
                    'tx_pxhybridauth_domain_model_identity') . ' AND ' . 'tx_extbase_type=' . $this->db->fullQuoteStr($extbaseType,
                    'tx_pxhybridauth_domain_model_identity');
            $socialIdentities = $this->db->exec_SELECTgetRows(
                '*',
                'tx_pxhybridauth_domain_model_identity',
                $identityClause
            );

            foreach ($socialIdentities as $socialIdentity) {
                if (isset($socialIdentity['fe_user'])) {
                    $dbUser = is_array($dbUserSetup) ? $dbUserSetup : $this->db_user;
                    // Look up the user by the username and/or extraWhere:
                    $dbres = $this->db->exec_SELECTquery('*', $dbUser['table'],
                        'uid' . '=' . $this->db->fullQuoteStr($socialIdentity['fe_user'],
                            $dbUser['table']) . $this->db->fullQuoteStr($dbUser['check_pid_clause'],
                            $dbUser['table']) . $dbUser['enable_clause'] . $extraWhere);
                    if ($dbres) {
                        $result = $this->db->sql_fetch_assoc($dbres);
                        $this->db->sql_free_result($dbres);
                        if ($result) {
                            break;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Should be implemented in child classes
     *
     * @return string
     */
    public function getServiceProvider()
    {
        return $this->provider;
    }
}