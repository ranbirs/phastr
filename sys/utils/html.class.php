<?php

namespace sys\utils;

class Html {

	public static function getAttr($attr)
	{
		$attrs = array();
		foreach ($attr as $key => $val)
			$attrs[] = $key . '="' . $val . '"';
		return " " . implode(" ", $attrs);
	}

	public static function getAsset($type = 'script', $context = null, $subj = null, $params = null, $append = null)
	{
		switch ($type) {
			case 'script':
				switch ($context) {
					case null:
					case 'file':
						$subj = \sys\utils\Helper::getPath($subj, 'base');
						$asset = '<script src="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '"></script>';
						break 2;
					case 'inline':
						$asset = "<script>" . $subj . "</script>";
						break 2;
					default:
						$asset = null;
				}
				break;
			case 'style':
				if (!empty($params))
					$params = self::getAttr($params);
				switch ($context) {
					case null:
					case 'file':
						$subj = \sys\utils\Helper::getPath($subj, 'base');
						$asset = '<link href="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '" rel="stylesheet"' . $params . ">";
						break 2;
					case 'inline':
						$asset = "<style>" . $subj . "</style>";
						break 2;
					default:
						$asset = null;
				}
				break;
			case 'meta':
				$asset = '<meta name="' . $subj . '" content="' . $params . '">';
				break;
			default:
				$asset = null;
		}
		return $asset;
	}

}
