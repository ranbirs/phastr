<?php

namespace app\forms\index;

use sys\modules\Form;

class Service_form extends Form
{

    public function fields()
    {
        $this->input('test_service_field', ' ',
            $params = ['attr' => ['class' => 'form-control']]);

        $this->validate('test_service_field', 'require');

        $this->button('submit_button', 'Submit',
            $params = ['attr' => ['class' => ['btn', 'btn-primary']]]);

        // $this->expire(true);
    }

    public function submit($values = null, $status = null)
    {
        $this->load()->module('aes', 'app');
        $this->load()->module('oauth_consumer', 'app');

        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/index.php/provider/example-service-post-action';
        $oauth = [
            'consumer_key' => 'consumerx',
            'consumer_secret' => 'consumerx-secret',
            'token' => 'consumerx-token',
            'token_secret' => 'consumerx-token-secret'
        ];
        $params['iv'] = $this->aes->iv();
        $params['data'] = $this->aes->encrypt($values, 'consumerx-token-secret', $params['iv']);
        $response = $this->oauth_consumer->request($url, $params, $oauth, 'post');

        $this->message('Went through!', 'success');

        $this->callback('form_callback', serialize($response));
    }

}
