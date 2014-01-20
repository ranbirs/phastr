<?php

namespace sys\traits;

trait Session {

	public function session()
	{
		return \sys\Init::session();
	}

}
