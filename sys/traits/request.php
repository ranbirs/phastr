<?php

namespace sys\traits;

use sys\Init;

trait Request {

	protected function submitRequest($context = null, $subj = null)
	{
		if (is_null($subj) or Init::request()->header() !== Init::session()->xid()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$method = Init::request()->method;
				Init::view()->request = Init::request()->$method();
				Init::view()->response = Init::view()->request($subj);
				Init::view()->response(Init::request()->layout);
				break;
			case 'form':
				if ($this->$subj instanceof \sys\modules\Form) {
					$method = $this->$subj->method();
					Init::view()->request = Init::request()->$method();
					Init::view()->response = $this->$subj->submit();
					Init::view()->response('json');
				}
				break;
		}
		return false;
	}

}
