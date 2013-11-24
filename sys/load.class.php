<?php

namespace sys;

use sys\utils\Helper;

class Load {

	use \sys\traits\Loader;

	public function model($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'model', true, app__, 'model.php');
	}

	public function service($path, $data = null)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'service', true, app__, 'service.php');
	}

	public function form($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'form', true, app__, 'form.php');
	}

	public function nav($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'nav', true, app__, 'nav.php');
	}

	public function module($path, $base = app__)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'module', true, $base, ($base == app__) ? "module.php" : "class.php");
	}

	public function controller($path)
	{
		return $this->resolveInclude($path, 'controller', true, app__, "controller.php");
	}

	public function conf($path, $base = app__)
	{
		return $this->resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = "")
	{
		return $this->resolveInclude(($lang) ? $lang . "/" . $path : $path, 'vocab', false);
	}

}
