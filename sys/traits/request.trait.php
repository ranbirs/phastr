<?php

namespace sys\traits;

use sys\Init;

trait Request {

	protected function submitRequest()
	{
		$context = Init::route()->params(1);
		$subj = Init::route()->params(2);

		if (is_null($subj) or Init::request()->header() !== Init::session()->token()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$method = Init::request()->method;
				Init::view()->request = Init::request()->$method();
				Init::view()->response = Init::view()->request($subj);
				if (Init::view()->response !== false) {
					return Init::view()->response(Init::request()->layout);
				}
				return false;
			case 'form':
				if ($this->$subj instanceof \sys\modules\Form) {
					$method = $this->$subj->method();
					Init::view()->request = Init::request()->$method();
					Init::view()->response = $this->$subj->submit();
					return Init::view()->response('json');
				}
				return false;
			default:
				return false;
		}
	}

}
