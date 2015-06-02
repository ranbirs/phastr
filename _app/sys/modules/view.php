<?php

namespace sys\modules;

class View
{

	public function view($path, $data = null, $ext = '.php')
	{
		return $this->render($path, $data, $ext);
	}

	public function layout($path, $data = null, $ext = '.php')
	{
		if (isset($data)) {
			extract($data);
		}
		require $path . $ext;
		
		exit();
	}

	public function render($path, $data = null, $ext = '')
	{
		ob_start();
		
		if (isset($data)) {
			extract($data);
		}
		include $path . $ext;
		
		return ob_get_clean();
	}

}
