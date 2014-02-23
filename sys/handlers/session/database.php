<?php

namespace sys\handlers\session;

use SessionHandlerInterface;
use app\confs\Database as DatabaseConf;

class Database implements SessionHandlerInterface
{
	
	use \sys\traits\module\Database;

	const table__ = 'session';

	const id__ = 'sid';

	const data__ = 'data';

	const time__ = 'time';

	function __construct()
	{
	}

	public function open($savePath, $sessionName)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		$read = $this->database()->select('session', ['data'], 'WHERE sid = :sid', [':sid' => $id]);
		if ($read) {
			return base64_decode($read[0]->data);
		}
		return false;
	}

	public function write($id, $data)
	{
		$bind = [':sid' => $id, ':data' => base64_encode($data), ':time' => time()];
		$read = $this->database()->select('session', ['sid'], 'WHERE sid = :sid', [':sid' => $id]);
		
		if ($read) {
			$write = $this->database()->query('UPDATE session SET data = :data, time = :time WHERE sid = :sid', $bind);
		}
		else {
			$write = $this->database()->query('INSERT INTO session (sid, data, time) VALUES (:sid, :data, :time)', 
				$bind);
		}
		return (bool) $write->rowCount();
	}

	public function destroy($id)
	{
		$destroy = $this->database()->query('DELETE FROM session WHERE sid = :sid LIMIT 1', [':sid' => $id]);
		return (bool) $destroy->rowCount();
	}

	public function gc($maxLifeTime)
	{
		$destroy = $this->database()->query('DELETE FROM session WHERE time < :time', 
			[':time' => (time() - $maxLifeTime)]);
		return true;
	}

}