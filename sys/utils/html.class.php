<?php

namespace sys\utils;

use sys\Util;

class Html extends Util {

	public function getAttr($attr = [])
	{
		$attr = $this->helper->getAttr($attr);
		return (!empty($attr)) ? ' ' . implode(' ', $this->helper->getStringArray('="', $attr, '', '"')) : '';
	}

	public function getAsset($type = 'script', $context = null, $subj = null, $params = null, $append = null)
	{
		switch ($type) {
			case 'script':
				switch ($context) {
					case null:
					case 'file':
						$subj = '/' . $this->helper->getPath($subj, 'base');
					case 'remote':
						$asset = '<script src="' . ((!is_null($append)) ? $subj . '?' . $append : $subj) . '"></script>';
						break 2;
					case 'inline':
						$asset = '<script>' . $subj . '</script>';
						break 2;
					default:
						return false;
				}
				break;
			case 'style':
				if (!empty($params))
					$params = $this->getAttr($params);
				switch ($context) {
					case null:
					case 'file':
						$subj = '/' . $this->helper->getPath($subj, 'base');
					case 'remote':
						$asset = '<link href="' . ((!is_null($append)) ? $subj . '?' . $append : $subj) . '" rel="stylesheet"' . $params . '>';
						break 2;
					case 'inline':
						$asset = '<style>' . $subj . '</style>';
						break 2;
					default:
						return false;
				}
				break;
			case 'meta':
				$asset = '<meta name="' . $subj . '" content="' . $params . '">';
				break;
			default:
				return false;
		}
		return $asset;
	}

}
