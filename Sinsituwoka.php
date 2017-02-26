<?php
	namespace Sinsituwoka;

	require 'vendor/autoload.php';

	use GuzzleHttp\Client;
	class Sinsituwoka
	{

		public function __construct()
		{
			$this->client_secret = self::clientSecret();
			$this->client_id = $this->client_secret->installed->client_id;
			$this->client_secret_str = $this->client_secret->installed->client_secret;
			$this->redirect_uri = $this->client_secret->installed->redirect_uris[0];
			$this->uri = self::uri();
			$this->authorization_code = self::authorizationCode();
			$this->access_token = self::accessTokenFromLocal();
		}

		public function clientSecret()
		{
			$client_secret_json = file_get_contents('constants/client_secret.json');
			$client_secret_obj = json_decode($client_secret_json);
			return $client_secret_obj;
		}

		public function authorizationCode()
		{
			$authorization_code = rtrim(file_get_contents('constants/authorization_code'));
			return $authorization_code;
		}

		public function uri()
		{
			$scope = rtrim(file_get_contents('constants/scope'));

			$query = http_build_query(
				[
					'response_type' => 'code',
						'client_id' => $this->client_id,
						'redirect_uri' => $this->redirect_uri,
						'scope' => $scope,
						'access_type' => 'offline',
				]);
			$uri = 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
			return $uri;
		}


		public function accessTokenFromRemote()
		{
			$client = new Client([
				'base_uri' => 'https://www.googleapis.com/oauth2/v4/token',
					'timeout'  => 2.0,
			]);

			$response = $client->request('POST', '', [
				'form_params' => [
					'code' => $this->authorization_code,
						'client_id' => $this->client_id,
						'client_secret' => $this->client_secret_str,
						'redirect_uri' => $this->redirect_uri,
						'grant_type' => 'authorization_code',
						'access_type' => 'offline',
				]
			]);
			return $response->getBody();
		}

		public function accessTokenFromLocal()
		{
			$access_token_json = file_get_contents('constants/access_token.json');
			$access_token_obj = json_decode($access_token_json);
			return $access_token_obj;
		}

		public function refresh()
		{
			$secret = $this->client_secret;
			$access_token = self::accessTokenFromLocal();
			$refresh_token = $access_token->refresh_token;

			$client = new Client([
				'base_uri' => 'https://www.googleapis.com/oauth2/v4/token',
					'timeout'  => 2.0,
			]);

			$response = $client->request('POST', '', [
				'form_params' => [
					'refresh_token' => $refresh_token,
						'client_id' => $this->client_id,
						'client_secret' => $this->client_secret_str,
						'grant_type' => 'refresh_token',
				]
			]);
			$refreshed_token_json = $response->getBody();
			$refreshed_token_obj = json_decode($refreshed_token_json);
			return $refreshed_token_obj;
		}

		public function bearer()
		{
			$refreshed_token_obj = self::refresh();
			$bearer = $refreshed_token_obj->access_token;
			return $bearer;
		}

	}
