<?php

namespace sys;

use sys\utils\Helper;
use sys\utils\Loader;

class Load {

	public function model($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Loader::resolveInclude($path, 'model', true, app__, 'model.php');
	}

	public function service($path, $data = null)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Loader::resolveInclude($path, 'service', true, app__, 'service.php');
	}

	public function form($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Loader::resolveInclude($path, 'form', true, app__, 'form.php');
	}

	public function nav($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Loader::resolveInclude($path, 'nav', true, app__, 'nav.php');
	}

	public function module($path, $base = app__)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Loader::resolveInclude($path, 'module', true, $base, ($base == app__) ? 'module.php' : 'class.php');
	}

	public function controller($path)
	{
		return Loader::resolveInclude($path, 'controller', true, app__, 'controller.php');
	}

	public function conf($path, $base = app__)
	{
		return Loader::resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = '')
	{
		return Loader::resolveInclude(($lang) ? $lang . '/' . $path : $path, 'vocab', false);
	}

}
