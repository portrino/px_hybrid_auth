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
      Portrino\\PxHybridAuth\\Service\\SocialLoginAuthenticationService
   :Name:
      returnUrl
   :File:
      SocialLoginAuthenticationService.php
   :Method:
      getUser()
   :Description:
      Slot is called after the returnUrl was build

 - :Class:
      Portrino\\PxHybridAuth\\Service\\SocialLoginAuthenticationService
   :Name:
      getUser
   :File:
      SocialLoginAuthenticationService.php
   :Method:
      getUser()
   :Description:
      Slot is called after the user object is created

 - :Class:
      Portrino\\PxHybridAuth\\Service\\SocialLoginAuthenticationService
   :Name:
      authUser
   :File:
      SocialLoginAuthenticationService.php
   :Method:
      authUser()
   :Description:
      Slot is called after social authentication is done

 - :Class:
      Portrino\\PxHybridAuth\\Controller\\AbstractUserController
   :Name:
      loginErrorBeforeRedirect
   :File:
      AbstractUserController.php
   :Method:
      initializeNewLoginAction()
   :Description:
      Slot is called if an error occurred during initializing the newLoginAction()

 - :Class:
      Portrino\\PxHybridAuth\\Controller\\IdentityController
   :Name:
      afterCreateAction
   :File:
      IdentityController.php
   :Method:
      createAction()
   :Description:
      Slot is called after create identity, before the redirect takes place

 - :Class:
      Portrino\\PxHybridAuth\\Controller\\IdentityController
   :Name:
      afterRemoveAction
   :File:
      IdentityController.php
   :Method:
      removeAction()
   :Description:
      Slot is called after remove identity, before the redirect takes place

Example
'''''''

ext_localconf.php

.. code-block:: php

  if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('px_hybrid_auth')) {

    $signalSlotDispatcher->connect(
        'Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService',
        'getUser',
        'Portrino\PxRegister\Slots\HybridAuthSlot',
        'getUser',
        FALSE
    );

    $signalSlotDispatcher->connect(
        'Portrino\PxHybridAuth\Service\SocialLoginAuthenticationService',
        'authUser',
        'Portrino\PxRegister\Slots\HybridAuthSlot',
        'authUser',
        FALSE
    );

    $signalSlotDispatcher->connect(
        'Portrino\PxHybridAuth\Controller\AbstractUserController',
        'loginErrorBeforeRedirect',
        'Portrino\PxRegister\Slots\HybridAuthSlot',
        'loginErrorBeforeRedirect',
        FALSE
    );
  }



HybridAuthSlot.php


.. code-block:: php

  /**
   * Class HybridAuthSlot
   *
   * @package Portrino\PxRegister\Slots
   */
  class HybridAuthSlot {

      /**
       * authUser
       *
       * @param array $user
       * @param int $result
       */
      public function authUser($user, &$result) {
          ...
      }

      /**
       * loginErrorBeforeRedirect
       *
       * @param \Portrino\PxHybridAuth\Controller\AbstractUserController $pObj
       * @param \TYPO3\CMS\Extbase\Mvc\Request $request
       */
      public function loginErrorBeforeRedirect($pObj, $request) {
          ...
      }
  }


