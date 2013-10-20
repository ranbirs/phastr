<?php

namespace sys\modules;

use sys\Init;
use sys\utils\Helper;
use sys\utils\Html;

abstract class Nav {

	protected $nav_id;
	private $_build = [], $_items = [];

	function __construct()
	{
		$this->nav_id = strtolower(Helper::getClassName(get_class($this)));
	}

	abstract protected function build();

	public function html($import = null, $title = null, $attr = [], $template = "bootstrap")
	{
		$this->build($import);

		$attr['id'] = $this->nav_id;
		$this->_build['title'] = $title;
		$this->_build['attr'] = Html::getAttr($attr);

		$nav = ['build' => $this->_build, 'items' => $this->_items];
		return Init::view()->template('nav', $template, $nav);
	}

	protected function item($label = null, $path = null, $params = [])
	{
		$count = count($this->_items);
		$index = ($count > 0) ? $count + 1 : 0;

		if (!empty($params)) {
			foreach ($params as $key => $val) {
				if (is_int($key)) {
					$this->_items[$index]['label'] = $label;
					$this->_items[$index]['path'] = $path;
					$this->_items[$index]['item'][] = $val;
					continue;
				}
				$this->_items[] = ['label' => $label, 'path' => $path, 'item' => $params];
				break;
			}
		}
		else {
			$this->_items[] = ['label' => $label, 'path' => $path, 'item' => $params];
		}
	}

}
