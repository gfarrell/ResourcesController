<?php
/*

	Dependency Calculator
	---------------------

	@file 		DependencyCalculator.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

App::import('ResourcesController.Lib', 'exceptions');

class DependencyCalculator {
	/**
	 * Contains the SimpleXML object that describes the packages.
	 * 
	 * @access private
	 */
	private $__xml;

	/**
	 * Contains a temporary list of the current (flattened) dependency tree.
	 * 
	 * @access private
	 */
	private $__list;

	/**
	 * Constructor
	 * 
	 * @param string $xml_input the XML describing the package as a string
	 * @return void
	 */
	public function __construct($xml_input) {
		$this->loadPackageDescription($xml_input);
	}

	/**
	 * loadPackageDescription
	 * Loads the XML describing the package.
	 * 
	 * @access public
	 * @param string $xml_input the XML describing the package as a string
	 * @return void
	 */
	public function loadPackageDescription($xml_input) {
		$this->__xml = new SimpleXMLElement($xml_input);
	}

	/**
	 * computePackage
	 * Computes the dependencies for the given package.
	 * 
	 * @access public
	 * @param string $package_name the name of the package
	 * @throws MissingResourcePackageException If package can't be found
	 * @return array flattened dependency tree with no repetition
	 */
	public function computePackage($package_name) {
		$package = $this->__xml->xpath('file[@name = "'.$package_name.'"]');
		
		// Initialise the file-list
		$root = false;
		if(!is_array($this->__list)) {
			$this->__list = array();
			$root = true;
		}


		if(!$package or $package->count() == 0) {
			throw new MissingResourcePackageException(array('package'=>$package_name));
		} else {
			$package = $package[0];
		}

		// First let's look at the imports
		$imports = $package->xpath('/imports');

		if($imports && $imports->count() > 0) {
			$imports = $imports->children();
			foreach($imports as $import) {
				if($import->getName() == 'package') {
					$this->__list = array_merge($this->__list, $this->computePackage((string)$import));
				} elseif($import->getName() == 'file') {
					$import = (string)$import;

					if(strpos($import, ':') > 0) {
						list($pkg, $fl) = explode(':', (string)$import);
					} else {
						$pkg = $package_name;
						$fl = $import;
					}
					
					// Don't look at the file if its dependencies have already been computed (and thus it is in the list)
					if(!in_array("$pkg:$fl", $this->__list)) {
						$this->__list = array_merge($this->__list, $this->computeFile($pkg, $fl));
					}
				}
			}
		}

		// Now let's look at the files in this package
		$files = $package->xpath('/files');

		if($files && $imports->count() > 0) {
			$files = $files->children();
			foreach($files as $file) {
				$file_name = $file['name'];
				
				if(!in_array("$package_name:$file_name", $this->__list)) {
					if($file->count() > 0) {
						$requires = $file->children();
						foreach($requires as $required_file) {
							if($required_file->getName() == 'requires') {
								$req = (string)$required_file;
								if(strpos($req, ':') >= 0) {
									list($pkg, $fl) = explode($req);
								} else {
									$pkg = $package_name;
									$fl = $req;
								}

								if($fl == '*') {
									$this->__list = array_merge($this->__list, $this->computePackage($pkg));
								} else {
									if(!in_array($fl, $this->__list)) {
										$this->__list = array_merge($this->__list, $this->computeFile($pkg, $fl));
									}
								}
							}
						}
					}
					array_push($this->__list, "$package_name:$file_name");
				}
			}
		}

		if($root) {
			$this->__list = array();
			return array_unique($this->_list);
		}

		return $this->__list;
	}

	/**
	 * computeFile
	 * Computes the dependencies for the given file in a particular package.
	 * 
	 * @access public
	 * @throws MissingResourcePackageException If package can't be found
	 * @throws MissingResourceFileException If file can't be found
	 * @param string $package_name the name of the package
	 * @param string $file_name the name of the file
	 * @param SimpleXMLElement $description the XML node describing the file
	 * @return array flattened dependency tree with no repetition
	 */
	public function computeFile($package_name, $file_name, $description = null) {
		if(is_null($description)) {
			$package = $this->__xml->xpath('/package[@name = "'.$package_name.'"]');
			if(!$package) {
				throw new MissingResourcePackageException(array('package'=>$package_name));
			} else {
				$package = $package[0];
			}

			$description = $package->xpath('file[@name = "'.$file_name.'"]');
			if(!$description) {
				throw new MissingResourceFileException(array('file'=>$file_name, 'package'=>$package_name));
			} else {
				$description = $description[0];
			}
		}
		
		// Initialise the file-list
		$root = false;
		if(!is_array($this->__list)) {
			$this->__list = array();
			$root = true;
		}

		// Get file requirements
		if(!in_array("$package_name:$file_name", $this->__list)) {
			if($file->count() > 0) {
				$requires = $file->children();
				foreach($requires as $required_file) {
					if($required_file->getName() == 'requires') {
						$req = (string)$required_file;
						if(strpos($req, ':') >= 0) {
							list($pkg, $fl) = explode($req);
						} else {
							$pkg = $package_name;
							$fl = $req;
						}

						if($fl == '*') {
							$this->__list = array_merge($this->__list, $this->computePackage($pkg));
						} else {
							if(!in_array($fl, $this->__list)) {
								$this->__list = array_merge($this->__list, $this->computeFile($pkg, $fl));
							}
						}
					}
				}
			}

			array_push($this->__list, "$package_name:$file_name");
		}

		if($root) {
			$this->__list = array();
			return array_unique($this->_list);
		}

		return $this->__list;
	}
}

?>