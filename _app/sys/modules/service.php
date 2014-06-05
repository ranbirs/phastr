<?php

namespace sys\modules;

class Service
{
	
	use \sys\Loader;

	protected $handle, $headers, $response, $info, $header, $result;

	public function init($url)
	{
		if ($this->load()->module('validation')->validate($url, 'url')) {
			return $this->handle = curl_init($url);
		}
		return false;
	}

	public function request($url, $data = null, $method = 'post')
	{
		if (!$this->init($url)) {
			return false;
		}
		curl_setopt($this->handle, CURLOPT_HEADER, true);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		
		switch ($method) {
			case 'post':
				$request[$method] = $data;
				curl_setopt($this->handle, CURLOPT_POST, true);
				curl_setopt($this->handle, CURLOPT_POSTFIELDS, $request);
				break;
			case 'get':
				curl_setopt($this->handle, CURLOPT_HTTPGET, true);
				break;
			case 'put':
				curl_setopt($this->handle, CURLOPT_PUT, true);
				curl_setopt($this->handle, CURLOPT_BINARYTRANSFER, true);
				break;
			default:
				return false;
		}
		return true;
	}

	public function execute()
	{
		if (!isset($this->handle)) {
			return false;
		}
		$this->response = curl_exec($this->handle);
		$this->info = curl_getinfo($this->handle);
		$this->header = trim(substr($this->response, 0, $header_size = (int) $this->info['header_size']));
		$this->result = trim(substr($this->response, $header_size));
		
		curl_close($this->handle);
		
		return $this->result;
	}

	public function response()
	{
		if (!isset($this->result)) {
			return $this->execute();
		}
		return $this->result;
	}

	public function info($key = '')
	{
		if (!isset($this->info)) {
			return false;
		}
		return ($key) ? ((isset($this->info[$key])) ? $this->info[$key] : false) : $this->info;
	}

	public function header($key = '')
	{
		if (!isset($this->header)) {
			return false;
		}
		if (!is_array($this->header)) {
			$this->header = \sys\utils\helper\args(\sys\utils\helper\filter_split(eol__, $this->header));
		}
		return ($key) ? ((isset($this->header[$key])) ? $this->header[$key] : false) : $this->header;
	}

	public function setHeader($headers = [])
	{
		$headers = \sys\utils\helper\iterate_join(': ', $headers);
		$this->headers = array_merge((array) $this->headers, $headers);
		
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
	}

	public function setOpt($params = [])
	{
		foreach ($params as $option => $value) {
			curl_setopt($this->handle, $option, $value);
		}
	}

}