<?php
namespace Portrino\PxHybridAuth\Domain\Model;

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
use Portrino\PxHybridAuth\Domain\Model\Identity;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class User
 *
 * @package Portrino\PxHybridAuth\Domain\Model
 */
class User extends FrontendUser
{

    /**
     * identities
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Portrino\PxHybridAuth\Domain\Model\Identity>
     * @cascade remove
     */
    protected $identities = null;

    public function initializeObject()
    {
    }

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->identities = new ObjectStorage();
    }

    /**
     * Adds a Identity
     *
     * @param \Portrino\PxHybridAuth\Domain\Model\Identity $identity
     *
     * @return void
     */
    public function addIdentity(Identity $identity)
    {
        $this->identities->attach($identity);
    }

    /**
     * Removes a Identity
     *
     * @param \Portrino\PxHybridAuth\Domain\Model\Identity $identityToRemove The Identity to be removed
     *
     * @return void
     */
    public function removeIdentity(Identity $identityToRemove)
    {
        $this->identities->detach($identityToRemove);
    }

    /**
     * Returns the identities
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Portrino\PxHybridAuth\Domain\Model\Identity> $identities
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * Sets the identities
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Portrino\PxHybridAuth\Domain\Model\Identity> $identities
     *
     * @return void
     */
    public function setIdentities(ObjectStorage $identities)
    {
        $this->identities = $identities;
    }

    /**
     * check if the user is connected with the given provider (if a identity exists for the given provider)
     *
     * @param string $provider
     *
     * @return boolean
     */
    public function isConnected($provider)
    {
        $result = false;
        /** @var \Portrino\PxHybridAuth\Domain\Model\Identity $identity */
        foreach ($this->getIdentities() as $identity) {
            if (strtolower($identity->getExtbaseType()) === strtolower('Tx_PxHybridAuth_Domain_Model_Identity_' . $provider . 'Identity')) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}