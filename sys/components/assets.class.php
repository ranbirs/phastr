<?php

namespace sys\components;

use sys\utils\Helper;
use sys\utils\Html;

class Assets {

	private $_assets = array();

	public function get($type = 'script')
	{
		switch ($type) {
			case 'script':
			case 'style':
				if (\app\confs\app\optimize__) {
					return $this->_optimize($type, $this->_assets[$type]);
				}
				else {
					$assets = array();
					foreach ($this->_assets[$type] as $context => $asset)
						foreach ($asset as $param)
							$assets[] = $param['asset'];
					return implode("\n", $assets);
				}
				break 2;
			default:
				return (isset($this->_assets[$type])) ?
					implode("\n", array_values($this->_assets[$type])) :
					null;
		}
	}

	public function set($type = array('script' => 'file'), $subj = null, $params = null, $append = \app\confs\app\iteration__)
	{
		$context = null;
		if (is_array($type)) {
			$context = current($type);
			$type = key($type);
		}
		$asset = Html::getAsset($type, $context, trim($subj), $params, $append);
		switch ($type) {
			case 'script':
			case 'style':
				switch ($context) {
					case null:
						$context = 'file';
					case 'file':
						return $this->_assets[$type][$context][] = array(
							'value' => $subj,
							'asset' => $asset,
							'iteration' => $append
						);
						break;
					case 'inline':
						return $this->_assets[$type][$context][] = $asset;
						break;
				}
				break;
			default:
				return $this->_assets[$type][] = $asset;
		}
	}

	private function _optimize($type, $assets = array())
	{
		$file_assets = array();
		$inline_assets = array();
		$ext = array('script' => "js", 'style' => "css");

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
						$inline_assets[] = $param;
					break;
			}
		}
		if (isset($file_assets['value'])) {
			$document_root = $_SERVER['DOCUMENT_ROOT'] . Helper::getPath("", 'base');
			$write_path = \app\confs\app\assets__ . "/" . $type;
			$file_name = hash('md5', implode($file_assets['checksum'])) . "." . $ext[$type];
			$dir = $document_root . $write_path;
			$file = $dir . "/" . $file_name;

			if (!is_dir($dir))
				@mkdir($dir);
			if (is_writable($dir)) {
				if (!file_exists($file)) {
					$content = array();
					foreach ($file_assets['value'] as $file_path)
						$content[] = @file_get_contents($document_root . $file_path);
					@file_put_contents($file, implode("\n", $content));
				}
				$file_assets = array(Html::getAsset($type, 'file', $write_path . "/". $file_name, null, null));
			}
			else {
				trigger_error(\app\vocabs\sys\error\assets_write__);
				$file_assets = $file_assets['asset'];
			}
		}
		return implode("\n", array_merge($file_assets, $inline_assets));
	}

}
