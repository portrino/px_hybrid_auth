<?php
namespace Portrino\PxHybridAuth\Script\Interfaces;

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

/**
 * Interface UpdateScriptInterface
 *
 * @package Portrino\PxHybridAuth\Script\Interfaces
 */
interface UpdateScriptInterface {

    /**
     * Stub function for the extension manager
     *
     * @return	boolean	true to allow access
     */
    public function access();

    /**
     * Updates nested sets
     *
     * @return string
     */
    public function main();

}
