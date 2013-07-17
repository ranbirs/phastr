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

	public static function getAsset($type = 'script', $context = 'file', $subj = null, $content = null, $append = null)
	{
		switch ($type) {
			case 'script':
				switch ($context) {
					case 'inline':
						$asset = "<script>" . $subj . "</script>";
						break 2;
					case 'file':
					case null:
						$asset = '<script src="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '"></script>';
						break 2;
					default:
						$asset = null;
				}
				break;
			case 'style':
				$attrs = "";
				if (is_array($content)) {
					$attrs = self::getAttr($content);
					$content = null;
				}
				switch ($context) {
					case 'inline':
						$asset = "<style>" . $subj . "</style>";
						break 2;
					case 'file':
					case null:
						$asset = '<link href="' . ((!is_null($append)) ? $subj . "?" . $append : $subj) . '" rel="stylesheet"' . "$attrs>";
						break 2;
					default:
						$asset = null;
				}
				break;
			case 'meta':
				$asset = '<meta name="' . $subj . '" content="' . $content . '">';
				break;
			default:
				$asset = null;
		}
		return $asset;
	}

}
