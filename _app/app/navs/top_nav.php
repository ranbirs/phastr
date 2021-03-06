<?php

namespace app\navs;

class Top_nav extends \app\modules\Nav
{

	protected function items()
	{
		$this->item('/', '/', $params = ['attr' => ['class' => 'css-class', 'data-attr-name' => 'data_attr_val']]);
		$this->item('Forms', 'index/example-forms');
		$this->item('Blog', 'example-blog', 
			[
				['label' => 'Example Blog', 'path' => 'example-blog/test', 'attr' => ['class' => ['css-flex', 'css-one', 'css-two']]], 
				['label' => 'Another Blog', 'path' => 'example-blog/test1', 'attr' => ['data-attr1' => 'attr1_val', 'data-attr2' => 'attr2_val']]]);
		
		$this->item('Private', 'index/private-page');
	}

}
