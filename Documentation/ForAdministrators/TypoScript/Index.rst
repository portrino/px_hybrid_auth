.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _configuration-typoscript:

TypoScript Configuration
^^^^^^^^^^^^^^^^^^^^^^^^

Include HybridAuth in Template
''''''''''''''''''''''''''''''

Please include the static template of *HybridAuth* either through an Include or through the General options.

.. figure:: ../../Images/Configuration/BackendIncludeStaticTemplate.png
    :width: 500px
    :align: left

| To include the HybridAuth directly in your template setup please use the following code:
| ``<INCLUDE_TYPOSCRIPT: source="FILE:EXT:px_hybrid_auth/Configuration/TypoScript/setup.txt">``
| ``<INCLUDE_TYPOSCRIPT: source="FILE:EXT:px_hybrid_auth/Configuration/TypoScript/constants.txt">``

TypoScript values
'''''''''''''''''

The following values can be defined through TypoScript. This options can be overriden with the flexform values of the plugin.

======================================  ==========  =================================================================================================  =======
TypoScript value                        Data type   Description                                                                                        Default
======================================  ==========  =================================================================================================  =======
persistence.storagePid                  string      Define the Storage Folder with the Website User Records, use comma seperated list or single value
persistence.recursive                   integer     The level of recursion
settings.redirectPageLoginError         PID         The page to be redirected to if an error occurs during login
settings.showLogoutFormAfterLogin       boolean     Display logout button after successful login                                                       false
settings.loginPid                       PID         The id of the login page.
======================================  ==========  =================================================================================================  =======

Example
'''''''