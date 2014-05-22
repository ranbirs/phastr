<?php

namespace sys;

use app\confs\Config as __config;

class View
{
	
	use \sys\Loader;

	function __construct()
	{
		$this->load()->util('path');
	}

	public function page($path = null)
	{
		return $this->render('views/pages/' . $this->path->page($path));
	}

	public function block($path)
	{
		return $this->render('views/blocks/' . $path);
	}

	public function request($path)
	{
		return $this->render('views/requests/' . $path);
	}

	public function template($type, $path, $data = null)
	{
		return $this->render('views/templates/' . $type . '/' . $path, [$type => $data]);
	}

	public function layout($path = __config::layout__)
	{
		include $this->path->file('views/layouts/' . $path);
		
		exit();
	}

	protected function render($path, $data = null)
	{
		$file = $this->path->file($path);
		
		ob_start();
		
		if (!is_null($data)) {
			extract($data);
		}
		include $file;
		
		return ob_get_clean();
	}

}
