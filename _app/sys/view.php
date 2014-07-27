<?php

namespace sys;

use app\confs\Config as __config;

class View
{

	public $layout = __config::layout__;

	public function page($path = null)
	{
		return $this->render('pages/' . \sys\utils\path\page($path));
	}

	public function request($path)
	{
		return $this->render('requests/' . $path);
	}

	public function template($subj, $path, $data = null)
	{
		return $this->render('templates/' . $subj . '/' . $path, [$subj => $data]);
	}

	public function layout($path = null)
	{
		include \sys\utils\path\file('views/layouts/' . ((!$path) ? $this->layout : $path));
		
		exit();
	}

	public function render($path, $data = null)
	{
		$file = \sys\utils\path\file('views/' . $path);
		
		ob_start();
		
		if ($data) {
			extract($data);
		}
		include $file;
		
		return ob_get_clean();
	}

}
