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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class ProcessDataMapDispatcher
 *
 * @author Andre Wuttig <wuttig@portrino.de>
 * @package Portrino\PxHybridAuth\Hooks
 *
 * = Examples =
 *
 * <code title="Using ProcessDataMapDispatcher Signal in your Extension">
 *
 *   $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
 *
 *   $signalSlotDispatcher->connect(
 *     'tt_content',
 *     'updateRecordPostProcessFieldArray',
 *     'Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot',
 *     'preventEmptyFlexFormValues',
 *     FALSE
 *   );
 *
 *   $signalSlotDispatcher->connect(
 *     'tt_content',
 *     'newRecordPostProcessFieldArray',
 *     'Portrino\PxHybridAuth\Slots\ProcessDataMap\ContentSlot',
 *     'preventEmptyFlexFormValues',
 *     FALSE
 *   );
 *
 * </code>
 *
 */
class ProcessDataMapDispatcher
{

    /**
     * @param array $incomingFieldArray
     * @param string $table
     * @param int $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $dataHandler)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = $objectManager->get(Dispatcher::class);
        $signalSlotDispatcher->dispatch($table, 'PreProcessFieldArray', [&$incomingFieldArray, $id, $dataHandler]);
    }

    /**
     * @param string $status
     * @param string $table
     * @param int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $dataHandler)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = $objectManager->get(Dispatcher::class);
        $signalSlotDispatcher->dispatch($table, $status . 'RecordPostProcessFieldArray',
            [$id, &$fieldArray, $dataHandler]);
    }

    /**
     * @param string $status
     * @param string $table
     * @param int $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $dataHandler)
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = $objectManager->get(Dispatcher::class);
        // @deprecated: will be removed soon
        $signalSlotDispatcher->dispatch($table, $status . 'Record', [$id, &$fieldArray, $dataHandler]);
        $signalSlotDispatcher->dispatch($table, $status . 'RecordAfterDataBaseOperations',
            [$id, &$fieldArray, $dataHandler]);
    }

}