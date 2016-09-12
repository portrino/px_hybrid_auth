<?php
namespace Portrino\PxHybridAuth\Hooks;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class LogOffHook
 *
 * @package Portrino\PxHybridAuth\Hooks
 */
class LogOffHook
{

    /**
     * Object manager
     *
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @param array $_params
     * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $pObj
     */
    public function postProcessing($_params, $pObj)
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var SingleSignOnUtility $singleSignOnUtility */
        $singleSignOnUtility = $this->objectManager->get(SingleSignOnUtility::class);
        $singleSignOnUtility->logout();
        $pObj->removeCookie('PHPSESSID');
    }
}