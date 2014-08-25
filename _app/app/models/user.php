<?php

namespace app\models;

class User extends \sys\Model
{

	const table__ = 'user';

	const id__ = 'uid';

	const auth__ = 'authentication';

	const role__ = 'role';

	const status__ = 'status';

	const session_id__ = 'sid';

	function __construct()
	{
		$this->load()->init('route');
		$this->load()->module('database');
		$this->load()->module('session');
		$this->load()->module('hash');
		$this->load()->module('config', 'app');
		$this->load()->module('vocab', 'app');
	}

	public function token($id)
	{
		$user = $this->database->select('user', ['uid', 'sid'], 'WHERE uid = :uid', [':uid' => $id]);
		if ($user) {
			return current($user)->sid;
		}
		return false;
	}

	public function login($email, $password)
	{
		$user = $this->database->select('user', ['uid', 'role', 'authentication'], 
			'WHERE email = :email AND status = :status', [':email' => $email, ':status' => 'active']);
		if ($user) {
			$user = current($user);
			if (!$this->hash->resolve($user->authentication, $password, 'sha512')) {
				return false;
			}
			$this->database->update('user', ['sid' => ':sid'], 'WHERE uid = :uid', 
				[':uid' => $user->uid, ':sid' => $this->session->token()]);
			$this->session->set(['user' => 'id'], $user->uid);
			$this->session->set(['user' => 'token'], $this->hash->rand(16, 'sha256'));
			return true;
		}
		return false;
	}

	public function register($name, $email, $password)
	{
		$user = $this->database->select('user', ['uid'], 'WHERE email = :email', [':email' => $email]);
		if ($user) {
			return false;
		}
		$hash = $this->hash->gen($password, 'sha512');
		$token = $this->hash->rand(16, 'sha256');
		$register = $this->database->insert('user', 
			['name' => ':name', 'email' => ':email', 'authentication' => ':authentication', 'sid' => ':sid', 
				'role' => ':role', 'status' => ':status'], 
			[':name' => $name, ':email' => $email, ':authentication' => $hash, ':sid' => $token, ':role' => 'guest', 
				':status' => 'new']);
		if ($register) {
			$host = $_SERVER['SERVER_NAME'];
			$addr = 'noreply@' . $host;
			$from = $this->conf->k('title');
			$path = $this->route->path();
			$headers = 'From: ' . $from . ' <' . $addr . '>' . eol__;
			$subject = $this->vocab->t('register_verify_email_subject', 'user');
			$msg = $this->vocab->t('register_verify_email_body', 'user') . eol__ . 'http://' . $host . '/' . $path .
				 '/verify/' . $this->session->key() . '/' . $token . '/';
			mail($email, $subject, $msg, $headers, '-f ' . $addr);
			return true;
		}
		return false;
	}

	public function verify($token)
	{
		$verify = $this->database->select('user', ['uid'], 'WHERE sid = :sid AND status = :status', 
			[':sid' => $token, ':status' => 'new']);
		if ($verify) {
			$user = current($verify);
			$this->database->update('user', ['status' => ':status'], 'WHERE uid = :uid', 
				[':uid' => $user->uid, ':status' => 'active']);
			return true;
		}
		return false;
	}

}
