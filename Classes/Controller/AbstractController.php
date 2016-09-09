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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class AbstractController
 *
 * @package Portrino\PxHybridAuth\Controller
 */
abstract class AbstractController extends ActionController
{

    /**
     * @var \DateTime The current time
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $extConf = [];

    /**
     * contains the ts settings for the current action
     *
     * @var array
     */
    protected $actionSettings = [];

    /**
     * contains the specific ts settings for the current controller
     *
     * @var array
     */
    protected $controllerSettings = [];

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager = null;

    /**
     * @var int
     */
    protected $currentPageUid;

    /**
     * Initializes the controller before invoking an action method.
     *
     * Override this method to solve tasks which all actions have in
     * common.
     *
     * @return void
     */
    protected function initializeAction()
    {
        parent::initializeAction();
        $this->dateTime = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName)]);
        $this->controllerSettings = $this->settings['controllers'][$this->request->getControllerName()];
        $this->actionSettings = $this->controllerSettings['actions'][$this->request->getControllerActionName()];
        $this->currentPageUid = $GLOBALS['TSFE']->id;
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        $this->view->assignMultiple([
            'controllerSettings' => $this->controllerSettings,
            'actionSettings' => $this->actionSettings,
            'extConf' => $this->extConf,
            'currentPageUid' => $this->currentPageUid,
            'dateTime' => $this->dateTime
        ]);
    }

    /**
     * redirects to page
     *
     * @param null $pageUid
     * @param array $additionalParams
     * @param int $pageType
     * @param bool $noCache
     * @param bool $noCacheHash
     * @param string $section
     * @param bool $linkAccessRestrictedPages
     * @param bool $absolute
     * @param bool $addQueryString
     * @param array $argumentsToBeExcludedFromQueryString
     */
    protected function redirectToPage(
        $pageUid = null,
        array $additionalParams = [],
        $pageType = 0,
        $noCache = false,
        $noCacheHash = false,
        $section = '',
        $linkAccessRestrictedPages = false,
        $absolute = false,
        $addQueryString = false,
        array $argumentsToBeExcludedFromQueryString = []
    ) {
        $uri = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setUseCacheHash(!$noCacheHash)
            ->setSection($section)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->build();

        $this->redirectToUri($uri);
    }

    /**
     * Deactivate FlashMessages for erros
     *
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::getErrorFlashMessage()
     */
//    protected function getErrorFlashMessage() {
//        return FALSE;
//    }

    /**
     * Returns the current view
     *
     * @return \TYPO3\CMS\Extbase\Mvc\View\ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }
}