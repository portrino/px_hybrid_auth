<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 André Wuttig <wuttig@portrino.de>, portrino GmbH
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

// only take the px_lib abstract controller if it is installed

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('px_lib')) {
    abstract class DynamicAbstractUpdateScript extends \Portrino\PxLib\Script\AbstractUpdateScript  {}
} else {
    abstract class DynamicAbstractUpdateScript extends \Portrino\PxHybridAuth\Script\AbstractUpdateScript  {}
}

/**
 * Class ext_update
 */
class ext_update extends DynamicAbstractUpdateScript {

    /**
     * @var string
     */
    protected $extensionName = 'PxHybridAuth';

    /**
     * Returns the extensionName
     *
     * @return string
     */
    protected function getExtensionName() {
        return $this->extensionName;
    }

    protected function init() {
        parent::init();
        $this->status = array(
            'tableNames' => $this->tableNameUpdateStatus(),
            'columnNames' => $this->columnNameUpdateStatus(),
            'extbaseTypes' => $this->updateExtbaseTypesStatus(),
        );
    }

    /**
     *
     */
    private function tableNameUpdateStatus() {
        $result = self::OK;
        if (is_array($this->currentSchema) && array_key_exists('tx_pxregister_domain_model_identity', $this->currentSchema) && !array_key_exists('tx_pxhybridauth_domain_model_identity', $this->currentSchema)) {
            $result = self::INFO;
        }
        return $result;
    }

    /**
     * update table names action
     */
    public function updateTableNamesAction() {
            # Rename Table
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE tx_pxregister_domain_model_identity RENAME TO tx_pxhybridauth_domain_model_identity;');
        $this->redirectToAction();
    }

    /**
     *
     */
    private function columnNameUpdateStatus() {
        $result = self::OK;
        if (isset($this->currentSchema['fe_users']['fields'])) {
            $fieldSchema = $this->currentSchema['fe_users']['fields'];
            $oldFields = array(
                'tx_pxregister_identities' => 'tx_pxregister_identities',
            );
            $result = count(array_intersect_key($oldFields, $fieldSchema)) > 0 ? self::INFO : self::OK;
        }
        return $result;
    }

    /**
     * update table names action
     */
    public function updateColumnNamesAction() {
        # Rename Table
        $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE fe_users CHANGE tx_pxregister_identities tx_pxhybridauth_identities int(11) unsigned DEFAULT \'0\' NOT NULL;');

        $this->redirectToAction();
    }

    /**
     *
     */
    private function updateExtbaseTypesStatus() {
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'COUNT(uid)',
            'tx_pxhybridauth_domain_model_identity',
            'tx_extbase_type LIKE \'%PxRegister%\'');
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
        return (int)$row[0] > 0 ? self::INFO : self::OK;
    }

    /**
     * update table names action
     */
    public function updateExtbaseTypesAction() {
        # Rename tx_extbase_type
        $GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_pxhybridauth_domain_model_identity SET tx_extbase_type = replace( tx_extbase_type, \'PxRegister\', \'PxHybridAuth\')');
        $this->redirectToAction();
    }
}
