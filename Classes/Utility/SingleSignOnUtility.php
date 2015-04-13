<?php
namespace Portrino\PxHybridAuth\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 André Wuttig <wuttig@portrino.de>, portrino GmbH
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
 * Class SingleSignOnUtility
 *
 * @package Portrino\PxHybridAuth\Utility
 */
class SingleSignOnUtility  {

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $extConf;

    /**
     * initializeObject
     */
    public function initializeObject() {
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['px_hybrid_auth']);
        $this->config = array(
                'base_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/?type=1410157426&no_cache=1',
                'providers' => array (
                    'Facebook' => array (
                        'enabled' =>  $this->extConf['provider.']['facebook.']['enabled'],
                        'keys'    => array (
                            'id' => $this->extConf['provider.']['facebook.']['id'],
                            'secret' => $this->extConf['provider.']['facebook.']['secret']
                        ),
                        'scope'   => 'email',
                        'display' => 'page'
                    ),
                    'LinkedIn' => array (
                        'enabled' => $this->extConf['provider.']['linkedin.']['enabled'],
                        'keys'    => array (
                            'key' => $this->extConf['provider.']['linkedin.']['key'],
                            'secret' => $this->extConf['provider.']['linkedin.']['secret']
                        ),
                        'scope'   => 'email',
                        'display' => 'page'
                    ),
                    'XING' => array (
                        'enabled' => $this->extConf['provider.']['xing.']['enabled'],
                        'keys'    => array (
                            'key' => $this->extConf['provider.']['xing.']['key'],
                            'secret' => $this->extConf['provider.']['xing.']['secret']
                        ),
                        'scope'   => 'email',
                        'display' => 'page'
                    )
                ),
                'debug_mode' => (boolean)$this->extConf['debug_mode'],
                'debug_file' => $this->extConf['debug_file'],
        );

    }

    /**
     * @param string $provider
     * @param string $returnTo
     *
     *  @return \Hybrid_User_Profile|FALSE
     */
    public function authenticate($provider, $returnTo) {
        try{
            $hybridauth = new \Hybrid_Auth($this->config);
            $service = $hybridauth->authenticate($provider,
                array(
                    'hauth_return_to' => $returnTo
                )
            );
            $socialUser = $service->getUserProfile();

        } catch( \Exception $exception ){
            switch ($exception->getCode()) {
                case 0:
                    $error = 'Unspecified error.';
                    break;
                case 1:
                    $error = 'Hybriauth configuration error.';
                    break;
                case 2:
                    $error = 'Provider not properly configured.';
                    break;
                case 3:
                    $error = 'Unknown or disabled provider.'; break;
                    break;
                case 4:
                    $error = 'Missing provider application credentials.'; break;
                    break;
                case 5:
                    $error = 'User has cancelled the authentication or the provider refused the connection.';
                case 6:
                    $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
                case 7:
                    $error = 'User not connected to the provider.';
                    break;
            }
        }
        if ($socialUser) {
            return $socialUser;
        } else {
            return FALSE;
        }
    }

    /**
     * logout from all providers when typo3 logout takes place
     */
    public function logout() {
        (new \Hybrid_Auth($this->config))->logoutAllProviders();
    }

}