<?php

namespace sys;

use sys\modules\Assets;
use sys\utils\Helper;
use sys\utils\Loader;

class View {

	public $request, $response, $assets, $error, $type, $page, $body, $title;

	function __construct()
	{
		$this->assets = new Assets;
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
		$path = Helper::getPath($path, 'page');
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
		return Loader::resolveFile('views/' . $type . 's/' . $path);
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
