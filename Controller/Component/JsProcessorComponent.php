<?php
/*

	JsProcessorComponent - Javascript Preprocessor
	----------------------------------------------

	@file 		JsProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::uses('iProcessor', 'ResourcesController.Lib');
App::import('ResourcesController.Vendor', 'JavascriptPacker', array('file' => 'JavascriptPacker' . DS . 'class.JavascriptPacker.php'));

class JsProcessor extends Component implements iProcessor {
	public function process($script_in, $compress=false) {
		return $compress ? $this->compress($script_in) : $script_in;
	}

	public function compress($script_in) {
		try {
			$packer = new JavascriptPacker($script_in);
		} catch(Exception $e) {
			throw new PreprocessFailureException(array('processor'=>'JavascriptPacker'));
		}
		return $packer->pack();
	}
}