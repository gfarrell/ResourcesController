ResourcesController Plugin
===========================

A *CakePHP* plugin designed to handle the modular packaging of resources such as CSS and Javascript files without having to do tedious building/processing beforehand.

1. [License](#license-)  
2. [Usage](#usage-)
3. [Sample XML File](#samplexmlfile-)
4. [Milestones](#milestones-)
5. [People](#people-)



License <a id="license"></a>
----------------------------

This software is licensed under the MIT X11 License (http://www.opensource.org/licenses/mit-license.php)

Copyright (c) 2009-present Gideon Farrell

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Usage <a name="usage-"></a>
---------------------------

First load the plugin (*app/Config/bootstrap.php*):
    
    CakePlugin::load('ResourcesController');


Sample XML File <a name="samplexmlfile-"></a>
---------------------------------------------

    <?xml version="1.0"?>
    <package name="MyPackage" lang="js">
    	<out>
    		<file compression="true">mypackage.compressed.js</file>
    		<file>mypackage.uncompressed.js</file>
    	</out>
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
    ...


Milestones <a name="milestones-"></a>
-------------------------------------

* Integrate LESS preprocessing
* Use CakePHP native caching
* Write full configuration options

People <a name="people-"></a>
------------------------------

* Originally developed by Gideon Farrell [<me@gideonfarrell.co.uk>](mailto:me@gideonfarrell.co.uk)