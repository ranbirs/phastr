<?php

namespace sys;

use sys\utils\Helper;

class Load {

	use \sys\traits\Loader;

	public function model($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'model');
	}

	public function service($path, $data = null)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'service');
	}

	public function form($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'form');
	}

	public function nav($path)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = $this->resolveInclude($path, 'nav');
	}

	public function controller($path)
	{
		return $this->resolveInclude($path, 'controller');
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
