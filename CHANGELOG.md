# PxHybridAuth Change log

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
** for now we are not able to add "hybridauth/hybridauth" as dependency to 'composer.json', because older versions need the hybridauth php code anyway
* we also integrate features and bugfixes from the fork of georg ringer (https://github.com/georgringer/px_hybrid_auth)  