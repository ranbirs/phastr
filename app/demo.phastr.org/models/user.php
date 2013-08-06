<?php

namespace app\models;

class User extends \sys\Model {

	function __construct()
	{
		parent::__construct();

		\sys\Load::vocab('user', 'en');
	}

	public function login($email, $password)
	{
		$user = $this->database()->select('user', array('uid', 'role', 'authentication'),
			"WHERE email = :email AND status = :status",
			array('email' => $email, 'status' => 'active')
		);
		if ($user) {
			$user = $user[0];
			if (!\sys\utils\Hash::resolve($user->authentication, $password)) {
				return false;
			}
			$this->database()->update('user', array('sid' => $this->_sid),
				"WHERE uid = :uid",
				array('uid' => $user->uid)
			);
			\sys\Init::session()->set(array('_user' => 'uid'), $user->uid);
			\sys\Init::session()->set(array('_user' => 'token', \sys\utils\Hash::rand()));
			return true;
		}
		return false;
	}

	public function register($name, $email, $password)
	{
		$user = $this->database()->select('user', array('uid'),
			"WHERE email = :email",
			array('email' => $email)
		);
		if ($user) {
			return false;
		}
		$hash = \sys\utils\Hash::get($password);
		$token = \sys\utils\Hash::rand();
		$register = $this->database()->insert('user',
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
			$from = \sys\utils\Conf::k('title');
			$path = \sys\Init::route()->path();
			$xid = \sys\Init::session()->xid();
			$headers = "From: $from <$addr>\n";
			$subject = \sys\utils\Vocab::t('register_verify_email_subject', 'user');
			$msg = \sys\utils\Vocab::t('register_verify_email_body', 'user') .
				"http://$host/$path/verify/$xid/$token/";

			mail($email, $subject, $msg, $headers, "-f $addr");
			return true;
		}
		return false;
	}

	public function verify($token)
	{
		$verify = $this->database()->select('user', array('uid'),
			"WHERE sid = :sid AND status = :status",
			array('sid' => $token, 'status' => 'new')
		);
		if ($verify) {
			$user = $verify[0];
			$this->database()->update('user', array('status' => 'active'),
				"WHERE uid = :uid", array('uid' => $user->uid)
			);
			return true;
		}
		return false;
	}

}
