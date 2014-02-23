<?php

namespace sys\utils;

class Html
{
	
	use \sys\traits\util\Helper;
	use \sys\traits\util\Path;

	public function attr($attr = [])
	{
		$attr = $this->helper()->attr($attr);
		return (!empty($attr)) ? ' ' . implode(' ', $this->helper()->composeArray('="', $attr, '', '"')) : '';
	}

	public function asset($type = 'script', $context = 'file', $subj = null, $params = null, $append = null)
	{
		switch ($type) {
			case 'script':
				switch ($context) {
					case 'file':
						$subj = '/' . $this->path()->base($subj);
					case 'external':
						$params['src'] = (!is_null($append)) ? $subj . '?' . $append : $subj;
						$asset = '<script' . $this->attr($params) . '></script>';
						break 2;
					case 'inline':
						$asset = '<script' . $this->attr($params) . '>' . trim($subj) . '</script>';
						break 2;
					default:
						return false;
				}
				break;
			case 'style':
				switch ($context) {
					case 'file':
						$subj = '/' . $this->path()->base($subj);
					case 'external':
						$params['href'] = (!is_null($append)) ? $subj . '?' . $append : $subj;
						$params['rel'] = 'stylesheet';
						$asset = '<link' . $this->attr($params) . '>';
						break 2;
					case 'inline':
						$asset = '<style' . $this->attr($params) . '>' . trim($subj) . '</style>';
						break 2;
					default:
						return false;
				}
				break;
			case 'meta':
				$asset = '<meta' . $this->attr((array) $subj) . '>';
				break;
			default:
				return false;
		}
		return $asset;
	}

}
