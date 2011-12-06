<?php
	/* Wrapper for the Twitter oAuth API */

	require_once('OAuth.php');

	class Twitter extends Channel {
		private $consumer_key;
		private $consumer_secret;

		private $authenticated;

		public function __construct($consumer_key, $consumer_secret, $access_token = null) {
			$this->consumer_key = $consumer_key;
			$this->consumer_secret = $consumer_secret;

			if ($access_token) {
				$this->api = new TwitterAPI($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
				$this->screen_name = $access_token['screen_name'];
				$this->user_id = $access_token['user_id'];
				$this->authenticated = true;
			} else {
				$this->api = new TwitterAPI($consumer_key, $consumer_secret);
				$this->authenticated = false;
			}
		}

		/* The default action */
		public function _default(Writable $writable) {
			return $this->tweet($writable);
		}

		private function tweet(Writable $writable) {
			echo 'Tweeting ('.strlen($writable->output).'): '.$writable->output."\n";
			// $this->api->post('statuses/update', array('status' => utf8_encode($writable->output)));
			sleep(10);
		}

		public function isAuthenticated() {
			return $this->authenticated;
		}

		public function register() {
			return $this->api->getAuthorizationURL($this->api->getRequestToken());
		}

		public function validate($oauth_verifier) {
			$credentials = $this->api->getAccessToken($oauth_verifier);

			if (!isset($credentials['user_id'])) return false;
			else {
				$this->screen_name = $credentials['screen_name'];
				$this->user_id = $credentials['user_id'];
			}

			$this->authenticated = true;

			return $credentials;
		}

		public function authenticate() {
			if ($this->isAuthenticated()) {
				echo 'Instance authenticated as @'.$this->screen_name.' (#'.$this->user_id.')'."\n";
				return;
			}

			echo 'Please visit '.$this->register();
			echo ' and enter the PIN code here: ';

			if (!$credentials = $this->validate(trim(fgets(STDIN)))) die('Could not validate PIN, please try again...'."\n");

			echo 'This instance is authenticated as @'.$this->screen_name.' (#'.$this->user_id.')'."\n";

			echo 'I will now save your tokens. ';
			echo 'Input filename: (none) ';

			if (!$filename = trim(fgets(STDIN))) return;
			if ($out = file_put_contents($filename, serialize($credentials)))
				echo 'Your token has been saved to '.$filename.' ('.$out.' bytes written)'."\n";
			else echo 'There was a problem saving your token to '.$filename."\n";
		}
	}

	class TwitterAPI {
		const ACC_URL = 'https://api.twitter.com/oauth/access_token';
		const AUTH_URL = 'https://api.twitter.com/oauth/authorize';
		const REQ_URL = 'https://api.twitter.com/oauth/request_token';
		const API_URL = 'https://api.twitter.com/1/';

		private $signature_method;
		private $consumer;
		private $access_token;
		private $request_token;

		public function __construct($consumer_key, $consumer_secret, $oauth_token = null, $oauth_token_secret = null) {
			$this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();

			$this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
			if ($oauth_token and $oauth_token_secret) $this->access_token = new OAuthConsumer($oauth_token, $oauth_token_secret);
			else $this->access_token = NULL;
		}

		public function getRequestToken() {
			$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, 'GET', self::REQ_URL, null);
			$request->sign_request($this->signature_method, $this->consumer, $this->access_token);

			$this->request_token = OAuthUtil::parse_parameters($this->_request($request->to_url(), 'GET', null));
			$request_token = $this->request_token; /* Save unaltered for return value */

			$this->request_token = new OAuthConsumer($this->request_token['oauth_token'], $this->request_token['oauth_token_secret']);

			return $request_token;
		}

		public function getAuthorizationURL($request_token) {
			if (is_array($request_token)) {
				$token = $request_token['oauth_token'];
			} else return false;
			return self::AUTH_URL.'?oauth_token='.$token;
		}

		public function getAccessToken($oauth_verifier) {
			$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->request_token, 'GET', self::ACC_URL, array('oauth_verifier' => $oauth_verifier));
			$request->sign_request($this->signature_method, $this->consumer, $this->request_token);

			$this->access_token = OAuthUtil::parse_parameters($this->_request($request->to_url(), 'GET'));
			if (!isset($this->access_token['user_id'])) return false;

			$access_token = $this->access_token; /* Save unaltered for return value */

			$this->access_token = new OAuthConsumer($this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);

			return $access_token;
		}

		public function post($url, $data) {
			$url = self::API_URL.$url.'.json';

			$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, 'POST', $url, $data);
			$request->sign_request($this->signature_method, $this->consumer, $this->access_token);

			$response = OAuthUtil::parse_parameters($this->_request($request->get_normalized_http_url(), 'POST', $request->to_postdata()));

			return json_decode($response);
		}

		public function _request($url, $method, $data = null) {
			$handle = curl_init($url);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

			if ($method == 'POST') {
				curl_setopt($handle, CURLOPT_POST, TRUE);
				if (!empty($data)) curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
			}

			return curl_exec($handle);
		}
	}
?>
