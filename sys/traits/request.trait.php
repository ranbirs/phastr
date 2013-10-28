<?php

namespace sys\traits;

use sys\Init;

trait Request {

	protected function request()
	{
		return Init::request();
	}

	protected function submitRequest()
	{
		$context = Init::route()->params(1);
		$subj = Init::route()->params(2);

		if (is_null($subj) or $this->request()->header() !== Init::session()->token()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$method = $this->request()->method;
				Init::view()->request = $this->request()->$method();
				Init::view()->response = Init::view()->request($subj);
				if (Init::view()->response !== false) {
					return Init::view()->response($this->request()->layout);
				}
				return false;
			case 'form':
				if ($this->$subj instanceof \sys\modules\Form) {
					$method = $this->$subj->method();
					Init::view()->request = $this->request()->$method();
					Init::view()->response = $this->$subj->submit();
					return Init::view()->response('json');
				}
				return false;
			default:
				return false;
		}
	}

}
