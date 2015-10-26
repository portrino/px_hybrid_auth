.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _for-administrators:

For Administrators
===================

Import
------

There are two ways of installing the extension. As described `here <https://wiki.typo3.org/Composer#Composer_Mode>`_

Import the extension to your server from the

- TYPO3 Extension Repository (TER) or
- via GIT

From TER (Classic Mode)
^^^^^^^^^^^^^^^^^^^^^^^

Select "*Get Extensions*" in the extension manager and update your extension list. Search for "px_hybrid_auth" and click "Import and Install" to get the latest version.
There are no other dependencies than TYPO3 6.2.

.. figure:: ../Images/Administration/GetExtensionHybridAuth.png
    :width: 500px
    :align: left

From GIT
^^^^^^^^

Please use the following command to get the extension from GIT.

.. code-block:: bash

    git clone https://github.com/portrino/px_hybrid_auth

Via composer
^^^^^^^^^^^^

Since TYPO3 7.x you are able to get extension via composer. As described `here <https://wiki.typo3.org/Composer#Composer_Mode>`_ here you just have to user TYPO3 in Composer Mode
and add this line to your require section within the composer.json file and run composer install / composer update.

.. code-block:: json

    "portrino/px_hybrid_auth": "dev-master",

If you want a specific version than change "dev-master" to the version you need.

.. note::

    While we want support Classic Mode TYPO3 Mode (Non Composer Mode) too, we ship the source code of `hybridauth <https://github.com/hybridauth/hybridauth>`_ directly within *Resources/Public/Php* folder.
    As soon composer become mandatory for TYPO3 Extensions and only this will be used for package management we add it as requirement to composer.json.

Install
-------

Wether you run your TYPO3 in Classic Mode or Composer Mode you should install the extension via ExtensionManager or via Composer. Click `here <https://wiki.typo3.org/Composer>`_ for more details

After the installation is finished open the Extension Configuration by clicking on the "Configure" gear.


Configure
---------
Please configure the extension after you have successfully installed it.

.. toctree::
   :maxdepth: 5
   :titlesonly:
   :glob:

   ExtensionManager/Index
   TypoScript/Index