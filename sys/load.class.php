<?php

namespace sys;

use sys\utils\Helper;
use sys\utils\Loader;

class Load {

	use \sys\traits\Utils;
	
	public function model($path)
	{
		$subj = $this->utils()->helper->getPathName($path);
		return $this->$subj = $this->utils()->loader->resolveInclude($path, 'model', true, app__, 'model.php');
	}

	public function service($path, $data = null)
	{
		$subj = $this->utils()->helper->getPathName($path);
		return $this->$subj = $this->utils()->loader->resolveInclude($path, 'service', true, app__, 'service.php');
	}

	public function form($path)
	{
		$subj = $this->utils()->helper->getPathName($path);
		return $this->$subj = $this->utils()->loader->resolveInclude($path, 'form', true, app__, 'form.php');
	}

	public function nav($path)
	{
		$subj = $this->utils()->helper->getPathName($path);
		return $this->$subj = $this->utils()->loader->resolveInclude($path, 'nav', true, app__, 'nav.php');
	}

	public function module($path, $base = app__)
	{
		$subj = $this->utils()->helper->getPathName($path);
		return $this->$subj = $this->utils()->loader->resolveInclude($path, 'module', true, $base, ($base == app__) ? 'module.php' : 'class.php');
	}

	public function controller($path)
	{
		return $this->utils()->loader->resolveInclude($path, 'controller', true, app__, 'controller.php');
	}

	public function conf($path, $base = app__)
	{
		return $this->utils()->loader->resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = '')
	{
		return $this->utils()->loader->resolveInclude(($lang) ? $lang . '/' . $path : $path, 'vocab', false);
	}

}
