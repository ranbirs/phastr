<?php

namespace sys\modules;

class Service
{

    public $handle, $headers, $response, $info, $header, $result;

    public function request($url, $data = null, $method = 'post')
    {
        $this->handle = curl_init($url);

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
        $this->response = curl_exec($this->handle);
        $this->info = curl_getinfo($this->handle);
        $this->header = trim(substr($this->response, 0, $header_size = (int)$this->info['header_size']));
        $this->header = \sys\utils\Helper::args(\sys\utils\Helper::filter_split(PHP_EOL, $this->header));
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

    public function setHeader($headers = [])
    {
        $headers = \sys\utils\Helper::iterate_join($headers, ': ');
        $this->headers = array_merge((array)$this->headers, $headers);

        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
    }

    public function setOpt($params = [])
    {
        foreach ($params as $option => $value) {
            curl_setopt($this->handle, $option, $value);
        }
    }

}