<?php

namespace sys;

use sys\modules\Assets;

class View {

	use \sys\traits\Util;

	public $request, $response, $error, $type, $page, $body, $title;

	private $_assets;

	function __construct()
	{

	}

	public function assets()
	{
		return (isset($this->_assets)) ? $this->_assets : $this->_assets = new \sys\modules\Assets;
	}
	
	public function request($path)
	{
		return $this->_render($path, 'request');
	}

	public function block($path)
	{
		return $this->_render($path, 'block');
	}

	public function page($path = null)
	{
		$path = $this->util()->helper()->path($path, 'page');
		return $this->_render($path, 'page');
	}

	public function template($type, $path, $data = null)
	{
		$this->$type = $data;
		return $this->_render($type . '/' . $path, 'template');
	}

	public function layout($path = \app\confs\config\layout__)
	{
		$file = $this->_resolveFile($path, 'layout');
		$this->_includeFile($file, true);
		exit;
	}

	private function _render($path, $type = 'page')
	{
		$file = $this->_resolveFile($path, $type);

		if (!$file) {
			return false;
		}
		ob_start();
		$this->_includeFile($file);
		return ob_get_clean();
	}

	private function _resolveFile($path, $type = 'page')
	{
		if (is_array($path))
			$path = implode('/', $path);
		return $this->util()->loader()->resolveFile('views/' . $type . 's/' . $path);
	}

	private function _includeFile($file, $require = false)
	{
		if (!$require) {
			include $file;
		}
		else {
			require $file;
		}
	}

}
