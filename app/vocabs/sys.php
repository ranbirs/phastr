<?php

namespace app\vocabs\sys;

	const er_icr__ = 'Current [route] ([controller]/[page]/[action]) can only contain letters, numbers and hyphens and can NOT be the same as the default [method] or have more than 128 characters individually (this does not apply to [params] or query strings)';
	const er_icc__ = "Current [controller] cannot be the same as the default [master] Controller";

	const er_ccm__ = 'None of the Current [methods] ("[default]_[default]", "[default]_[action]", "[page]_[default]" or "[page]_[action]") was declared in the scope of the Current [controller]';

	const er_vcv__ = "Current [view] could not be found";
	const er_vcl__ = "Current [layout] could not be found";
