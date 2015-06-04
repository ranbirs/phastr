<?php
$list = [];

foreach ($nav['items'] as $index => &$item) {
	
	$leaf = [];
	$item['active'] = false;
	$item['attr'] = (isset($item['item']['attr'])) ? $item['item']['attr'] : [];
	
	foreach ($item['item'] as $key => &$val) {
		
		$val['active'] = false;
		
		if (!is_int($key)) {
			continue;
		}
		if (!isset($val['attr'])) {
			$val['attr'] = [];
		}
		if (isset($val['attr']['class'])) {
			$val['attr']['class'] = (array) $val['attr']['class'];
		}
		if ($val['path'] === $this->uri) {
			$val['active'] = true;
			$item['active'] = true;
		}
		if ($val['active']) {
			$val['attr']['class'][] = 'active';
		}
		$val['attr'] = \sys\utils\Html::attr($val['attr']);
		
		$leaf[] = '<li' . $val['attr'] . '>';
		
		if ($val['path']) {
			$val['path'] = \sys\utils\Path::uri($val['path']);
		}
		$val['anchor'] = '<a' . (($val['path']) ? ' href="' . $val['path'] . '"' : '') . '>' . $val['label'] . '</a>';
		$leaf[] = $val['anchor'];
		$leaf[] = '</li>';
	}
	unset($val);
	
	if (isset($item['attr']['class'])) {
		$item['attr']['class'] = (array) $item['attr']['class'];
	}
	if ($leaf) {
		$item['attr']['class'][] = 'dropdown';
	}
	if ($item['active'] || $item['path'] === $this->uri) {
		$item['attr']['class'][] = 'active';
	}
	$item['attr'] = \sys\utils\Html::attr($item['attr']);
	
	$list[] = '<li' . $item['attr'] . '>';
	
	if ($item['path']) {
		$item['path'] = \sys\utils\Path::uri($item['path']);
	}
	if ($leaf) {
		$item['anchor'] = '<a data-toggle="dropdown" class="dropdown-toggle"' . (($item['path']) ? ' href="' . $item['path'] . '"' : '') . '>' . $item['label'] .
			 '</a>';
		$list[] = $item['anchor'];
		$list[] = '<ul class="dropdown-menu">' . PHP_EOL . implode(PHP_EOL, $leaf) . PHP_EOL . '</ul>';
	} else {
		$list[] = '<a href="' . $item['path'] . '">' . $item['label'] . '</a>';
	}
	$list[] = '</li>';
}
unset($item);
$nav['attr'] = \sys\utils\Html::attr($nav['attr']);
?>
<ul <?= $nav['attr']; ?>>
    <?= implode(PHP_EOL, $list); ?>
</ul>
