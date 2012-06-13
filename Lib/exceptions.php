<?php
/*

	Plugin Exceptions 
	-----------------

	@file 		exceptions.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

class MissingResourceConfigurationException extends CakeException {
	protected $_messageTemplate = 'It seems that the configuration file for the ResourcesController plugin is missing. Please create ResourcesController.xml in the Config directory';
}

class MissingResourcePackageException extends CakeException {
	protected $_messageTemplate = 'It seems that the package %s cannot be found.';
}

class MissingResourceFileException extends CakeException {
	protected $_messageTemplate = 'It seems that the file %s (in package %s) cannot be found.';
}

class PreprocessFailureException extends CakeException {
	protected $_messageTemplate = 'The preprocessor (%s) has encountered an error.';
}