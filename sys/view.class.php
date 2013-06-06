<?php

namespace sys;

use sys\Res;
use sys\utils\Helper;
use sys\utils\Html;

class View {

	private $_assets = array();

	function __construct()
	{

	}

	public function assets($type = 'script', $subj = null, $content = null, $append = \app\confs\app\iteration__)
	{
		if (!$subj and !$content) {
			if (!isset($this->_assets[$type]))
				$this->_assets[$type] = array();
			return implode("\n", array_values($this->_assets[$type]));
		}
		$key = ($subj) ? $subj : md5($content);
		if (!isset($this->_assets[$type][$key]))
			$this->_assets[$type][$key] = Html::getAsset($type, $subj, $content, $append);
		return true;
	}

	public function block($name)
	{
		return $this->_render($name, 'block');
	}

	public function page($name = null)
	{
		if (!$name) {
			$path = Helper::getPath(Res::get('controller'));
			$name = "$path/" . Helper::getPath(Res::get('page'), 'tree');
		}
		return $this->_render($name, 'page');
	}

	public function template($type, $name, $data = null)
	{
		$this->$type = $data;
		return $this->_render(array($type => $name), 'template');
	}

	public function layout($name)
	{
		$file = $this->_resolve($name, 'layout');
		if ($file) {
			$this->_include($file);
		}
		else {
			trigger_error(\app\vocabs\sys\er_vcl__);
		}
		exit();
	}

	public function error($code, $msg = "", $sys = true)
	{
		header(" ", true, $code);
		$this->sys_error_msg = $msg;
		$this->app_error_msg = (!$sys) ? $msg : "";
		$this->layout("error/$code");
	}

	private function _render($name = null, $type = 'page')
	{
		if (is_array($name)) {
			$control = current(array_keys($name));
			$name = current(array_values($name));
			$name = "{$control}s/$name";
		}
		$file = $this->_resolve($name, $type);

		if (!$file) {
			$this->error(404, \app\vocabs\sys\er_vcv__);
		}
		ob_start();
		$this->_include($file);

		if (isset($this->access[1])) {
			if (!$this->_access($this->access[0], $this->access[1])) {
				ob_end_clean();
				$this->error(403);
			}
		}
		return ob_get_clean();
	}

	private function _resolve($name = null, $type = 'page')
	{
		$path = \sys\app_base__ . "views/{$type}s/$name";
		return Helper::resolveFilePath($path);
	}

	private function _include($file)
	{
		include $file;
	}

	private function _access($rule, $role)
	{
		if (is_array($role)) {
			return false;
		}
		switch ($role) {
			case 'public':
				switch ($rule) {
					case 'deny':
						$access = (!is_null(Res::session()->token()));
						break 2;
				}
				$access = true;
				break;
			case 'private':
				switch ($rule) {
					case 'deny':
						$access = (is_null(Res::session()->token()));
						break 2;
				}
				$access = true;
				break;
			case 'role':
			default:
				$access = false;
		}
		return $access;
	}

}
