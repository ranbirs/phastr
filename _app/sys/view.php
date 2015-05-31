<?php

namespace sys;

class View
{

	public function view($path, $data = null)
	{
		return $this->render($path, $data);
	}

	public function layout($path, $data = null)
	{
		if (isset($data)) {
			extract($data);
		}
		require $path . '.php';
		
		exit();
	}

	protected function render($path, $data = null)
	{
		ob_start();
		
		if (isset($data)) {
			extract($data);
		}
		include $path . '.php';
		
		return ob_get_clean();
	}

}
