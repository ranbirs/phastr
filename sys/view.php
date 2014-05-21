<?php

namespace sys;

use app\confs\Config as __config;

class View
{
	
	use \sys\traits\Load;

	public $request, $response, $error, $type, $page, $body, $title, $callback;

	function __construct()
	{
		$this->load()->module('assets');
		$this->load()->util('path');
		$this->load()->util('html');
	}

	public function block($path)
	{
		return $this->render($this->filePath($path, 'block'));
	}

	public function request($path)
	{
		return $this->render($this->filePath($path, 'request'));
	}

	public function template($type, $path, $data = null)
	{
		return $this->render($this->filePath($type . '/' . $path, 'template'), [$type => $data]);
	}

	public function page($path = null)
	{
		if (($file = $this->resolveFile($this->path->page($path), 'page')) !== false) {
			return $this->render($file);
		}
		return false;
	}

	public function layout($path = null)
	{
		$file = $this->filePath(($path) ? $path : __config::layout__, 'layout');
		$this->includeFile($file);
		
		exit();
	}

	protected function render($file, $data = null)
	{
		ob_start();
		$this->includeFile($file, $data);
		return ob_get_clean();
	}

	protected function filePath($path, $type)
	{
		return $this->path->file('views/' . $type . 's/' . $path);
	}

	protected function resolveFile($path, $type = 'page')
	{
		return $this->path->resolve($this->filePath($path, $type));
	}

	protected function includeFile($file, $data = null)
	{
		if (!is_null($data)) {
			extract($data);
		}
		include $file;
	}

}
