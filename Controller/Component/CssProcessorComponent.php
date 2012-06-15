<?php
/*

	CssProcessorComponent - CSS Preprocessor
	----------------------------------------------

	@file 		CssProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::uses('iPreprocessor', 'ResourcesController.Lib');

class CssProcessorComponent extends Component implements iPreprocessor {
	public function process($script_in, $compress=false) {
		return $compress ? $this->compress($script_in) : $script_in;
	}

	public function compress($script_in) {
		$script_in = preg_replace('#\s+#', ' ', $script_in); // Multiple spaces
		$script_in = preg_replace('#/\*.*?\*/#s', '', $script_in);
		$script_in = preg_replace("/(;|:|{|}|,)\s/u", "$1", $script_in); // All post-spacing
		$script_in = preg_replace("/\s(;|:|{|}|,)/u", "$1", $script_in); // All pre-spacing
		$script_in = str_replace(';}', '}', $script_in); // Redundant semi-colons

		return trim($script_in);
	}
}