<?php

namespace sys\utils\html;

function attr($attr = []) {
	$attr = \sys\utils\helper\attr($attr);
	return (!empty($attr)) ? ' ' . implode(' ', \sys\utils\helper\iterate_join('="', $attr, '', '"')) : '';
}

function script($subj = '', $context = 'file', $attr = null, $iteration = null) {
	switch ($context) {
		case 'file':
			$subj = \sys\utils\path\base($subj);
		case 'external':
			$attr['src'] = ($iteration) ? $subj . '?' . $iteration : $subj;
			return '<script' . \sys\utils\html\attr($attr) . '></script>';
		case 'inline':
			return '<script' . \sys\utils\html\attr($attr) . '>' . trim($subj) . '</script>';
		default:
			return false;
	}
}

function style($subj = '', $context = 'file', $attr = null, $iteration = null) {
	switch ($context) {
		case 'file':
			$subj = \sys\utils\path\base($subj);
		case 'external':
			$attr['rel'] = 'stylesheet';
			$attr['href'] = ($iteration) ? $subj . '?' . $iteration : $subj;
			return '<link' . \sys\utils\html\attr($attr) . '>';
		case 'inline':
			return '<style' . \sys\utils\html\attr($attr) . '>' . trim($subj) . '</style>';
		default:
			return false;
	}
}

function meta($attr = []) {
	return '<meta' . \sys\utils\html\attr($attr) . '>';
}
