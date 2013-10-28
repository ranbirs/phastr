<?php

namespace sys\modules;

use sys\utils\Helper;
use sys\utils\Html;

abstract class Nav {

	use \sys\traits\View;

	protected $nav_id;
	protected $build = [], $items = [];

	function __construct()
	{

	}

	abstract protected function build();

	public function id()
	{
		if (!isset($this->nav_id))
			$this->nav_id = strtolower(Helper::getInstanceClassName($this));
		return $this->nav_id;
	}

	public function html($import = null, $title = null, $attr = [], $template = "bootstrap")
	{
		$this->build($import);

		$attr['id'] = $this->id();
		$this->build['title'] = $title;
		$this->build['attr'] = Helper::getAttr($attr);

		$nav = ['build' => $this->build, 'items' => $this->items];
		return $this->view()->template('nav', $template, $nav);
	}

	protected function item($label = null, $path = null, $params = [])
	{
		$count = count($this->items);
		$index = ($count > 0) ? $count + 1 : 0;

		if (!empty($params)) {
			foreach ($params as $key => $val) {
				if (is_int($key)) {
					$this->items[$index]['label'] = $label;
					$this->items[$index]['path'] = $path;
					$this->items[$index]['item'][] = $val;
					continue;
				}
				$this->items[] = ['label' => $label, 'path' => $path, 'item' => $params];
				break;
			}
		}
		else {
			$this->items[] = ['label' => $label, 'path' => $path, 'item' => $params];
		}
	}

}
