<?php

namespace sys;

use sys\modules\Request;
use sys\modules\Assets;
use sys\utils\Helper;

class View {

	use \sys\traits\Loader;

	public $request, $response, $error, $type, $page, $body, $title, $assets;

	function __construct()
	{
		$this->assets = new Assets();
	}

	public function request($path)
	{
		return $this->_render($path, 'request');
	}

	public function response($layout = Request::layout__)
	{
		$this->layout(Request::param__ . "/" . $layout);
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
		return $this->_render($type . "/" . $path, 'template');
	}

	public function layout($path = \app\confs\config\layout__)
	{
		$file = $this->_resolveFile($path, 'layout');
		if ($file) {
			$this->_includeFile($file);
		}
		else {
			trigger_error(\sys\confs\error\view_layout__);
		}
		exit;
	}

	public function error($code, $msg = "")
	{
		$code = (int) $code;
		header(" ", true, $code);
		$this->error = (\app\confs\config\errors__) ?
			((!empty($msg)) ? $msg : ((isset($this->error)) ? $this->error : "")) :
			"";
		$this->layout("error/" . $code);
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
		return $this->resolveFile("views/" . $type . "s/" . $path);
	}

	private function _includeFile($file)
	{
		include $file;
	}

}
