<?php

namespace sys\modules;

use sys\Init;
use sys\utils\Html;

abstract class Nav {

	private $_build = array(), $_items = array();
	private $_html;

	function __construct()
	{

	}

	abstract protected function build();

	public function html($data = null, $title = null, $css = array(), $template = "bootstrap")
	{
		$this->build($data);

		if (!$this->_html) {
			if (!empty($css)) {
				$this->_build['attr']['class'] = implode(" ", $css);
				$this->_build['attr'] = Html::getAttr($this->_build['attr']);
			}
			$data = array('title' => $title, 'build' => $this->_build, 'items' => $this->_items);
			$this->_html = Init::view()->template('nav', $template, $data);
		}
		return $this->_html;
	}

	protected function item($label = null, $path = null, $data = array())
	{
		$count = count($this->_items);
		$index = ($count > 0) ? $count + 1 : 0;

		if (!empty($data)) {
			foreach ($data as $key => $val) {
				if (is_int($key)) {
					$this->_items[$index]['label'] = $label;
					$this->_items[$index]['path'] = $path;
					$this->_items[$index]['data'][] = $val;
					continue;
				}
				$this->_items[] = array('label' => $label, 'path' => $path, 'data' => $data);
				break;
			}
			return;
		}
		$this->_items[] = array('label' => $label, 'path' => $path, 'data' => $data);
	}

}
