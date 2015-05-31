<?php

namespace sys\modules;

use sys\configs\Hash as __hash;

class Hash
{

    public function gen($data = null, $algo = __hash::algo__, $key = __hash::key__)
    {
        return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
    }

    public function cipher($data = null)
    {
        return crypt($data, __hash::cipher__ . __hash::cost__ . '$' . $this->rand(__hash::salt__));
    }

    public function resolve($hash, $data = null, $algo = __hash::algo__, $key = __hash::key__)
    {
        if ($algo) {
            $subj = $this->gen($data, $algo, $key);
        } else {
            $subj = crypt($data, substr($hash, 0, strlen(__hash::cipher__ . __hash::cost__) + 1 + __hash::salt__));
        }
        return ($hash === $subj);
    }

    public function rand($length = 16, $algo = null, $chars = __hash::chars__)
    {
        $limit = strlen($chars) - 1;
        $rand = '';

        for ($i = 0; $i < (int)$length; $i++) {
            $index = mt_rand(0, $limit);
            $rand .= $chars[$index];
        }
        return (!$algo) ? $rand : hash($algo, uniqid($rand, true));
    }

}
