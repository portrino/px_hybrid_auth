<?php
namespace Portrino\PxHybridAuth\UserFunc;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tca
 *
 * @package Portrino\PxHybridAuth\UserFunc
 */
class Tca
{

    /**
     * @param $PA
     * @param $fObj
     *
     * @return mixed
     */
    public function showNoProvidersConfiguredMessage($PA, $fObj)
    {
        $content = GeneralUtility::makeInstance(FlashMessage::class,
            $GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:fe_users.message.no_configured_providers.body'),
            $GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:fe_users.message.no_configured_providers.header'),
            // the header is optional
            FlashMessage::WARNING,
            // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
            true // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
        )->render();
        return $content;
    }
}