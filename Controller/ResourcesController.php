<?php
/*

	Resources Controller
	---------------------

	@file 		ResourcesController.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

class ResourcesController extends ResourcesControllerAppController {
	var $layout = 'ResourcesController.blank';
	var $uses = null;
	var $components = array('Auth');
	
	/**
	 * beforeFilter sets auth to allow the resource action without authentication.
	 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('resource'));
	}

	/**
	 * resource action.
	 * 
	 * @access public
	 * @param string $package the package to look in.
	 * @param string $file [optional] the file to look for (if blank then do whole package)
	 */
	function resource($package, $file=null) {

	}

	/**
	 * __readFiles
	 * Reads the given files into a string.
	 * 
	 * @access private
	 * @param array $list an array of files in the form package:file
	 * @return string the output of all the files
	 */
	private function __readFiles($list) {

	}
}
?>