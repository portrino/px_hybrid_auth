<?php

namespace Portrino\PxHybridAuth\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 André Wuttig <wuttig@portrino.de>, portrino GmbH
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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

    // only take the px_lib abstract controller if it is installed
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('px_lib')) {
    class DynamicIdentityController extends \Portrino\PxLib\Controller\AbstractController {}
} else {
    class DynamicIdentityController extends \Portrino\PxHybridAuth\Controller\AbstractController {}
}

/**
 * Class IdentityController
 *
 * @package Portrino\PxHybridAuth\Controller
 */
class IdentityController extends DynamicIdentityController {

    /**
     * @var integer The uid of the current logged in user
     */
    protected $feUserUid;

    /**
     * @var \Portrino\PxHybridAuth\Domain\Model\User The object of the current logged in user
     */
    protected $feUserObj;

    /**
     * @var \Portrino\PxHybridAuth\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * SingleSignOnUtility
     *
     * @var \Portrino\PxHybridAuth\Utility\SingleSignOnUtility
     * @inject
     */
    protected $singleSignOnUtility;

    /**
     *
     */
    protected function initializeAction() {
        parent::initializeAction();
        $this->feUserUid = is_array($GLOBALS['TSFE']->fe_user->user) ? $GLOBALS['TSFE']->fe_user->user['uid'] : NULL;
        $this->feUserObj = $this->feUserUid ? $this->userRepository->findByIdentifier((int)$this->feUserUid) : NULL;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
        parent::initializeView($view);
        $this->view->assign('user', $this->feUserObj);
    }


    /**
     * list action
     *
     * @return void
     */
    public function listAction() {
        $socialProviders = array();
        foreach ($this->extConf['provider.'] as $provider => $config) {
            $provider = str_replace('.', '', $provider);
            if ((Boolean)$config['enabled']) {
                $socialProviders[$provider] = array(
                    'isConnected' => $this->feUserObj->isConnected($provider)
                );
            }
        }
        if (count($socialProviders) === 0) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('flash.warning.no_configured_providers.body', $this->extensionName),
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('flash.warning.no_configured_providers.header', $this->extensionName),
                \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING
            );
        }
        $this->view->assign('socialProviders', $socialProviders);
    }

    /**
     * create action
     *
     * @param \Portrino\PxHybridAuth\Domain\Model\Identity $identity
     * @ignorevalidation $identity
     * @return void
     */
    public function createAction(\Portrino\PxHybridAuth\Domain\Model\Identity $identity) {
        if ($this->feUserObj != NULL) {
            $return_url = $this->uriBuilder->getRequest()->getRequestUri();
            /** @var \Hybrid_User_Profile $socialUser */
            $socialUser = $this->singleSignOnUtility->authenticate($identity->getProvider(), $return_url);
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'beforeCreateAction', array($this, &$socialUser, $identity));

            if ($socialUser) {
                $identity->setIdentifier($socialUser->identifier);

                $this->feUserObj->addIdentity($identity);
                $this->userRepository->update($this->feUserObj);
            }

            $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterCreateAction', array($this, $socialUser, $identity));
        }
        $this->redirect('list','Identity');
    }

	/**
     * remove action
     *
     * @param string $identity
     * @ignorevalidation $identity
     * @return void
     */
	public function removeAction($identity) {
		$identity = strtolower($identity);

		$identities = $this->feUserObj->getIdentities();
		foreach ($identities as $singleIdentity) {
			/** @var \Portrino\PxHybridAuth\Domain\Model\Identity $singleIdentity */
			if (strtolower($singleIdentity->getProvider()) === $identity && $this->feUserObj->isConnected($identity)) {
				$this->feUserObj->removeIdentity($singleIdentity);
				$this->userRepository->update($this->feUserObj);
				$this->signalSlotDispatcher->dispatch(__CLASS__, 'afterRemoveAction', array($this, $singleIdentity));
			}
		}
		$this->redirect('list', 'Identity');
	}

	/**
	 * @return \Portrino\PxHybridAuth\Domain\Model\User
	 */
	public function getCurrentUser() {
		return $this->feUserObj;
	}
}
