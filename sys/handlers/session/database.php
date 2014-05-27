<?php

namespace sys\handlers\session;

use SessionHandlerInterface;

class Database implements SessionHandlerInterface
{
	
	use \sys\Loader;

	const table__ = 'session';

	const id__ = 'sid';

	const data__ = 'data';

	const time__ = 'time';

	const token__ = 'token';

	function __construct()
	{
		$this->load()->module('database');
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
		$read = $this->database->select('session', ['data'], 'WHERE sid = :sid', [':sid' => $session_id]);
		if ($read) {
			return json_decode(base64_decode($read[0]->data));
		}
		return false;
	}

	public function write($session_id, $session_data)
	{
		$query = 'INSERT INTO session (sid, data, time) VALUES (:sid, :data, :time) ' .
			 'ON DUPLICATE KEY UPDATE data = VALUES(data), time = VALUES(time)';
		$write = $this->database->query($query, 
			[':sid' => $session_id, ':data' => base64_encode(json_encode($session_data)), ':time' => time()]);
		return (bool) $write->rowCount();
	}

	public function destroy($session_id)
	{
		$destroy = $this->database->query('DELETE FROM session WHERE sid = :sid LIMIT 1', [':sid' => $session_id]);
		return (bool) $destroy->rowCount();
	}

	public function gc($maxlifetime)
	{
		$destroy = $this->database->query('DELETE FROM session WHERE time < :time', 
			[':time' => (time() - $maxlifetime)]);
		return true;
	}

}