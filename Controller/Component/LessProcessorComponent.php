<?php
/*

	CssProcessorComponent - CSS Preprocessor
	----------------------------------------------

	@file 		CssProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::uses('CssProcessorComponent', 'ResourcesController.Component');
App::import('ResourcesController.Vendor', 'LessPHP', array('file' => 'LessPHP' . DS . 'lessc.inc.php'));

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
		$script_out = $this->__lessc->parse($script_in);

		return $compress ? $this->compress($script_out) : $script_out;
	}
}