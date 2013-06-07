<?php

namespace app\models;

class User extends \sys\Model {

	function __construct()
	{
		parent::__construct();
	}

	public function login($email, $password)
	{
		$user = $this->db()->select('user', array('uid', 'role', 'authentication'),
			array(
				"WHERE email = :email AND status = :status",
				array('email' => $email, 'status' => 'active')
			)
		);
		if ($user) {
			$user = $user[0];
			if (!\sys\utils\Hash::resolve($user->authentication, $password)) {
				return false;
			}
			$this->db()->update('user',
				$data = array('sid' => $this->_sid),
				array("WHERE uid = :uid", array('uid' => $user->uid))
			);
			\sys\Res::session()->set(array('_user' => 'uid'), $user->uid);
			\sys\Res::session()->set(array('_user' => 'token'));
			return true;
		}
		return false;
	}

	public function register($name, $email, $password)
	{
		$user = $this->db()->select('user', array('uid'),
			array(
				"WHERE email = :email",
				array('email' => $email)
			)
		);
		if ($user) {
			return false;
		}
		$hash = \sys\utils\Hash::get($password);
		$token = \sys\utils\Hash::rand();
		$register = $this->db()->insert('user',
			array(
				'name' => $name,
				'email' => $email,
				'authentication' => $hash,
				'sid' => $token,
				'role' => 'guest',
				'status' => 'new'
			)
		);
		if ($register) {
			$host = $_SERVER['SERVER_NAME'];
			$addr = "noreply@$host";
			$from = \sys\utils\Conf::k('app\\title');
			$path = \sys\Res::get('path');
			$xid = \sys\Res::session()->xid();
			$headers = "From: $from <$addr>\n";
			$subject = \sys\utils\Vocab::t('user_register\\verify_email_subject');
			$msg = \sys\utils\Vocab::t('user_register\\verify_email_body') .
				"http://$host/$path/verify/$xid/$token/";

			mail($email, $subject, $msg, $headers, "-f $addr");
			return true;
		}
		return false;
	}

	public function verify($token)
	{
		$verify = $this->db()->select('user', array('uid'),
			array(
				"WHERE sid = :sid AND status = :status",
				array('sid' => $token, 'status' => 'new')
			)
		);
		if ($verify) {
			$user = $verify[0];
			$this->db()->update('user',
				$data = array('status' => 'active'),
				array("WHERE uid = :uid", array('uid' => $user->uid))
			);
			return true;
		}
		return false;
	}

}
