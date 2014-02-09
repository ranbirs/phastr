<?php

namespace sys\modules;

class Assets
{
	
	use \sys\traits\Util;

	const script__ = 'js';

	const style__ = 'css';

	const hash__ = 'md5';

	protected $assets = [];

	function __construct()
	{
	}

	public function get($type = 'script', $context = null)
	{
		switch ($type) {
			case 'script':
			case 'style':
				$assets = ['external' => [], 'file' => [], 'inline' => []];
				foreach ($this->assets[$type] as $_context => $asset) {
					if ($_context == 'file' && \app\confs\Config::assets__) {
						$assets['file'][] = $this->optimizeFiles($type, $asset);
						continue;
					}
					foreach ($asset as $param) {
						$assets[$_context][] = $param['asset'];
					}
				}
				if (is_null($context)) {
					return implode(eol__, array_merge($assets['external'], $assets['file'], $assets['inline']));
				}
				return (isset($assets[$context])) ? implode(eol__, $assets[$context]) : false;
			default:
				return (isset($this->assets[$type])) ? implode(eol__, array_values($this->assets[$type])) : null;
		}
	}

	public function set($type = ['script' => 'file'], $subj = null, $params = null, $append = \app\confs\Config::iteration__)
	{
		$context = 'file';
		if (is_array($type)) {
			$context = current($type);
			$type = key($type);
		}
		$asset = $this->util()->html()->asset($type, $context, $subj, $params, $append);
		
		switch ($type) {
			case 'script':
			case 'style':
				$key = hash(self::hash__, $subj);
				switch ($context) {
					case 'file':
					case 'external':
						return $this->assets[$type][$context][$key] = ['value' => $subj, 'asset' => $asset, 
							'iteration' => $append];
					case 'inline':
						return $this->assets[$type][$context][$key] = ['asset' => $asset];
				}
				break;
			default:
				return $this->assets[$type][] = $asset;
		}
	}

	protected function optimizeFiles($type, $files = [])
	{
		$assets = [];
		foreach ($files as $file) {
			$assets['value'][] = $file['value'];
			$assets['asset'][] = $file['asset'];
			$assets['checksum'][] = $type . $file['value'] . $file['iteration'];
		}
		$root_path = $this->util()->helper()->path('', 'root') . '/' . $this->util()->helper()->path('', 'base');
		$assets_path = \app\confs\Config::assets__ . '/' . $type;
		$file_name = hash(self::hash__, implode($assets['checksum'])) . '.' . constant('self::' . $type . '__');
		$dir = $root_path . $assets_path;
		
		if (! file_exists($file = $dir . '/' . $file_name)) {
			if (! is_dir($dir)) {
				mkdir($dir);
			}
			if (is_writable($dir)) {
				$content = [];
				foreach ($assets['value'] as $file_path) {
					$content[] = file_get_contents($root_path . $file_path);
				}
				file_put_contents($file, implode(eol__, $content));
			}
		}
		return $this->util()->html()->asset($type, 'file', $assets_path . '/' . $file_name, null, null);
	}

}
