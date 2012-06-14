<?php
/*

	DependencyCalculator Tests
	---------------------------

	@file 		DependencyCalculatorTest.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

class DependencyCalculatorTest extends CakeTestCase {
	public function setUp() {
		parent::setUp();
		$config_file = <<<EOF
<?xml version="1.0"?>
<package name="MyPackage" lang="js" path="/app/webroot/js/mypackage" forcecompression="true">
	<import>
		<file>OtherPackage:speciallib</file>
		<package>CoolLibrary</package>
	</import>
	<files>
		<file name="myscript">
			<requires>OtherPackage:otherscript</requires>
		</file>
		<file name="no_requirements" />
	</files>
</package>
<package name="OtherPackage" lang="js" path="/app/webroot/js/otherpackage" forcecompression="false">
	<files>
		<file name="speciallib" />
		<file name="otherscript" />
		<file name="libscriptreq" />
	</files>
</package>
<package name="CoolLibrary" lang="js" path="/app/webroot/js/coollibrary" forcecompression="false">
	<files>
		<file name="libscript1" />
		<file name="libscript2">
			<requires>OtherPackage:libscriptreq</requires>
		</file>
	</files>
</package>
EOF;
		$this->DependencyCalculator = new DependencyCalculator($config_file);
		$this->xml = new SimpleXMLElement($config_file);
	}

	public function testPackage() {
		$package = "OtherPackage";
		
	}
}

?>