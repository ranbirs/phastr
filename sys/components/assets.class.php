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
				if (\app\confs\config\optimize__) {
					return $this->_optimize($type, $this->_assets[$type]);
				}
				else {
					$assets = array('file' => array(), 'inline' => array());
					foreach ($this->_assets[$type] as $context => $asset)
						foreach ($asset as $param)
							$assets[$context][] = $param['asset'];
					return implode("\n", array_merge($assets['file'], $assets['inline']));
				}
				break 2;
			default:
				return (isset($this->_assets[$type])) ?
					implode("\n", array_values($this->_assets[$type])) :
					null;
		}
	}

	public function set($type = array('script' => 'file'), $subj = null, $params = null, $append = \app\confs\config\iteration__)
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
				$key = hash('md5', $subj);
				switch ($context) {
					case null:
						$context = 'file';
					case 'file':
						return $this->_assets[$type][$context][$key] = array(
							'value' => $subj,
							'asset' => $asset,
							'iteration' => $append
						);
						break;
					case 'inline':
						return $this->_assets[$type][$context][$key] = array('asset' => $asset);
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
						$inline_assets[] = $param['asset'];
					break;
			}
		}
		if (isset($file_assets['value'])) {
			$document_root = Helper::getPath("", 'root') . Helper::getPath("", 'base');
			$write_path = \app\confs\config\assets__ . "/" . $type;
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
				trigger_error(\sys\confs\error\assets_write__);
				$file_assets = $file_assets['asset'];
			}
		}
		return implode("\n", array_merge($file_assets, $inline_assets));
	}

}
