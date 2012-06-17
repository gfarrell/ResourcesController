<?php
/*

	LessProcessorComponent - LESS Preprocessor
	----------------------------------------------

	@file 		LessProcessorComponent.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::import('ResourcesController.Vendor', 'LessPHP/lessc');
App::uses('CssProcessorComponent', 'ResourcesController.Controller/Component');

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