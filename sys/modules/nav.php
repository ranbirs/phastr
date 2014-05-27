<?php

namespace sys\modules;

abstract class Nav
{
	
	use \sys\Loader;

	protected $nav_id;

	protected $nav = [], $items = [];

	abstract protected function items();

	public function nav_id()
	{
		return $this->nav_id;
	}

	public function get($params = null, $attr = [])
	{
		$this->nav_id = strtolower(\sys\utils\helper\class_name($this));
		
		$this->items();
		
		$attr['id'] = $this->nav_id;
		
		$this->nav['attr'] = \sys\utils\helper\attr($attr);
		$this->nav['params'] = $params;
		$this->nav['items'] = $this->items;
		
		return $this->nav;
	}
	
	public function render($template = 'bootstrap')
	{
		return $this->load()->init('view')->template('nav', $template, $this->nav);
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
