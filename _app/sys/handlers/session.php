<?php

namespace sys\handlers\session;

use SessionHandlerInterface;
use sys\Loader;
// use sys\configs\Session as __session;
class Session implements SessionHandlerInterface
{
	
	use Loader;

	function __construct()
	{
		$this->load()->load('app/modules/database');
	}

	public function open($save_path, $name)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($session_id)
	{
		if (($read = $this->database->select('session', ['data'], 'WHERE sid = :sid', [':sid' => $session_id]))) {
			return json_decode(base64_decode(current($read)->data));
		}
		return false;
	}

	public function write($session_id, $session_data)
	{
		$query = 'INSERT INTO session (sid, data, time) VALUES (:sid, :data, :time) ON DUPLICATE KEY UPDATE data = VALUES(data), time = VALUES(time)';
		$write = $this->database->query($query, [':sid' => $session_id, ':data' => base64_encode(json_encode($session_data)), ':time' => time()]);
		return (bool) $write->rowCount();
	}

	public function destroy($session_id)
	{
		$destroy = $this->database->query('DELETE FROM session WHERE sid = :sid LIMIT 1', [':sid' => $session_id]);
		return (bool) $destroy->rowCount();
	}

	public function gc($maxlifetime)
	{
		$destroy = $this->database->query('DELETE FROM session WHERE time < :time', [':time' => (time() - $maxlifetime)]);
		return true;
	}

}