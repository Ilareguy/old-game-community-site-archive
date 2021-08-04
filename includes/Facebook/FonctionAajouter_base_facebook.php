public function getExtendedAccessToken(){

    try {
        // need to circumvent json_decode by calling _oauthRequest
          // directly, since response isn't JSON format.
        $access_token_response =
            $this->_oauthRequest(
                $this->getUrl('graph', '/oauth/access_token'), array(
                    'client_id' => $this->getAppId(),
                    'client_secret' => $this->getAppSecret(),
                    'grant_type'=>'fb_exchange_token',
                    'fb_exchange_token'=>$this->getAccessToken()
                )
            );
    } catch (FacebookApiException $e) {
      // most likely that user very recently revoked authorization.
      // In any event, we don't have an access token, so say so.
      return false;
    }

    if (empty($access_token_response)) {
      return false;
    }

    $response_params = array();
    parse_str($access_token_response, $response_params);
    if (!isset($response_params['access_token'])) {
      return false;
    }

    return $response_params['access_token'];
}