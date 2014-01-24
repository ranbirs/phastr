<?php

namespace sys\modules;

use sys\Module;

class Assets extends Module {

	const script__ = 'js';
	const style__ = 'css';
	const hash__ = 'md5';

	protected $assets = [];

	function __construct()
	{

	}

	public function get($type = 'script')
	{
		switch ($type) {
			case 'script':
			case 'style':
				if (\app\confs\config\optimize__) {
					return $this->_optimize($type, $this->assets[$type]);
				}
				$assets = ['remote' => [], 'file' => [], 'inline' => []];
				foreach ($this->assets[$type] as $context => $asset)
					foreach ($asset as $param)
						$assets[$context][] = $param['asset'];
				return implode(eol__, array_merge($assets['remote'], $assets['file'], $assets['inline']));
			default:
				return (isset($this->assets[$type])) ?
					implode(eol__, array_values($this->assets[$type])) :
					null;
		}
	}

	public function set($type = ['script' => 'file'], $subj = null, $params = null, $append = \app\confs\config\iteration__)
	{
		$context = null;
		if (is_array($type)) {
			$context = current($type);
			$type = key($type);
		}
		if (filter_var($subj, FILTER_VALIDATE_URL))
			$context = 'remote';
		$asset = $this->util()->html()->getAsset($type, $context, trim($subj), $params, $append);

		switch ($type) {
			case 'script':
			case 'style':
				$key = hash(self::hash__, $subj);
				switch ($context) {
					case null:
						$context = 'file';
					case 'file':
					case 'remote':
						return $this->assets[$type][$context][$key] = [
							'value' => $subj,
							'asset' => $asset,
							'iteration' => $append
						];
					case 'inline':
						return $this->assets[$type][$context][$key] = ['asset' => $asset];
				}
				break;
			default:
				return $this->assets[$type][] = $asset;
		}
	}

	private function _optimize($type, $assets = [])
	{
		$file_assets = [];
		$inline_assets = [];
		$remote_assets = [];
		$ext = ['script' => self::script__, 'style' => self::style__];

		foreach ($assets as $context => $asset) {
			switch ($context) {
				case 'file':
					foreach ($asset as $param) {
						$file_assets['value'][] = $param['value'];
						$file_assets['asset'][] = $param['asset'];
						$file_assets['checksum'][] = $param['value'] . $ext[$type] . $param['iteration'];
					}
					break;
				case 'inline':
					foreach ($asset as $param)
						$inline_assets[] = $param['asset'];
					break;
				case 'remote':
					foreach ($asset as $param)
						$remote_assets[] = $param['asset'];
					break;
			}
		}
		if (isset($file_assets['value'])) {
			$root_path = $this->util()->helper()->getPath('', 'root') . '/' . $this->util()->helper()->getPath('', 'base');
			$write_path = \app\confs\config\assets__ . '/' . $type;
			$file_name = hash(self::hash__, implode($file_assets['checksum'])) . '.' . $ext[$type];
			$dir = $root_path . $write_path;
			$file = $dir . '/' . $file_name;

			if (!is_dir($dir))
				mkdir($dir);
			if (is_writable($dir)) {
				if (!file_exists($file)) {
					$content = [];
					foreach ($file_assets['value'] as $file_path)
						$content[] = file_get_contents($root_path . $file_path);
					file_put_contents($file, implode(eol__, $content));
				}
				$file_assets = [$this->util()->html()->getAsset($type, 'file', $write_path . '/'. $file_name, null, null)];
			}
			else {
				$file_assets = $file_assets['asset'];
			}
		}
		return implode(eol__, array_merge($remote_assets, $file_assets, $inline_assets));
	}

}
