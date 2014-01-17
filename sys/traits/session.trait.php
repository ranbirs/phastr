<?php

namespace sys\traits;

trait Session {

	public function session($new = false)
	{
		return \sys\Init::session($new);
	}

}
