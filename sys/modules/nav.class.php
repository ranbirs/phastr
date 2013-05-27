<?php

namespace sys\modules;

use sys\Res;

use sys\utils\Html;

class Nav {

	private $_html;

	private $_build = array();
	private $_items = array();

	function __construct()
	{

	}

	public function html($template = "bootstrap", $data = null)
	{
		$this->build($data);

		if (!$this->_html) {
			$data = array('build' => $this->_build, 'items' => $this->_items);
			$this->_html = Res::view()->template('nav', $template, $data);
		}
		return $this->_html;
	}

	protected function open($css = array())
	{
		if (!empty($css)) {
			$this->_build['attr']['class'] = implode(" ", $css);
			$this->_build['attr'] = Html::getAttr($this->_build['attr']);
		}
	}

	protected function close()
	{

	}

	protected function item($label = null, $path = null, $data = array())
	{
		$count = count($this->_items);
		$index = ($count > 0) ? $count + 1 : 0;

		if (!empty($data)) {
			foreach ($data as $key => $val) {
				if (is_numeric($key)) {
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
