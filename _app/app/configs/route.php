<?php

namespace app\configs;

class Route
{

	const rewrite__ = 0;

	const trail__ = '/';

	const scope__ = 'index,user,example-blog,consumer,provider'; /* Controllers path */

	const controller__ = 'index'; /* Default controller path */

	const page__ = 'index'; /* Default page path */

	const action__ = 'index'; /* Default action path */

	const glue__ = '__'; /* Default page-action method name delimiter */
	
	const request__ = 'ajax';
	
	const length__ = 32; /* Max character length for controller, page or action path */
	
	const limit__ = 16; /* Max number of path arguments */

}