<?php

namespace app\init;

class Mvc extends \sys\Init
{

	public static $routes = [
		'index' => 'app/controllers/index', 
		'user' => 'app/controllers/user', 
		'example-blog' => 'app/controllers/example_blog', 
		'consumer' => 'app/controllers/consumer', 
		'provider' => 'app/controllers/provider'];

	public static $deny = ['index/*/*/*'];
	// @todo
	function __construct()
	{
		parent::__construct();
		
		$this->route = new \sys\Route('index', 'index', self::$routes, self::$deny);
		
		if (!$this->route->route) {
			$this->route->error(404, 'app/views/layouts/error/404');
		}
		
		$this->controller = new $this->route->route['class']();
		
		if (method_exists($this->controller, $this->route->route['label'][1])) {
			$this->controller->init($this->route->route['label'][1], $this->route->route['params']);
			$this->controller->{$this->route->route['label'][1]}($this->route->route['params']);
			$this->controller->render();
		}
		$this->route->error(404, 'app/views/layouts/error/404');
	}

}
