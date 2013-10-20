<?php

namespace sys\utils;

use sys\utils\Helper;

class Html {

	public static function getAttr($attr)
	{
		$attrs = [];
		foreach ($attr as $key => $val) {
			if (is_int($key)){
				$attrs[] = $val;
				continue;
			}
			$attrs[] = $key . '="' . ((!is_array($val)) ? $val : implode(" ", $val)) . '"';
		}
		return (!empty($attrs)) ? " " . implode(" ", $attrs) : "";
	}

	public static function getAsset($type = 'script', $context = null, $subj = null, $params = null, $append = null)
	{
		switch ($type) {
			case 'script':
				switch ($context) {
					case null:
					case 'file':
						$subj = "/" . Helper::getPath($subj, 'base');
					case 'remote':
						$asset = '<script src="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '"></script>';
						break 2;
					case 'inline':
						$asset = "<script>" . eol__ . $subj . eol__ . "</script>";
						break 2;
					default:
						return false;
				}
				break;
			case 'style':
				if (!empty($params))
					$params = self::getAttr($params);
				switch ($context) {
					case null:
					case 'file':
						$subj = "/" . Helper::getPath($subj, 'base');
					case 'remote':
						$asset = '<link href="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '" rel="stylesheet"' . $params . ">";
						break 2;
					case 'inline':
						$asset = "<style>" . eol__ . $subj . eol__ . "</style>";
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
