<?php

$_routes = [
	'index' => 'app/controllers/index', 
	'user' => 'app/controllers/user', 
	'example-blog' => 'app/controllers/example_blog', 
	'consumer' => 'app/controllers/consumer',
	'provider' => 'app/controllers/provider'];

$_init = new \app\init\Mvc($_route = new \sys\Route('index', 'index', $_routes));

exit();