<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_LinkedIn provider adapter based on OAuth1 protocol
 *
 * Hybrid_Providers_LinkedIn use linkedinPHP library created by fiftyMission Inc.
 *
 * http://hybridauth.sourceforge.net/userguide/IDProvider_info_LinkedIn.html
 */
class PxHybridAuth_Providers_LinkedIn extends Hybrid_Providers_LinkedIn {

	function __construct($providerId, $config, $params = NULL) {
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(class_exists('PxHybridAuth_Hybrid_User'));
		exit;
	}


	/**
	 * load the user profile from the IDp api client
	 */
	function getUserProfile() {
		try {
			// http://developer.linkedin.com/docs/DOC-1061
			$response = $this->api->profile('~:(id,first-name,last-name,public-profile-url,picture-url,picture-urls::(original),email-address,date-of-birth,phone-numbers,summary,positions,location,industry,)');
		}
		catch( LinkedInException $e ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an error: $e", 6 );
		}

		if( isset( $response['success'] ) && $response['success'] === TRUE ){
			$data = @ new SimpleXMLElement( $response['linkedin'] );

			if ( ! is_object( $data ) ){
				throw new Exception( "User profile request failed! {$this->providerId} returned an invalid xml data.", 6 );
			}

			$this->user->profile->identifier  = (string) $data->{'id'};
			$this->user->profile->firstName   = (string) $data->{'first-name'};
			$this->user->profile->lastName    = (string) $data->{'last-name'};
			$this->user->profile->displayName = trim( $this->user->profile->firstName . " " . $this->user->profile->lastName );

			$this->user->profile->email         = (string) $data->{'email-address'};
			$this->user->profile->emailVerified = (string) $data->{'email-address'};

			$this->user->profile->photoURL    = (string) $data->{'picture-url'};

			if ($data->{'picture-urls'}) {
				$pictureUrls = $data->{'picture-urls'};
				$attributes = $data->{'picture-urls'}->attributes();
				if (isset($attributes['total']) && (int)$attributes['total'] > 0 ) {
					foreach ($pictureUrls as $pictureUrl) {
						$this->user->profile->photoURL = $pictureUrl->{'picture-url'};
					}
				}
			}

			$this->user->profile->profileURL  = (string) $data->{'public-profile-url'};
			$this->user->profile->description = (string) $data->{'summary'};

			if( $data->{'phone-numbers'} && $data->{'phone-numbers'}->{'phone-number'} ){
				$this->user->profile->phone = (string) $data->{'phone-numbers'}->{'phone-number'}->{'phone-number'};
			}
			else{
				$this->user->profile->phone = null;
			}

			if( $data->{'date-of-birth'} ){
				$this->user->profile->birthDay   = (string) $data->{'date-of-birth'}->day;
				$this->user->profile->birthMonth = (string) $data->{'date-of-birth'}->month;
				$this->user->profile->birthYear  = (string) $data->{'date-of-birth'}->year;
			}

			if ($data->{'positions'}) {
				$positions = $data->{'positions'};
				$attributes = $data->{'positions'}->attributes();
				if (isset($attributes['total']) && (int)$attributes['total'] > 0 ) {
					foreach ($positions->{'position'} as $position) {
						if ((string) $position->{'is-current'} == 'true') {
							$this->user->profile->position = (string)$position->title;
							if ($position->company) {
								$this->user->profile->company = (string)$position->company->name;
							}
							$this->user->profile->position = (string)$position->title;
						}
					}
				}
			}

			if ($data->{'industry'}) {
				$this->user->profile->industry = (string)$data->{'industry'};
			}

			if ($data->{'location'}) {
				$location = $data->{'location'};
				$this->user->profile->city = (string)$location->name;
			}


			return $this->user->profile;



		}
		else {
			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
		}
	}

}
