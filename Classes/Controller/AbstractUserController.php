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
use Portrino\PxHybridAuth\Controller\AbstractController;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Class AbstractUserController
 *
 * @package Portrino\PxHybridAuth\Controller
 */
class AbstractUserController extends AbstractController
{

    public function initializeNewLoginAction()
    {
        $redirectUrl = ($this->request->hasArgument('redirect_url')) ? $this->request->getArgument('redirect_url') : null;
        $redirectPid = ($this->request->hasArgument('redirect_pid')) ? $this->request->getArgument('redirect_pid') : null;
        // handle the redirects
        if ($redirectUrl) {
            $this->redirectToUri($redirectUrl);
        }
        if ($redirectPid) {
            $this->redirectToPage($redirectPid);
        }
        $loginError = ($this->request->hasArgument('login_error')) ? $this->request->getArgument('login_error') : null;
        // handle the redirect
        if ($loginError) {
            $this->signalSlotDispatcher->dispatch(__CLASS__, 'loginErrorBeforeRedirect', [$this, $this->request]);
            if ($this->settings['redirectPageLoginError']) {
                $this->redirectToPage($this->settings['redirectPageLoginError']);
            }
        }
    }

    /**
     * action newLogin
     *
     * @return void
     */
    public function newLoginAction()
    {
        $returnUrl = GeneralUtility::_GP('return_url') ? GeneralUtility::_GP('return_url') : null;
        // only if no return_url given use the redirectPageLogin from TS/Flexform settings
        if (!$returnUrl) {
            // if no redirectPageLogin from TS/Flexform was given use the loginPid from ext_conf
            $returnPid = $this->settings['redirectPageLogin'] ? $this->settings['redirectPageLogin'] : $this->extConf['basic.']['login_pid'];
        }
        if ($this->settings['storagePid']) {
            $storagePids = $this->getPidList($this->settings['storagePid'], (int)$this->settings['recursive']);
        } else {
            $pids = $GLOBALS['TSFE']->getStorageSiterootPids();
            $storagePids = $pids['_STORAGE_PID'];
        }


        $this->view->assign('redirect_url', $returnUrl);
        $this->view->assign('redirect_pid', $returnPid);
        $this->view->assign('pid', $storagePids);
        $this->view->assign('settings', $this->settings);
    }

    /**
     * Returns a commalist of page ids for a query (eg. 'WHERE pid IN (...)')
     *
     * @param string $pid_list A comma list of page ids (if empty current page is used)
     * @param integer $recursive An integer >=0 telling how deep to dig for pids under each entry in $pid_list
     *
     * @return string List of PID values (comma separated)
     */
    protected function getPidList($pid_list, $recursive = 0)
    {
        $cObjData = $this->configurationManager->getContentObject();
        if (!strcmp($pid_list, '')) {
            $pid_list = $GLOBALS['TSFE']->id;
        }
        $recursive = MathUtility::forceIntegerInRange($recursive, 0);
        $pid_list_arr = array_unique(GeneralUtility::trimExplode(',', $pid_list, true));
        $pid_list = [];
        foreach ($pid_list_arr as $val) {
            $val = MathUtility::forceIntegerInRange($val, 0);
            if ($val) {
                $_list = $cObjData->getTreeList(-1 * $val, $recursive);
                if ($_list) {
                    $pid_list[] = $_list;
                }
            }
        }
        return implode(',', $pid_list);
    }
}