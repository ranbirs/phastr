<?php

namespace app\services;

class Example_service
{

    use \sys\Loader;

    public function get_action($request, $consumer)
    {
        $this->load()->load('app/modules/aes');
        $data = $this->aes->decrypt($request['data'], $consumer['token_secret'], $request['iv']);

        return $this->response(200, ['decrypted' => $data]);
    }

    public function post_action($request, $consumer)
    {
        $this->load()->load('app/modules/aes');
        $data = $this->aes->decrypt($request['data'], $consumer['token_secret'], $request['iv']);

        return $this->response(200, ['decrypted' => $data]);
    }

    protected function validate()
    {
        // @todo
        /*if ($method !== $_SERVER['REQUEST_METHOD']) {
            throw new OAuthException('request_method', OAUTH_CONSUMER_KEY_REFUSED);
        }
        if (!in_array($endpoint, (array) $consumer['endpoint'])) {
            throw new OAuthException('endpoint', OAUTH_CONSUMER_KEY_REFUSED);
        }
        if (!in_array($_SERVER['REMOTE_ADDR'], (array) $consumer['host'])) {
            throw new OAuthException('remote_host', OAUTH_CONSUMER_KEY_REFUSED);
        }*/
    }

    protected function response($status = 200, $data = null)
    {
        http_response_code($status);
        return ['status' => $status, 'data' => $data];
    }

}
