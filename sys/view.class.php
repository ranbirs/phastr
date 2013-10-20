<?php

namespace sys;

use sys\Init;
use sys\utils\Helper;

class View {

	use \sys\traits\Loader;

	public $request, $response, $error, $type, $page, $body, $title, $assets;

	function __construct()
	{

	}

	public function block($name)
	{
		return $this->_render($name, 'block');
	}

	public function page($name = null)
	{
		if (is_null($name))
			$name = Init::route()->controller() . "/" . Helper::getPath(Init::route()->page(), 'tree');
		return $this->_render($name, 'page');
	}

	public function template($type, $name, $data = null)
	{
		$this->$type = $data;
		return $this->_render($type . "/" . $name, 'template');
	}

	public function request($name)
	{
		return $this->_render($name, 'request');
	}

	public function response($layout = \app\confs\request\layout__)
	{
		$this->layout(\sys\modules\Request::param__ . "/" . $layout);
	}

	public function layout($name)
	{
		$file = $this->_resolveFile($name, 'layout');
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
		header(" ", true, $code);
		$this->error = (\app\confs\config\errors__) ? ((!empty($msg)) ? $msg : ((isset($this->error)) ? $this->error : "")) : "";
		$this->layout("error/" . $code);
	}

	private function _render($name, $type = 'page')
	{
		$file = $this->_resolveFile($name, $type);

		if (!$file) {
			return false;
		}
		ob_start();
		$this->_includeFile($file);
		return ob_get_clean();
	}

	private function _resolveFile($name, $type = 'page')
	{
		return $this->resolveFile("views/" . $type . "s/" . $name);
	}

	private function _includeFile($file)
	{
		include $file;
	}

}
