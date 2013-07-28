<?php

namespace sys;

use sys\Init;
use sys\components\Assets;
use sys\components\Loader;
use sys\components\Access;
use sys\utils\Helper;

class View {

	public $request, $response, $assets, $access, $error;

	function __construct()
	{
		$this->assets = new Assets();
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

	public function response($layout = \app\confs\sys\request_layout__)
	{
		$this->layout(\sys\components\Request::param__ . "/" . $layout);
	}

	public function layout($name)
	{
		$file = $this->_resolveFile($name, 'layout');
		if ($file) {
			$this->_includeFile($file);
		}
		else {
			trigger_error(\app\vocabs\sys\error\view_layout__);
		}
		exit();
	}

	public function error($code, $msg = "")
	{
		header(" ", true, $code);
		$this->error = (\app\confs\app\errors__) ? ((!empty($msg)) ? $msg : ((isset($this->error)) ? $this->error : "")) : "";
		$this->layout("error/" . $code);
	}

	private function _render($name, $type = 'page')
	{
		$file = $this->_resolveFile($name, $type);

		if (!$file) {
			$this->error(404, \app\vocabs\sys\error\view_render__);
		}
		ob_start();
		$this->_includeFile($file);

		if (isset($this->access[1])) {
			if (!Access::resolveRule($this->access[0], $this->access[1])) {
				ob_end_clean();
				$this->error(403);
			}
		}
		return ob_get_clean();
	}

	private function _resolveFile($name, $type = 'page')
	{
		return Loader::resolveFile("views/" . $type . "s/" . $name);
	}

	private function _includeFile($file)
	{
		include $file;
	}

}
