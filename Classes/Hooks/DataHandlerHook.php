<?php
namespace Portrino\PxHybridAuth\Hooks;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Axel Boeswetter <boeswetter@portrino.de>, portrino GmbH
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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DataHandlerHook
 *
 * @package Portrino\PxHybridAuth\Hooks
 */
class DataHandlerHook
{

    /**
     * @var string
     */
    protected $CType = 'px_hybrid_auth_login';

    /**
     * @param string $status
     * @param string $table
     * @param int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$dataHandler)
    {
        if ($table == 'tt_content' && ($status == 'new' || $status == 'update')) {
            $this->preventEmptyFlexFormValues($id, $fieldArray, $dataHandler);
        }
    }

    /**
     * @param int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     *
     * @return void
     */
    protected function preventEmptyFlexFormValues($id, &$fieldArray, &$dataHandler)
    {
        $content = BackendUtility::getRecord('tt_content', $id);
        /** @var FlexFormTools $flexFormTools */
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);

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
                    $fieldArray['pi_flexform'] = $flexFormTools->flexArray2Xml($flexformData, true);
                }
            }
        }
    }
}
