<?php

namespace sys;

class Load {

	use \sys\traits\Util;
	
	public function model($path)
	{
		$subj = $this->util()->helper->getPathName($path);
		return $this->$subj = $this->util()->loader->resolveInclude($path, 'model', true, app__, 'model.php');
	}

	public function service($path, $data = null)
	{
		$subj = $this->util()->helper->getPathName($path);
		return $this->$subj = $this->util()->loader->resolveInclude($path, 'service', true, app__, 'service.php');
	}

	public function form($path)
	{
		$subj = $this->util()->helper->getPathName($path);
		return $this->$subj = $this->util()->loader->resolveInclude($path, 'form', true, app__, 'form.php');
	}

	public function nav($path)
	{
		$subj = $this->util()->helper->getPathName($path);
		return $this->$subj = $this->util()->loader->resolveInclude($path, 'nav', true, app__, 'nav.php');
	}

	public function module($path, $base = app__)
	{
		$subj = $this->util()->helper->getPathName($path);
		$ext = ($base == app__) ? 'module.php' : 'class.php';
		return $this->$subj = $this->util()->loader->resolveInclude($path, 'module', true, $base, $ext);
	}

	public function controller($path)
	{
		return $this->util()->loader->resolveInclude($path, 'controller', true, app__, 'controller.php');
	}

	public function conf($path, $base = app__)
	{
		return $this->util()->loader->resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = '')
	{
		return $this->util()->loader->resolveInclude(($lang) ? $lang . '/' . $path : $path, 'vocab', false);
	}

}
