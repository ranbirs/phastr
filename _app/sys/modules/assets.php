<?php

namespace sys\modules;

use app\confs\Config as __config;

class Assets
{

	const script__ = 'js';

	const style__ = 'css';

	const hash__ = 'md5';

	protected $assets = [];

	public function script($subj = null, $type = 'file', $attr = null, $iteration = __config::iteration__)
	{
		$key = hash(self::hash__, 'script' . $type . $iteration . $subj);
		$asset = \sys\utils\html\script($subj, $type, $attr, $iteration);
		return $this->assets['script'][$type][$key] = ['value' => $subj, 'asset' => $asset, 'iteration' => $iteration];
	}
	
	public function style($subj = null, $type = 'file', $attr = null, $iteration = __config::iteration__)
	{
		$key = hash(self::hash__, 'style' . $type . $iteration . $subj);
		$asset = \sys\utils\html\style($subj, $type, $attr, $iteration);
		return $this->assets['style'][$type][$key] = ['value' => $subj, 'asset' => $asset, 'iteration' => $iteration];
	}
	
	public function meta($attr = null)
	{
		return $this->assets['meta'][] = \sys\utils\html\meta($attr);
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
					if (__config::assets__ && $_type == 'file') {
						$_assets = [['asset' => $this->optimize($subj, $_assets)]];
					}
					foreach ($_assets as $asset) {
						$assets[$_type][] = $asset['asset'];
					}
				}
				return ($type) ? ((isset($assets[$type])) ? implode(eol__, $assets[$type]) : false) : implode(eol__, call_user_func_array('array_merge', $assets));
			case 'meta':
				return (isset($this->assets[$subj])) ? implode(eol__, array_values($this->assets[$subj])) : false;
			default:
				return (isset($this->assets[$subj])) ? $this->assets[$subj] : false;
		}
	}

	protected function optimize($type, $assets = [])
	{
		$ext = constant('self::' . $type . '__');
		$root_path = \sys\utils\path\root() . '/' . \sys\utils\path\base();
		$assets_path = __config::assets__ . '/' . $ext;
		$file_name = hash(self::hash__, implode(array_keys($assets))) . '.' . $ext;
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
				file_put_contents($file, implode(eol__, $content));
			}
		}
		return call_user_func_array('\\sys\\utils\\html\\' . $type, [$file_path, 'file']);
	}

}
