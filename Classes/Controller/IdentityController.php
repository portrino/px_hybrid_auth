<?php

namespace Portrino\PxHybridAuth\Controller;

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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class IdentityController
 *
 * @package Portrino\PxHybridAuth\Controller
 */
class IdentityController extends AbstractController
{

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
    protected function initializeAction()
    {
        parent::initializeAction();
        $this->feUserUid = is_array($GLOBALS['TSFE']->fe_user->user) ? $GLOBALS['TSFE']->fe_user->user['uid'] : null;
        $this->feUserObj = $this->feUserUid ? $this->userRepository->findByUid((int)$this->feUserUid) : null;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        $this->view->assign('user', $this->feUserObj);
    }


    /**
     * list action
     *
     * @return void
     */
    public function listAction()
    {
        $socialProviders = [];
        if ($this->feUserObj) {
            foreach ($this->extConf['provider.'] as $provider => $config) {
                $provider = str_replace('.', '', $provider);
                if ((Boolean)$config['enabled']) {
                    $socialProviders[$provider] = [
                        'isConnected' => $this->feUserObj->isConnected($provider)
                    ];
                }
            }
            if (count($socialProviders) === 0) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('flash.warning.no_configured_providers.body', $this->extensionName),
                    LocalizationUtility::translate('flash.warning.no_configured_providers.header',
                        $this->extensionName),
                    FlashMessage::WARNING
                );
            }
        }
        $this->view->assign('socialProviders', $socialProviders);
    }

    /**
     * create action
     *
     * @param \Portrino\PxHybridAuth\Domain\Model\Identity $identity
     * @ignorevalidation $identity
     *
     * @return void
     */
    public function createAction(\Portrino\PxHybridAuth\Domain\Model\Identity $identity = null)
    {
        if ($this->feUserObj != null) {
            $return_url = $this->uriBuilder->getRequest()->getRequestUri();
            /** @var \Hybrid_User_Profile $socialUser */
            $socialUser = $this->singleSignOnUtility->authenticate($identity->getProvider(), $return_url);
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'beforeCreateAction', [$this, &$socialUser, $identity]);

            if ($socialUser) {
                $identity->setIdentifier($socialUser->identifier);

                $this->feUserObj->addIdentity($identity);
                $this->userRepository->update($this->feUserObj);
            }

            $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterCreateAction', [$this, $socialUser, $identity]);
        }
        $this->redirect('list', 'Identity');
    }

    /**
     * remove action
     *
     * @param string $identity
     * @ignorevalidation $identity
     *
     * @return void
     */
    public function removeAction($identity)
    {
        $identity = strtolower($identity);

        $identities = $this->feUserObj->getIdentities();
        foreach ($identities as $singleIdentity) {
            /** @var \Portrino\PxHybridAuth\Domain\Model\Identity $singleIdentity */
            if (strtolower($singleIdentity->getProvider()) === $identity && $this->feUserObj->isConnected($identity)) {
                $this->feUserObj->removeIdentity($singleIdentity);
                $this->userRepository->update($this->feUserObj);
                $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterRemoveAction', [$this, $singleIdentity]);
            }
        }
        $this->redirect('list', 'Identity');
    }

    /**
     * @return \Portrino\PxHybridAuth\Domain\Model\User
     */
    public function getCurrentUser()
    {
        return $this->feUserObj;
    }
}
