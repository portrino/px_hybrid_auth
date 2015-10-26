# PxHybridAuth Change log

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