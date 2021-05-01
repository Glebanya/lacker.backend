<?php

namespace App\Notification;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Notificator
{
	protected Client $client;

	public function __construct()
	{
		$this->client = new Client([
			'base_uri' => 'https://fcm.googleapis.com/fcm/send',
			'timeout' => 5,
			'allow_redirects' => false,
		]);
	}

	public function notify()
	{
		try
		{
			$this->client->request('POST', 'https://fcm.googleapis.com/fcm/send');
		}
		catch (GuzzleException $e)
		{
		}
	}
}