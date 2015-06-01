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

	function __construct()
	{
		parent::__construct();
		
		$this->route = new \sys\Route('index', 'index', self::$routes, self::$deny);
		
		if (!$this->route->route['resource']) {
			$this->route->error(404, 'app/views/layouts/error/404');
		}
		
		$this->controller = $this->route->resource(true);
		$this->controller = new $this->controller();
		
		if (method_exists($this->controller, $action = $this->route->action('-', '_'))) {
			$this->controller->init($action, $this->route->route['params']);
			$this->controller->{$action}($this->route->route['params']);
			$this->controller->render();
		}
		$this->route->error(404, 'app/views/layouts/error/404');
	}

}
