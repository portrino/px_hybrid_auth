<?php
namespace Portrino\PxHybridAuth\Script;
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractUpdateScript
 *
 * @package Portrino\PxHybridAuth\Script
 */
abstract class AbstractUpdateScript implements \Portrino\PxLib\Script\Interfaces\UpdateScriptInterface  {

    const OK = 'OK';
    const INFO = 'INFO';
    const ERROR = 'ERROR';

    /**
     * Object manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Install\Service\SqlSchemaMigrationService $schemaMigrationService
     */
    protected $schemaMigrationService;
    /**
     * @var \TYPO3\CMS\Fluid\View\StandaloneView
     * @inject
     */
    protected $view;

    /**
     * @var string
     */
    protected $extensionName;

    /**
     * @var array
     */
    protected $currentSchema;

    /**
     * @var array
     */
    protected $status;

    /**
     * Stub function for the extension manager
     *
     * @return    boolean    true to allow access
     */
    protected function init() {
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->schemaMigrationService = $this->objectManager->get('TYPO3\\CMS\\Install\\Service\\SqlSchemaMigrationService');
        $this->currentSchema = $this->schemaMigrationService->getFieldDefinitions_database();
    }

    /**
     * Stub function for the extension manager
     *
     * @return    boolean    true to allow access
     */
    public function access() {
        $this->init();
        $result = FALSE;
        foreach($this->status as $state) {
            if ($state != self::OK) {
                $result = TRUE;
                break;
            }
        }
        return $result;
    }

    /**
     * main method
     *
     * @return string
     */
    public function main() {
        $this->view = $this->objectManager->get('\\TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $this->view->getRequest()->setControllerExtensionName($this->getExtensionName());
        $this->view->getRequest()->setPluginName('UpdateScript');
        $this->view->getRequest()->setControllerName('UpdateScript');
        $this->view->setLayoutRootPaths($this->getTemplateFolders('layout'));
        $this->view->setPartialRootPaths($this->getTemplateFolders('partial'));
        $this->view->assign('requestUri', GeneralUtility::getIndpEnv('REQUEST_URI'));

        $action = GeneralUtility::_GP('action') ? GeneralUtility::_GP('action') : 'Main';
        if (is_callable(array($this, $action . 'Action'))) {
            $this->view->setTemplatePathAndFilename(
                $this->getTemplatePath('UpdateScript/' . ucfirst($action) .'.html')
            );
            $content = call_user_func_array(array($this, $action . 'Action'), array());
        }
        return $content;
    }

    /**
     * Calls the mainAction
     *
     * @return string
     */
    public function mainAction() {
        $this->init();
        $this->view->assign('status', $this->status);
        return $this->view->render();
    }

    /**
     * Returns the extensionName
     *
     * @return string
     */
    abstract protected function getExtensionName();

    /**
     * Get absolute paths for templates with fallback
     *
     * @param string $part "template", "partial", "layout"
     * @return array
     */
    protected function getTemplateFolders($part = 'template') {
        $templatePaths = array(
            10 => 'EXT:' . GeneralUtility::camelCaseToLowerCaseUnderscored($this->getExtensionName()) . '/Resources/Private/' . ucfirst($part) . 's/'
        );
        $absoluteTemplatePaths = array();
        foreach($templatePaths as $key => $templatePath) {
            $absoluteTemplatePaths[$key] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePath);
        }
        return $absoluteTemplatePaths;
    }

    /**
     * Return path and filename for a file
     * 		respect *RootPaths and *RootPath
     *
     * @param string $relativePathAndFilename e.g. Email/Name.html
     * @param string $part "template", "partial", "layout"
     * @return string
     */
    protected function getTemplatePath($relativePathAndFilename, $part = 'template') {
        $absolutePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
            'EXT:' . GeneralUtility::camelCaseToLowerCaseUnderscored($this->getExtensionName()) . '/Resources/Private/' . ucfirst($part) . 's/' . $relativePathAndFilename
        );
        return $absolutePathAndFilename;
    }

    protected function redirectToAction($action = 'main') {
        $urlParts = parse_url(GeneralUtility::getIndpEnv('REQUEST_URI'));
        parse_str($urlParts['query'], $queryParts);
        $queryParts['action'] = $action;
        $urlParts['query'] = http_build_query($queryParts);
        $redirectUrl = \TYPO3\CMS\Core\Utility\HttpUtility::buildUrl($urlParts);
        header('Location: '. $redirectUrl);
        exit;
    }

}

?>