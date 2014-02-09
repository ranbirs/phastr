<?php

namespace sys;

use sys\modules\Assets;

class View
{
	
	use \sys\traits\Util;

	public $request, $response, $error, $type, $page, $body, $title, $callback;

	private $_assets;

	function __construct()
	{
	}

	public function assets()
	{
		return (isset($this->_assets)) ? $this->_assets : $this->_assets = new \sys\modules\Assets();
	}

	public function block($path)
	{
		return $this->_render($path, 'block');
	}

	public function request($path)
	{
		return $this->_render($path, 'request');
	}

	public function template($type, $path, $data = null)
	{
		return $this->_render($type . '/' . $path, 'template', [$type => $data]);
	}

	public function page($path = null)
	{
		$path = $this->util()->helper()->path($path, 'page');
		return $this->_render($path, 'page');
	}

	public function layout($path = null)
	{
		$file = $this->_resolveFile(($path) ? $path : \app\confs\Config::layout__, 'layout');
		$this->_includeFile($file);
		
		exit();
	}

	private function _render($path, $type = 'page', $data = null)
	{
		$file = $this->_resolveFile($path, $type);
		if (! $file) {
			return false;
		}
		ob_start();
		$this->_includeFile($file, $data);
		return ob_get_clean();
	}

	private function _resolveFile($path, $type = 'page')
	{
		$path = implode('/', (array) $path);
		return $this->util()->loader()->resolveFile('views/' . $type . 's/' . $path);
	}

	private function _includeFile($file, $data = null)
	{
		if (! is_null($data)) {
			extract($data);
		}
		include $file;
	}

}
