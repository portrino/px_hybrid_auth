# PxHybridAuth Changelog

3.1.0 - 2017-03-23
------------------
* [TASK] Updates hybridauth/hybdridauth package to 2.8.3-dev which is not released yet but necessary to get facebook 
  login working

3.0.3 - 2017-02-24
------------------
* [BUGFIX][CLEANUP] renames ProcessDataMapDispatcher hook class to DataHandlerHook and moves ContentSlot logic into it

3.0.2 - 2016-12-16
------------------
* [BUGFIX] adds additional property `loginError` to `SocialLoginAuthenticationService` which is used in 
  `isServiceresponsible()` to prevent redirect loops after a login error occured

3.0.1 - 2016-11-04
------------------
* [BUGFIX] changes logic to retrieve the position via XING provider 
  * we now take the title field at first and then the description field
 
3.0.0 - 2016-09-12
------------------
* [TASK] switch to PSR1/2 code style 
* [TASK] replaces traditional literal array syntax with shorthand syntax
* [TASK] code refactoring with code some code inspection tools
* [FEATURE] replaces old icons with SVG and uses new TYPO3 `IconRegistry` 
* [BUGIX] adapts facebook adapter to work with new facebook API 
* [TASK] general refactoring of social login adapters for better update compatibility of hybridauth library
* [FEATURE] adds typeicon_column and typeicon_classes for identity model

2.1.0 - 2015-10-19
------------------
* [FEATURE] adds slot for `returnUrlNoUser` in `SocialLoginAuthenticationService`
* [FEATURE] adds slot for `beforeCreationSlot` in `IdentityController`
* [BUGFIX] only add identity if third party user exists in `IdentityController`

2.0.0 - 2015-10-19
------------------
* adapts PxHybridAuth to work with next TYPO3 LTS Version 7.x 
* refactors class loading in conjunction
** we support installation via "Classic Mode" (https://wiki.typo3.org/Composer#Classic_Mode) and also the modern way via Composer (https://wiki.typo3.org/Composer#Composer_Mode)
** we also add 'ext_autoload.php' for legacy support of the "old" autoloading mechanism
* we decouple our own overriden HybridAuth-Adapters from hybridAuth vendor package which is located under Resources/Public/Php for legacy purposes
** this makes it even easier to integrate updates from vendor package "hybridauth/hybridauth"
** for now we are not able to add "hybridauth/h