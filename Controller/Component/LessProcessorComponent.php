<?php
/*

	LessProcessorComponent - LESS Preprocessor
	----------------------------------------------

	@file 		LessProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

<<<<<<< HEAD
App::uses('CssProcessorComponent', 'ResourcesController.Component');
App::import('ResourcesController.Vendor', 'LessPHP/lessc');
=======
App::uses('CssProcessorComponent', 'ResourcesController.Controller/Component');
App::import('ResourcesController.Vendor', 'LessPHP', array('file' => 'LessPHP' . DS . 'lessc.inc.php'));
>>>>>>> d144c4ecb778c4cb9136a0f7d9dc8ebf3d489a0f

class LessProcessorComponent extends CssProcessorComponent {
	/**
	 * Less Processor
	 * 
	 * @access private
	 */
	private $__lessc;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->__lessc = new lessc();
	}

	public function process($script_in, $compress=false) {
		try {
			$script_out = $this->__lessc->parse($script_in);
		} catch(Exception $e) {
			throw new PreprocessFailureException(array('processor'=>'LessPHP'));
		}

		return $compress ? $this->compress($script_out) : $script_out;
	}
}