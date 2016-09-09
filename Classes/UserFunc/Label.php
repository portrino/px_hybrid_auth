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

use \TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class Label
 *
 * @package Portrino\PxHybridAuth\UserFunc
 */
class Label
{

    function getIdentityLabel(&$params, &$pObj)
    {
        $id = $params['row']['uid'];
        $params['title'] = $params['row']['uid'];
        if ((int)$id > 0) {
            $row = BackendUtility::getRecord('tx_pxhybridauth_domain_model_identity', $id);
            if ($row != null) {
                $type = $GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:tx_pxhybridauth_domain_model_identity.tx_extbase_type.' . $row['tx_extbase_type']);
                if ($type) {
                    $params['title'] = $type . ': ' . $row['identifier'];
                }
            }
        }
    }
}
