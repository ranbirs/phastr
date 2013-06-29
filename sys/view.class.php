<?php

namespace sys;

use sys\Res;
use sys\components\Loader;
use sys\components\Access;
use sys\utils\Helper;
use sys\utils\Html;

class View {

	public $request, $response, $error_msg;
	protected $access, $assets;

	function __construct()
	{

	}

	public function assets($type = 'script', $subj = null, $content = null, $append = \app\confs\app\iteration__)
	{
		if (is_null($subj) and is_null($content)) {
			if (!isset($this->assets[$type]))
				$this->assets[$type] = array();
			return implode("\n", array_values($this->assets[$type]));
		}
		$key = (!is_null($subj)) ? hash('md5', $subj) : hash('md5', $content);
		if (!isset($this->assets[$type][$key]))
			$this->assets[$type][$key] = Html::getAsset($type, $subj, $content, $append);
		return true;
	}

	public function block($name)
	{
		return $this->_render($name, 'block');
	}

	public function page($name = null)
	{
		if (is_null($name))
			$name = Res::controller() . "/" . Helper::getPath(Res::page(), 'tree');
		return $this->_render($name, 'page');
	}

	public function request($type, $name)
	{
		return $this->_render($type . "/" . $name, 'request');
	}

	public function template($type, $name, $data = null)
	{
		$this->$type = $data;
		return $this->_render($type . "/" . $name, 'template');
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
		$this->error_msg = (\app\confs\app\error_msg__) ? $msg : "";
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
