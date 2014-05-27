<?php

namespace sys;

use app\confs\Config as __config;

class View
{

	public function page($path = null)
	{
		return $this->render('pages/' . \sys\utils\path\page($path));
	}

	public function request($path)
	{
		return $this->render('requests/' . $path);
	}

	public function template($type, $path, $data = null)
	{
		return $this->render('templates/' . $type . '/' . $path, [$type => $data]);
	}

	public function layout($path = __config::layout__)
	{
		include \sys\utils\path\file('views/layouts/' . $path);
		
		exit();
	}

	public function render($path, $data = null)
	{
		$file = \sys\utils\path\file('views/' . $path);
		
		ob_start();
		
		if (!empty($data)) {
			extract($data);
		}
		include $file;
		
		return ob_get_clean();
	}

}
