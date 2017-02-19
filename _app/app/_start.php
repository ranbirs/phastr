<?php
spl_autoload_register();

$_error = new \sys\handlers\Error();

set_error_handler([$_error, 'error']);
set_exception_handler([$_error, 'exception']);

$_routes = [
	'index' => 'app/controllers/index', 
	'user' => 'app/controllers/user', 
	'example-blog' => 'app/controllers/example_blog', 
	'consumer' => 'app/controllers/consumer',
	'provider' => 'app/controllers/provider'];

$_init = new \app\init\Mvc($_route = new \sys\Route('index', 'index', $_routes));

exit();