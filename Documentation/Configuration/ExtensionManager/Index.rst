.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _configuration-extension-manager:

Extension Manager
^^^^^^^^^^^^^^^^^

After you have installed the extension successfully (:ref:`administration`) you need to configure *PxHybridAuth*. In the different tabs you can

- enable the social provider
- enter the AppId and
- and secret key.

.. warning::

    Das kann ich nicht ganz interpretieren:
    You need to create your own Login Apps on the desired social networks for each new website.

Currently the following social providers are supported out-of-the-box:

- Facebook
- LinkedIn
- Xing

.. note::

    The *PxHybridAuth* extension is designed in a way which should make it easy to integrate other social providers by developers.


Basic
'''''

.. figure:: ../../Images/Configuration/ConfigureBasic.png
    :width: 500px
    :align: left

.. important:: Set the Pid of your Login page.

If there is no Login url stored in the session of the user this page will be used for the login.

If you enable the debug mode for Single SignOn (SSO) you can get additional information if the login to a social provider through PxHybridAuth does not work. You can also define the location of a file where this debug information are stored.

Facebook
''''''''

.. figure:: ../../Images/Configuration/ConfigureFacebook.png
    :width: 500px
    :align: left

In this tab you have the following options:

- Enable login through Facebook
- Define the Facebook AppId
- Define the App Secret which is used to generate access tokens

LinkedIn
''''''''

.. figure:: ../../Images/Configuration/ConfigureLinkedIn.png
    :width: 500px
    :align: left

In this tab you have the following options:

- Enable login through LinkedIn
- Define the LinkedIn Key
- Define the LinkedIn Secret

Xing
''''

.. figure:: ../../Images/Configuration/ConfigureXing.png
    :width: 500px
    :align: left

In this tab you have the following options:

- Enable login through Xing
- Define the Xing Key
- Define the Xing Secret

Automatic feuser creation
'''''''''''''''''''''''''

If you enable this feature a frontend user will be automatically created if no fe_user can be found for the given social identity. Be aware that this feature is still **experimental**.

.. figure:: ../../Images/Configuration/ConfigureAutomaticFeuserCreation.png
    :width: 500px
    :align: left

Please define also a page id where the created users should be stored.