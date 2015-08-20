<?php
namespace Portrino\PxHybridAuth\Slots;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Andre Wuttig <wuttig@portrino.de>, portrino GmbH
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

/**
 * Class SocialLoginAuthenticationServiceSlot
 *
 * @package Portrino\PxHybridAuth\Slots
 */
class SocialLoginAuthenticationServiceSlot {

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $database;

    /**
     * Get global database connection
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection() {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * getUser
     *
     * @param array $user
     * @param \Hybrid_User_Profile $socialUser
     * @param \Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService $pObj
     */
    public function getUser(&$user, $socialUser, $pObj) {
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
        $this->database = $this->getDatabaseConnection();

        if (!isset($this->extConf['auto_fe_user_creation.']['storagePid'])) {
            throw new \Exception('[px_hybrid_auth]: No storagePid for new fe_user records given! Please configure it in the extension configuration');
        }

        if (!$user) {
            $autoCreatedUser = $this->createFrontendUserRecordFromSocialUser($socialUser, intval($this->extConf['auto_fe_user_creation.']['storagePid']));
            if($autoCreatedUser) {
                $identity = $this->addIdentityToFrontendUser($autoCreatedUser, $pObj->getServiceProvider(), $socialUser->identifier);
                if ($identity) {
                        // overwrite the user with call by reference
                    $user = $autoCreatedUser;
                } else {
                        // delete the auto created user
                    $this->database->exec_DELETEquery(
                        'fe_users',
                        'uid=' . intval($autoCreatedUser['uid'])
                    );
                }
            }
        }
    }

    /**
     * Creates a frontend user based on the data of the social user
     *
     * @param \Hybrid_User_Profile $socialUser
     * @param int $pid
     * @param string $header
     * @return array|FALSE the created user record or false if it is not possible
     */
    protected function createFrontendUserRecordFromSocialUser($socialUser, $pid) {
        $result = FALSE;
        if (isset($socialUser->email) || isset($socialUser->emailVerified)) {
                // we should load the TCA explicitly, because we are in authentication step and TCA could be not loaded yet
            if (!isset($GLOBALS['TCA']['fe_users'])) {
                \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadCachedTca();
            }
            /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
            $dataHandler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

            $email = ($socialUser->email) ? $socialUser->email : $socialUser->emailVerified;

            if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('saltedpasswords')) {
                /** @var \TYPO3\CMS\Saltedpasswords\Salt\SaltInterface $saltedpasswordsInstance */
                $saltedpasswordsInstance = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance();
                $password = $saltedpasswordsInstance->getHashedPassword(uniqid());
            } else {
                $password = md5(uniqid());
            }
            $this->database->exec_INSERTquery(
                'fe_users',
                array(
                    'pid' => $pid,
                    'username' => $email,
                    'password' => $password,
                    'usergroup' => 1,
                    'email' => $email,
                    'first_name' => $socialUser->firstName,
                    'last_name' => $socialUser->lastName,
                    'disable' => 0,
                    'deleted' => 0,
                    'tstamp' => time(),
                    'crdate' => time(),
                )
            );
            $id = $this->database->sql_insert_id();
            $username = $dataHandler->getUnique('fe_users','username', $email, $id);
            if ($username != $email) {
                $this->database->exec_UPDATEquery('fe_users', 'uid=' . intval($id), array('username' => $username));
            }
            $where = 'uid=' . intval($id);
            $result = $this->database->exec_SELECTgetSingleRow('*', 'fe_users', $where);
        }
        return $result;
    }

    /**
     * Creates a frontend user based on the data of the social user
     *
     * @param array $user
     * @param string $provider
     * @param string $identifier
     * @return array|FALSE the created identity record or false if it is not possible
     */
    protected function addIdentityToFrontendUser($user, $provider, $identifier) {
        $result = FALSE;
        $identityClassName = 'Portrino\\PxHybridAuth\\Domain\\Model\Identity\\' . $provider . 'Identity';
        if (class_exists($identityClassName) && defined($identityClassName . '::EXTBASE_TYPE')) {
            $extbaseType = constant($identityClassName . '::EXTBASE_TYPE');

            $this->database->exec_INSERTquery(
                'tx_pxhybridauth_domain_model_identity',
                array(
                    'pid' => $user['pid'],
                    'tx_extbase_type' => $extbaseType,
                    'identifier' => $identifier,
                    'fe_user' => $user['uid'],
                    'hidden' => 0,
                    'deleted' => 0,
                    'tstamp' => time(),
                    'crdate' => time(),
                )
            );
            $id = $this->database->sql_insert_id();
            $where = 'uid=' . intval($id);
            $result = $this->database->exec_SELECTgetSingleRow('*', 'tx_pxhybridauth_domain_model_identity', $where);
            if ($result) {
                $this->database->exec_UPDATEquery('fe_users', 'uid=' . intval($user['uid']), array('tx_pxhybridauth_identities' => 1));
            }
        }
        return $result;
    }

}