.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _for-developers:

For Developers
==============

The *PxHybridAuth* extension is designed in a way which should make it easy to integrate other social providers by developers.


Signal slots
^^^^^^^^^^^^

HybridAuth offers a the following SignalSlots (Extbase pendant to Hooks) to extend the functions from your extension.

.. t3-field-list-table::
 :header-rows: 1

 - :Class:
      Signal Class Name
   :Name:
      Signal Name
   :File:
      Located in File
   :Method:
      Located in Method
   :Description:
      Description

 - :Class:
      Portrino\\PxHybridAuth\\Service\\SocialLoginAuthenticationServiceSlot
   :Name:
      getUser
   :File:
      SocialLoginAuthenticationServiceSlot.php
   :Method:
      getUser()
   :Description:
    Slot is called after the user object is created

 - :Class:
      Portrino\\PxHybridAuth\\Service\\SocialLoginAuthenticationServiceSlot
   :Name:
      authUser
   :File:
      SocialLoginAuthenticationServiceSlot.php
   :Method:
      authUser()
   :Description:
      Slot is called after social authentication is done

 - :Class:
      Portrino\\PxHybridAuth\\Controller;
   :Name:
      loginErrorBeforeRedirect
   :File:
      AbstractUserController.php
   :Method:
      initializeNewLoginAction()
   :Description:
      Slot is called if an error occured during initializing the newLoginAction()





