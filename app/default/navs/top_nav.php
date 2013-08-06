<?php

namespace app\navs;

class Top_nav extends \sys\modules\Nav {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($data = null)
	{
		$this->item("/", "/",
			$data = array(
				'css' => array("css-class"),
				'attr' => array('data-attr-name' => "data_attr_val")
			)
		);
	}

}
