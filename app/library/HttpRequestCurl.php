<?php

use \Phalcon\Http\Request;

/**
 * Implement CURL to send or receive data url json rest.
 * 
 * @author Julián Molina - julian.molina@datia.co
 * @version 0.1
 * @copyright Logisticapp.sa 
 */
class HttpRequestCurl extends Request
{

	/**
	 * This a fork to getJsonRawBody change to read post parameters.
	 * 
	 * 
	 */
	public function getJsonPost($associative = null)
	{
		$whilePost = array();
		if ($this->isPost()) {

			$post = $this->getPost();
			foreach ($post as $key => $value) {
				if ($value == "null") {
					$whilePost[$key] = "";
				} else if (gettype($value) == "array") {	
					$whilePost[$key] = $this->getPost($key);
				} else {
					$whilePost[$key] = $this->getPost($key, gettype($value));
				}
			}
		}
		
		return (object) $whilePost;
	}

	/**
	 * 
	 */
	public function sendRestService($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$serverOutput = curl_exec ($ch);
		curl_close ($ch);
		return $serverOutput;
	}
}