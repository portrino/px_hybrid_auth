<?php
namespace Portrino\PxHybridAuth\Hooks;

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
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageLayoutViewDrawItemHook
 *
 * @package Portrino\PxHybridAuth\Hooks
 */
class PageLayoutViewDrawItemHook implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{

    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param boolean $drawItem Whether to draw the item using the default functionalities
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     *
     * @return void
     */
    public function preProcess(
        \TYPO3\CMS\Backend\View\PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        if ($row['CType'] !== 'px_hybrid_auth_login') {
            return;
        }
        $drawItem = false;
        $data = GeneralUtility::xml2array($row['pi_flexform']);
        $headerContent = '<strong>' . htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tt_content.CType.px_hybrid_auth_login')) . '</strong><br />';
        if (is_array($data) && isset($data['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'])) {
            $provider = strtolower(str_replace('User->newLogin;', '',
                $data['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF']));
            if ($provider) {
                $headerContent .= htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:' . $provider . '_user.new_login')) . '<br />';
            }
        }
    }
}
