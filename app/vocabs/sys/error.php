<?php

namespace app\vocabs\sys\error;

	const res_route__ = 'Current [route] ([controller]/[page]/[action]) can only contain letters, numbers and hyphens and can NOT be the same as the default [method] or have more than 128 characters individually (this does not apply to [params] or query strings)';
	const res_controller__ = "Current [controller] cannot be the same as the default [master] Controller";
	const res_qstr__ = "Current [qstr] could not be parsed";

	const controller_methods__ = 'None of the Current [methods] ("[default]_[default]", "[default]_[action]", "[page]_[default]" or "[page]_[action]") was declared in the scope of the Current [controller]';

	const view_render__ = "Current [view] could not be found";
	const view_layout__ = "Current [layout] could not be found";

	const assets_write__ = "Current [assets] could not be optimized as the configured [assets] folder is not writable";
