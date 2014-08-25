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
	
	const length__ = 64;

	const glue__ = '__'; /* Default page-action method name delimiter */
	
	const request__ = 'ajax';

}