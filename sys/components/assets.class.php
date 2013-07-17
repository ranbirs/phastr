<?php

namespace sys\components;

use sys\utils\Html;

class Assets {

	private $_assets = array();

	public function get($type = 'script', $context = null)
	{
		switch ($context) {
			case 'file':
			case 'inline':
			case null:
				switch ($type) {
					case 'script':
					case 'style':
						if (!is_null($context)) {
							return (isset($this->_assets[$type][$context]['asset'])) ?
								implode("\n", array_values($this->_assets[$type][$context]['asset'])) :
								null;
						}
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
				break;
			default:
				return false;
		}
	}

	public function set($type = array('script' => 'file'), $subj = null, $content = null, $append = \app\confs\app\iteration__)
	{
		$context = null;
		if (is_array($type)) {
			$context = current($type);
			$type = key($type);
		}
		switch ($context) {
			case 'file':
			case 'inline':
			case null:
				$key = hash('md5', $subj);
				$asset = Html::getAsset($type, $context, $subj, $content, $append);
				switch ($type) {
					case 'script':
					case 'style':
						if (!is_null($context)) {
							$this->_assets[$type][$context][$key]['value'] = $subj;
							return $this->_assets[$type][$context][$key]['asset'] = $asset;
						}
						else {
							$this->_assets[$type]['file'][$key]['value'] = $subj;
							return $this->_assets[$type]['file'][$key]['asset'] = $asset;
						}
						break 2;
					default:
						return (isset($this->_assets[$type])) ?
							implode("\n", array_values($this->_assets[$type])) :
							null;
				}
				break;
			default:
				return false;
		}
	}

	private function _optimize($type, $assets = array())
	{
		$file_assets = array();
		$inline_assets = array();

		foreach ($assets as $context => $asset) {
			switch ($context) {
				case 'file':
					foreach ($asset as $param) {
						$file_assets['asset'][] = $param['asset'];
						$file_assets['value'][] = $param['value'];
					}
					break;
				case 'inline':
					foreach ($asset as $param)
						$inline_assets[] = $param['asset'];
					break;
			}
		}
		if (isset($file_assets['value'])) {
			$document_root = $_SERVER['DOCUMENT_ROOT'];
			$content = array();
			foreach ($file_assets['value'] as $file)
				$content[] = @file_get_contents($document_root . $file);

			$ext = array('script' => "js", 'style' => "css");
			$contents = implode("\n", $content);
			$checksum = hash('md5', $contents);
			$write_path = \app\confs\app\assets__ . "/" . $type;
			$file_name = $checksum . "." . $ext[$type];
			$dir = $document_root . "/" . $write_path;
			$file = $dir . "/" . $file_name;

			if (!is_dir($dir))
				@mkdir($dir);
			if (is_writable($dir)) {
				if (!file_exists($file))
					@file_put_contents($file, $contents);
				$file_assets = array(Html::getAsset($type, 'file', "/" . $write_path . "/". $file_name, null, null));
			}
			else {
				trigger_error(\app\vocabs\sys\error\assets_write__);
				$file_assets = $file_assets['asset'];
			}
		}
		return implode("\n", array_merge($file_assets, $inline_assets));
	}

}