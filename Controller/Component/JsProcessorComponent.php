<?php
/*

	JsProcessorComponent - Javascript Preprocessor
	----------------------------------------------

	@file 		JsProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::uses('iPreprocessor', 'ResourcesController.Lib');
App::import('ResourcesController.Vendor', 'JavaScriptPacker/JavaScriptPacker');

class JsProcessorComponent extends Component implements iPreprocessor {
	public function process($script_in, $compress=false) {
		return $compress ? $this->compress($script_in) : $script_in;
	}

	public function compress($script_in) {
		try {
			$packer = new JavaScriptPacker($script_in);
		} catch(Exception $e) {
			throw new PreprocessFailureException(array('processor'=>'JavaScriptPacker'));
		}
		return $packer->pack();
	}
}