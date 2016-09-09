<?php
namespace Portrino\PxHybridAuth\ItemsProcFunc;

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

/**
 * Class Login
 *
 * @package Portrino\PxHybridAuth\ItemsProcFunc
 */
class Login
{

    public function listSwitchableControllerActions($config)
    {
        // load extConf
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
        $config['items'] = [];
        $i = 0;
        foreach ($extConf['provider.'] as $provider => $providerConfig) {
            $provider = strtolower(str_replace('.', '', $provider));
            if ((Boolean)$providerConfig['enabled']) {
                $config['items'][$i] = [
                    0 => $GLOBALS['LANG']->sL('LLL:EXT:px_hybrid_auth/Resources/Private/Language/locallang_db.xlf:' . $provider . '_user.new_login'),
                    1 => ucfirst($provider) . 'User->newLogin;'
                ];
                $i++;
            }
        }
        return $config;
    }
}

?>