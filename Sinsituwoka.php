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


		public function accessToken()
		{
			$client = new Client([
				// Base URI is used with relative requests
				'base_uri' => 'https://www.googleapis.com/oauth2/v4/token',
					// You can set any number of default request options.
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

		public function refresh()
		{
			$secret = file_get_contents('constants/client_secret.json');
			$secret_array = json_decode($secret);

			$token = file_get_contents('constants/access_token.json');
			$token_array = json_decode($token);

			$client_id = $secret_array->installed->client_id;
			$client_secret = $secret_array->installed->client_secret;
			$refresh_token = $token_array->refresh_token;

			$str_refresh_token = '"refresh_token='.$refresh_token.'"';
			$str_client_id = '"client_id='.$client_id.'"';
			$str_client_secret = '"client_secret='.$client_secret.'"';
			$str_grant_type = '"grant_type=refresh_token"';
			$refresh_url = 'https://www.googleapis.com/oauth2/v4/token';

			$str = 'curl --data '.$str_refresh_token.' --data '.$str_client_id.' --data '.$str_client_secret.' --data '.$str_grant_type.' '.$refresh_url;

			return $token_json = shell_exec($str);
		}


	}


	
