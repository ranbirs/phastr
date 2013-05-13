<?php

namespace sys\modules;

class Nav extends \sys\Common {

	private $build = array();
	private $items = array();

	private $html;

	function __construct()
	{
		parent::__construct();
	}

	public function html($template = "bootstrap", $data = null)
	{
		$this->build($data);

		if (!$this->html) {
			$data = array('build' => $this->build, 'items' => $this->items);
			$this->html = $this->view->template('nav', $template, $data);
		}
		return $this->html;
	}

	protected function open($css = array())
	{
		if (!empty($css)) {
			$this->build['attr']['class'] = implode(" ", $css);
			$this->build['attr'] = \sys\utils\Html::getAttr($this->build['attr']);
		}
	}

	protected function close()
	{

	}

	protected function item($label = null, $path = null, $data = array())
	{
		$count = count($this->items);
		$index = ($count > 0) ? $count + 1 : 0;

		if (!empty($data)) {
			foreach ($data as $key => $val) {
				if (is_numeric($key)) {
					$this->items[$index]['label'] = $label;
					$this->items[$index]['path'] = $path;
					$this->items[$index]['data'][] = $val;
					continue;
				}
				$this->items[] = array('label' => $label, 'path' => $path, 'data' => $data);
				break;
			}
			return;
		}
		$this->items[] = array('label' => $label, 'path' => $path, 'data' => $data);
	}

}
