<?php
/*

	Resources Controller
	---------------------

	@file 		ResourcesController.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::uses('File', 'Utility');
App::uses('DependencyCalculator', 'ResourcesController.Lib');
App::uses('exceptions', 'ResourcesController.Lib');

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
	
	function resource() {
		$package = $this->request['package'];
		$file = $this->request['file'];

		$config = $this->__readConfig();
		$xml = new SimpleXMLElement($config);
		
		$pkgxml = $xml->xpath('/packages/package[@name="'.$package.'"]');

		if(!$pkgxml || count($pkgxml) == 0) {
			throw new MissingResourcePackageException(array('package'=>$package));
		} else {
			$pkgxml = $pkgxml[0];
		}

		$lang = $pkgxml['lang'];
		$path = $pkgxml['path'];

		if(is_null($pkgxml['forcecompression'])) {
			$pkgxml['forcecompression'] = false;
		} else {
			if($pkgxml['forcecompression'] == 'true') {
				$pkgxml['forcecompression'] = true;
			} else {
				$pkgxml['forcecompression'] = false;
			}

		}

		$compress = $pkgxml['forcecompression'] ? true : $this->request['compressed'];

		$depender = new DependencyCalculator($config);

		$files = is_null($file) ? $depender->computePackage($package) : $depender->computeFile($package, $file);

		$file_paths = $this->__getFileLocations($files, $xml, $pkgxml['lang']);

		// Cache
		// Before caching or not, we will look for the configure key "ResourcesController.Cache". If this is false, we won't perform any caching.
		
		$output = '';

		if(Configure::read('ResourcesController.Cache') !== false) {
			$key = is_null($file) ? 'ResourcesController.Cache.'.$package : 'ResourcesController.Cache.'.$package.'.'.$file;

			if($compress) { $key .= '.compressed'; }

			$cache_born = Cache::read($key.'.Created');

			$latest = $this->__getLatestModificationDate($file_paths);

			if($cache_born < $latest) {
				$output = $this->__processFiles($file_paths, $lang, $compress);
				Cache::write($key, $output);
			} else {
				$output = Cache::read($key);
			}
		} else {
			$output = $this->__processFiles($file_paths, $lang, $compress);
		}

		$this->__sendHeaders($lang);
		$this->set('resources', $output);
	}

	/**
	 * __readConfig
	 * 
	 * @access private
	 * @throws MissingResourceConfigurationException If config file can't be found
	 * @return string configuration file contents
	 */
	
	private function __readConfig() {
		$file = new File(APP . DS . 'Config' . DS . 'ResourcesController.xml');

		if(!$file->exists()) {
			throw new MissingResourceConfigurationException();
		}

		return $file->read();
	}

	/**
	 * __getFileLocations
	 * 
	 * @access private
	 * @throws MissingResourcePackageException If a package's config cannot be found
	 * @param array $files a list of files in the package:file format
	 * @param SimpleXMLElement $xml the xml config
	 * @param string $ext the file extension (taken from the package language)
	 * @return array file paths
	 */
	
	private function __getFileLocations($files, SimpleXMLElement $xml, $ext) {
		$package_paths = array();
		$file_paths = array();

		foreach($files as $file) {
			list($pkg, $fl) = explode(':', $file);

			if(!in_array($pkg, $package_paths)) {
				$package = $xml->xpath('/packages/package[@name="'.$pkg.'"]');

				if(!$package || count($package) == 0) {
					throw new MissingResourcePackageException(array('package'=>$pkg));
				}

				$package_paths[$pkg] = (string)$package[0]['path'];
			}

			$path = $package_paths[$pkg];

			array_push($file_paths, $path . DS . $fl . '.' . $ext);
		}

		return $file_paths;
	}

	/**
	 * __getLatestModificationDate
	 * Returns the latest modification date of a list of files
	 * 
	 * @access private
	 * @param array $files an array of file paths
	 * @return int the latest modification time (as a unix timestamp)
	 */
	
	private function __getLatestModificationDate($files) {
		$d = 0;

		foreach($files as $file) {
			$df = filemtime(ROOT . DS . $file);

			if($df > $d) $d = $df;
		}

		return $d;
	}

	/**
	 * __readFiles
	 * Reads the given files into a string.
	 * 
	 * @access private
	 * @param array $files an array of file paths
	 * @return string the output of all the files
	 */
	
	private function __readFiles($files) {
		$out = '';

		foreach($files as $file) {
			$f = new File(ROOT . DS . $file);
			$out .= $f->read();
			$out .= " \n";
		}

		return $out;
	}

	/**
	 * __processFiles
	 * Processes the given files.
	 * 
	 * @access private
	 * @param array $files the list of file paths to process
	 * @param string $lang the language to process for
	 * @param bool $compress whether or not to compress the output as well
	 * @return string file processing output
	 */
	
	private function __processFiles($files, $lang, $compress) {
		$output = $this->__readFiles($files);

		$processor = ucfirst($lang).'Processor';
		$this->$processor = $this->Components->load('ResourcesController.'.$processor);
		if($this->$processor) {
			$output = $this->$processor->process($output, $compress);
		}

		return $output;
	}

	/**
	 * __sendHeaders
	 * Sends the appropriate mime/headers for this language.
	 * 
	 * @access private
	 * @param string $lang the language for which to send headers
	 * @return void
	 */
	
	private function __sendHeaders($lang) {
		switch($lang) {
			case 'js':
				$ctype = 'text/javascript';
				break;
			case 'css':
			case 'less':
				$ctype = 'text/css';
				break;
			default:
				$ctype = 'text/plain';
		}

		$this->response->header('Content-type', $ctype);
		//$this->response->compress();
	}
}
?>