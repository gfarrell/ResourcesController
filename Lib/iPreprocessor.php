<?php
/*

	Preprocessor Interface
	----------------------

	@file 		iPreprocessor.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

if(!class_exists('MissingResourcePackageException')) {
	App::import('ResourcesController.Lib', 'exceptions');
}

interface iPreprocessor {
	/**
	 * process
	 * Processes the script.
	 * 
	 * @access public
	 * @param string $script_in the script to be processed
	 * @param bool $compress whether or not to perform compression
	 * @return string processed script
	 */
	public function process($script_in, $compress=false);

	/**
	 * compress
	 * Compresses the script.
	 * 
	 * @access public
	 * @param string $script_in the script to be compressed
	 * @return string compressed script
	 */
	public function compress($script_in); 
}