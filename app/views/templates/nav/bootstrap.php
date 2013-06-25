<?php

$build = $this->nav['build'];
$items = $this->nav['items'];
$menu = array();
$active = false;

foreach ($items as $index => &$item) {

	$leaf = array();
	$item['active'] = false;
	$item['css'] = (isset($item['data']['css'])) ? $item['data']['css'] : array();
	$item['attr'] = (isset($item['data']['attr'])) ? $item['data']['attr'] : array();

	foreach ($item['data'] as $key => &$val) {

		if (!is_int($key)) {
			continue;
		}
		if (!isset($val['css']))
			$val['css'] = array();
		if (!isset($val['attr']))
			$val['attr'] = array();

		$val['active'] = false;
		if (!$active) {
			if (\sys\Res::path() === $val['path']) {
				$val['active'] = true;
				$item['active'] = true;
			}
		}
		if (($val['active']))
			$val['css'][] = "active";

		if (!empty($val['css'])) {
			$val['attr']['class'] = implode(" ", $val['css']);
		}
		$val['attr'] = \sys\utils\Html::getAttr($val['attr']);

		$leaf[] = "<li" . $val['attr'] . ">";

		if ($val['path'] and $val['path'] != "/")
			$val['path'] = \sys\utils\Helper::getPath($val['path'], 'route') . "/";

		$val['anchor'] = "<a" . (($val['path']) ? ' href="' . $val['path'] . '"' : "") . ">" .
			$val['label'] . "</a>";
		$leaf[] = $val['anchor'];
		$leaf[] = "</li>";
	}
	unset($val);

	if (!empty($leaf))
		$item['css'][] = "dropdown";
	if (!$active) {
		if (($item['active'] or $item['path'] === \sys\Res::path()) or
			($item['path'] == "/" and $item['path'] === \sys\Res::request())) {
				$item['css'][] = "active";
				$active = true;
		}
	}
	if (!empty($item['css']))
		$item['attr']['class'] = implode(" ", $item['css']);

	$item['attr'] = \sys\utils\Html::getAttr($item['attr']);

	$menu[] = "<li" . $item['attr'] . ">";

	if ($item['path'] and $item['path'] != "/")
		$item['path'] = \sys\utils\Helper::getPath($item['path'], 'route') . "/";

	if (!empty($leaf)) {
		$item['anchor'] = '<a data-toggle="dropdown" class="dropdown-toggle"' .
			(($item['path']) ? ' href="' . $item['path'] . '"' : "") . ">" . $item['label'] . "</a>";
		$menu[] = $item['anchor'];
		$menu[] = '<ul class="dropdown-menu">' . implode("\n", $leaf) . "</ul>";
	}
	else {
		$menu[] = '<a href="' . $item['path'] . '">' . $item['label'] . "</a>";
	}
	$menu[] = "</li>";
}
unset($item);

$attrs = (isset($build['attr'])) ? $build['attr'] : "";

print "<ul$attrs>" . implode("\n", $menu) . "</ul>";
