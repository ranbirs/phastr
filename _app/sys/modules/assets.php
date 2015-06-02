<?php

namespace sys\modules;

class Assets
{

	public $assets = [];

	public function script($subj = null, $type = 'file', $attr = null, $iteration = null)
	{
		$key = hash('md5', 'script' . $type . $iteration . $subj);
		$asset = $this->scriptTag($subj, $type, $attr, $iteration);
		return $this->assets['script'][$type][$key] = ['value' => $subj, 'asset' => $asset, null];
	}

	public function style($subj = null, $type = 'file', $attr = null, $iteration = null)
	{
		$key = hash('md5', 'style' . $type . $iteration . $subj);
		$asset = $this->styleTag($subj, $type, $attr, $iteration);
		return $this->assets['style'][$type][$key] = ['value' => $subj, 'asset' => $asset, 'iteration' => $iteration];
	}

	public function meta($attr = null)
	{
		return $this->assets['meta'][] = $this->metaTag($attr);
	}

	public function set($type, $asset = null)
	{
		return $this->assets[$type][] = $asset;
	}

	public function get($subj = 'script', $type = null, $path = null)
	{
		switch ($subj) {
			case 'script':
			case 'style':
				$assets['external'] = [];
				$assets['file'] = [];
				$assets['inline'] = [];
				foreach ($this->assets[$subj] as $_type => $_assets) {
					if ($path && $_type == 'file') {
						$_assets = [['asset' => $this->optimize($subj, $_assets, $path)]];
					}
					foreach ($_assets as $asset) {
						$assets[$_type][] = $asset['asset'];
					}
				}
				return (!$type) ? implode(PHP_EOL, call_user_func_array('array_merge', $assets)) : ((isset($assets[$type])) ? implode(PHP_EOL, $assets[$type]) : false);
			case 'meta':
				return (isset($this->assets[$subj])) ? implode(PHP_EOL, array_values($this->assets[$subj])) : false;
			default:
				return (isset($this->assets[$subj])) ? $this->assets[$subj] : false;
		}
	}

	protected function scriptTag($subj = null, $context = 'file', $attr = null, $iteration = null)
	{
		switch ($context) {
			case 'file':
				$subj = \sys\utils\Path::base($subj);
			case 'external':
				$attr['src'] = (isset($iteration)) ? $subj . '?' . $iteration : $subj;
				return '<script' . \sys\utils\Html::attr($attr) . '></script>';
			case 'inline':
				return '<script' . \sys\utils\Html::attr($attr) . '>' . trim($subj) . '</script>';
			default:
				return false;
		}
	}

	protected function styleTag($subj = null, $context = 'file', $attr = null, $iteration = null)
	{
		switch ($context) {
			case 'file':
				$subj = \sys\utils\Path::base($subj);
			case 'external':
				$attr['rel'] = 'stylesheet';
				$attr['href'] = (isset($iteration)) ? $subj . '?' . $iteration : $subj;
				return '<link' . \sys\utils\Html::attr($attr) . '>';
			case 'inline':
				return '<style' . \sys\utils\Html::attr($attr) . '>' . trim($subj) . '</style>';
			default:
				return false;
		}
	}

	protected function metaTag($attr = [])
	{
		return '<meta' . \sys\utils\Html::attr($attr) . '>';
	}

	protected function optimize($type, $assets = [], $path)
	{
		$ext = ['style' => 'css', 'script' => 'js'];
		$root = \sys\utils\Path::root() . '/' . \sys\utils\Path::base();
		$file = hash('md5', implode(array_keys($assets))) . '.' . ($ext = $ext[$type]);
		$path = $path . '/' . $ext;
		$base = $root . $path;
		$path = $path . '/' . $file;
		
		if (!file_exists($file = $base . '/' . $file)) {
			if (!is_dir($base)) {
				mkdir($base);
			}
			if (is_writable($base)) {
				$content = [];
				foreach ($assets as $asset) {
					$content[] = file_get_contents($root . $asset['value']);
				}
				file_put_contents($file, implode(PHP_EOL, $content));
			}
		}
		return $this->{($type . 'Tag')}($path, 'file');
	}

}
