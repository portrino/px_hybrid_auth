<?php
/**
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html 
*/

/**
 * The Hybrid_User class represents the current logged in user
 */
class PxHybridAuth_Hybrid_User extends Hybrid_User {

	function __construct() {
		parent::__construct();

		$this->profile = = new PxHybridAuth_Hybrid_User_Profile();
	}

}
