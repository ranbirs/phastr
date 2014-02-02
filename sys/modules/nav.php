<?php

namespace sys\modules;

abstract class Nav extends \sys\Module
{

	protected $nav_id;

	protected $build = [], $items = [];

	function __construct()
	{
	}

	abstract protected function build();

	public function id()
	{
		return $this->nav_id;
	}

	public function html($import = null, $title = null, $attr = [], $template = 'bootstrap')
	{
		$this->nav_id = strtolower($this->util()->helper()->instanceClassName($this));
		$this->build($import);
		
		$attr['id'] = $this->nav_id;
		$this->build['title'] = $title;
		$this->build['attr'] = $this->util()->helper()->attr($attr);
		
		$nav = ['build' => $this->build, 'items' => $this->items];
		return $this->view()->template('nav', $template, $nav);
	}

	protected function item($label = null, $path = null, $params = [])
	{
		$count = count($this->items);
		$index = ($count > 0) ? $count + 1 : 0;
		
		if (! empty($params)) {
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
