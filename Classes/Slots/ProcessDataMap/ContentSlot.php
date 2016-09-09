<?php
namespace Portrino\PxHybridAuth\Slots\ProcessDataMap;

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
use \TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class ContentSlot
 *
 * @package Portrino\PxHybridAuth\Slots\ProcessDataMap
 */
class ContentSlot
{

    /**
     * @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools
     * @inject
     */
    protected $flexFormTools;

    protected $CType = 'px_hybrid_auth_login';

    /**
     * @param int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     *
     * @return void
     */
    public function preventEmptyFlexFormValues($id, &$fieldArray, $dataHandler)
    {
        $content = BackendUtility::getRecord('tt_content', $id);
        if (isset($fieldArray) && $fieldArray['CType'] === $this->CType || $content['CType'] === $this->CType) {
            if (isset($fieldArray['pi_flexform'])) {
                $flexformData = GeneralUtility::xml2array($fieldArray['pi_flexform']);
                if (isset($flexformData['data'])) {
                    foreach ($flexformData['data'] as $sheet => $fields) {
                        foreach ($fields['lDEF'] as $fieldKey => $fieldValue) {
                            if (strpos($fieldKey, 'settings.') !== false && $fieldValue['vDEF'] === '') {
                                unset($flexformData['data'][$sheet]['lDEF'][$fieldKey]);
                            }
                        }
                        if (isset($flexformData['data'][$sheet]['lDEF']) && $flexformData['data'][$sheet]['lDEF'] === []) {
                            unset($flexformData['data'][$sheet]);
                        }
                    }
                    $fieldArray['pi_flexform'] = $this->flexFormTools->flexArray2Xml($flexformData, true);
                }
            }
        }
    }
}