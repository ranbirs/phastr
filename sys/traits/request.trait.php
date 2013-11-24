<?php

namespace sys\traits;

trait Request {

	public function request()
	{
		return \sys\Init::request();
	}

	public function resolveRequest()
	{
		$context = \sys\Init::route()->params(1);
		$subj = \sys\Init::route()->params(2);

		if (is_null($subj) or $this->request()->header() !== \sys\Init::session()->token()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$method = $this->request()->method;
				\sys\Init::view()->request = $this->request()->$method();
				\sys\Init::view()->response = \sys\Init::view()->request($subj);
				if (\sys\Init::view()->response !== false) {
					return true;
				}
				return false;
			case 'form':
				if ($this->$subj instanceof \sys\modules\Form) {
					$method = $this->$subj->method();
					\sys\Init::view()->request = $this->request()->$method();
					\sys\Init::view()->response = $this->$subj->resolve('json');
					return true;
				}
				return false;
			default:
				return false;
		}
	}

}
