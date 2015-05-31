<?php

namespace sys\modules;

use sys\configs\Assets as __assets;

class Assets
{

	protected $assets = [];

	public function script($subj = null, $type = 'file', $attr = null, $iteration = __assets::iteration__)
	{
		$key = hash(__assets::algo__, 'script' . $type . $iteration . $subj);
		$asset = $this->scriptTag($subj, $type, $attr, $iteration);
		return $this->assets['script'][$type][$key] = ['value' => $subj, 'asset' => $asset, 'iteration' => $iteration];
	}

	public function style($subj = null, $type = 'file', $attr = null, $iteration = __assets::iteration__)
	{
		$key = hash(__assets::algo__, 'style' . $type . $iteration . $subj);
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

	public function get($subj = 'script', $type = null)
	{
		switch ($subj) {
			case 'script':
			case 'style':
				$assets['external'] = [];
				$assets['file'] = [];
				$assets['inline'] = [];
				foreach ($this->assets[$subj] as $_type => $_assets) {
					if (__assets::path__ && $_type == 'file') {
						$_assets = [['asset' => $this->optimize($subj, $_assets)]];
					}
					foreach ($_assets as $asset) {
						$assets[$_type][] = $asset['asset'];
					}
				}
				return ($type) ? ((isset($assets[$type])) ? implode(PHP_EOL, $assets[$type]) : false) : implode(PHP_EOL, 
					call_user_func_array('array_merge', $assets));
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

	protected function optimize($type, $assets = [])
	{
		$ext = constant('__assets::' . $type . '__');
		$root_path = \sys\utils\Path::root() . '/' . \sys\utils\Path::base();
		$assets_path = __assets::path__ . '/' . $ext;
		$file_name = hash(__assets::algo__, implode(array_keys($assets))) . '.' . $ext;
		$file_path = $assets_path . '/' . $file_name;
		$dir = $root_path . $assets_path;
		
		if (!file_exists($file = $dir . '/' . $file_name)) {
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			if (is_writable($dir)) {
				$content = [];
				foreach ($assets as $asset) {
					$content[] = file_get_contents($root_path . $asset['value']);
				}
				file_put_contents($file, implode(PHP_EOL, $content));
			}
		}
		return forward_static_call_array(['\\sys\\utils\\Html', $type], [$file_path, 'file']);
	}

}
