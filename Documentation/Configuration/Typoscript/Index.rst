.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _configuration-typoscript:

TypoScript
^^^^^^^^^^

Include HybridAuth in Template
''''''''''''''''''''''''''''''

Please include the static template of *HybridAuth* either through an Include or through the General options.

.. figure:: ../../Images/Configuration/BackendIncludeStaticTemplate.png
    :width: 500px
    :align: left

| To include the HybridAuth directly in your template setup please use the following code:
| ``<INCLUDE_TYPOSCRIPT: source="FILE:EXT:px_hybrid_auth/Configuration/TypoScript/setup.ts">``

TypoScript values
'''''''''''''''''

The following values can be defined through TypoScript.

.. warning:: Korrekt? -  This options can be overriden with the settings of the extension manager.

.. warning:: Tabelle prüfen

==========================  ==========  ============
TypoScript value            Data type   Description
==========================  ==========  ============
storagePid                  integer     The id of the page where the fe_users are stored.
recursive                   integer     The level of recursion
redirectPageLoginError      integer     The page where the user is redirected after an error. Probably the same like the login pid.
showLogoutFormAfterLogin    integer
loginPid                    integer     The id of the login page.
==========================  ==========  ============
