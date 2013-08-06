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
		$this->item("Forms", "example-forms");
		$this->item("Blog", "example-blog",
			array(
				array(
					'label' => "Example Blog",
					'path' => "example-blog/test",
					'css' => array("css-class", "css-2", "css-3")
				),
				array(
					'label' => "Another Blog",
					'path' => "example-blog/test1",
					'attr' => array(
						'data-attr1' => "attr_val1",
						'data-attr2' => "attr_val2",
						'data-attr3' => "attr_val3"
					)
				)
			)
		);
		$this->item("Tree page", "multi-level--sub-level--example-page");
		$this->item("Private", "user");
	}

}
