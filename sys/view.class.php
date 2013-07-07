<?php

namespace sys;

use sys\components\Loader;
use sys\components\Access;
use sys\utils\Helper;
use sys\utils\Html;

class View {

<<<<<<< HEAD
	public $xhr_method = \app\confs\sys\xhr_method__;
	public $xhr_layout = \app\confs\sys\xhr_layout__;
	public $request, $response, $access, $error_msg;
	private static $assets;
=======
	public $request, $response, $request_method, $request_format, $error_msg;
	protected $access, $assets;
>>>>>>> d6a96e0a4e6f64cabab2fc6a9729eb94aa71ea4b

	function __construct()
	{

	}

	public function assets($type = 'script', $subj = null, $content = null, $append = \app\confs\app\iteration__)
	{
		if (is_null($subj) and is_null($content)) {
<<<<<<< HEAD
			if (!isset(self::$assets[$type]))
				self::$assets[$type] = array();
			return implode("\n", array_values(self::$assets[$type]));
		}
		$key = (!is_null($subj)) ? hash('md5', $subj) : hash('md5', $content);
		return self::$assets[$type][$key] = Html::getAsset($type, $subj, $content, $append);
=======
			if (!isset($this->assets[$type]))
				$this->assets[$type] = array();
			return implode("\n", array_values($this->assets[$type]));
		}
		$key = (!is_null($subj)) ? hash('md5', $subj) : hash('md5', $content);
		return $this->assets[$type][$key] = Html::getAsset($type, $subj, $content, $append);
>>>>>>> d6a96e0a4e6f64cabab2fc6a9729eb94aa71ea4b
	}

	public function block($name)
	{
		return $this->_render($name, 'block');
	}

	public function page($name = null)
	{
		if (is_null($name))
			$name = \sys\Res::controller() . "/" . Helper::getPath(\sys\Res::page(), 'tree');
		return $this->_render($name, 'page');
	}

<<<<<<< HEAD
=======
	public function request($name)
	{
		return $this->_render($name, 'request');
	}

>>>>>>> d6a96e0a4e6f64cabab2fc6a9729eb94aa71ea4b
	public function template($type, $name, $data = null)
	{
		$this->$type = $data;
		return $this->_render($type . "/" . $name, 'template');
	}


	public function request($name)
	{
		return $this->_render($name, 'request');
	}

	public function response($layout = \app\confs\sys\xhr_layout__)
	{
		$this->layout(\app\confs\sys\xhr_param__ . "/$layout");
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
