<?php
/**
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html 
*/

/**
 * Hybrid_User_Profile object represents the current logged in user profile.
 * The list of fields available in the normalized user profile structure used by HybridAuth.
 *
 * The Hybrid_User_Profile object is populated with as much information about the user as
 * HybridAuth was able to pull from the given API or authentication provider.
 *
 * http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html
 */
class PxHybridAuth_Hybrid_User_Profile extends Hybrid_User_Profile {

    /**
     * Position
     *
     * @var String
     */
    public $position = NULL;

    /**
     * Company
     *
     * @var String
     */
    public $company = NULL;

    /**
     * Industry
     *
     * @var String
     */
    public $industry = NULL;
}
