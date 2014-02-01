<?php

namespace sys;

class Load
{
	
	use \sys\traits\Util;
	use \sys\traits\Instance;

	function __construct()
	{
	}

	public function controller($path)
	{
		return $this->util()->loader()->instanciate($path, 'controller');
	}

	public function module($path, $base = app__)
	{
		return $this->util()->loader()->instanciate($path, 'module', $this->instance(), $base);
	}

	public function model($path)
	{
		return $this->util()->loader()->instanciate($path, 'model', $this->instance());
	}

	public function form($path)
	{
		return $this->util()->loader()->instanciate($path, 'form', $this->instance());
	}

	public function nav($path)
	{
		return $this->util()->loader()->instanciate($path, 'nav', $this->instance());
	}

	public function service($path, $data = null)
	{
		return $this->util()->loader()->instanciate($path, 'service', $this->instance());
	}

}
