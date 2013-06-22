<?php

namespace sys\utils;

class Html {

	public static function getAttr($attr)
	{
		$attrs = array();
		foreach ($attr as $key => $val) {
			$attrs[] = $key . '="' . $val . '"';
		}
		return " " . implode(" ", $attrs);
	}

	public static function getAsset($type = 'script', $subj = null, $content = null, $append = null)
	{
		switch ($type) {
			case 'script':
				$asset = (is_null($content)) ?
					'<script src="' . (($append) ? $subj . "?" . $append : $subj) . '"></script>' :
					"<script>$content</script>";
				break;
			case 'style':
				$attrs = "";
				if (is_array($content)) {
					$attrs = self::getAttr($content);
					$content = null;
				}
				$asset = (is_null($content)) ?
					'<link href="' . (($append) ? $subj . "?" . $append : $subj) . '" rel="stylesheet"' . "$attrs>" :
					"<style>$content</style>";
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
