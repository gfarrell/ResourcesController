ResourcesController Plugin
===========================

A *CakePHP* plugin designed to handle the modular packaging of resources such as CSS and Javascript files without having to do tedious building/processing beforehand.

1. [License](#license-)  
2. [Usage](#usage-)
3. [Sample Config](#sampleconfig-)
4. [Configuration File Documentation](#configurationfiledocumentation-)
5. [People](#people-)
6. [Other Credits](#othercredits-)


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

First you need to have a config file *ResourcesController.xml* in your app's *Config* directory. See the [Sample Config](#sampleconfig-) section to understand the format of this.

Load the plugin in *app/Config/bootstrap.php*:
   
    CakePlugin::load('ResourcesController');
    
The plugin defines custom routes so it is advised to also load its configuration files:

	CakePlugin::load(array(
		'ResourcesController' => array('routes'=>true)
	));

Then all you need to do is fetch resources using URLs like `http://myapp.dev/resources/package` or `http://myapp.dev/resources/package/file`. If you have not loaded the routes configuration, then you will have to use slightly messier URLs: `http://myapp.dev/resources_controller/resources/resource/package` and `http://myapp.dev/resources_controller/resources/resource/package/file`.


Sample Config <a name="sampleconfig-"></a>
---------------------------------------------

    <?xml version="1.0"?>
    <packages>
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
	    ...
	</packages>


Configuration File Documentation <a name="configurationfiledocumentation-"></a>
-------------------------------------------------------------------------------

### \<packages\> *root node*
This root node is required.

### \<package\> *package definition*
##### Required attributes:
- *name* the package name
- *lang* the package language (currently supported are 'js', 'css' and 'less')
- *path* the path to the package files (from the *ROOT*)

##### Optional attributes:
- *forcecompression* whether or not to force file compression, default is false. If this is false, then both compressed and uncompressed versions can be requested from the plugin, whereas if it is true, then only compressed versions will be served.


### \<import\> *defines imported files/packages*
The import tag has no attributes, but contains either `<file>` tags or `<package>` tags.

##### \<file\> *imported files*
Should be formatted as `package:file`

##### \<package\> *imported packages*
Should just contain the package name.

### \<files\> *the files in this package*
Each `<file>` node **must have** a `name` attribute, which is the filename _without the extension_. The `lang` attribute of the package will be used as the file extension. A file can have requirements, either from external packages or from within the same package.

##### \<requires\> *file requirements*
There can be multiple `<requires>` nodes for each `<file>`. The requirement can either be a file from another package, in which case it should be formatted as `package:file` or from the same package, in which case it should just contain the file name (same as the `name` attribute for the relevant `<file>` node).



People <a name="people-"></a>
------------------------------

* Originally developed by Gideon Farrell [<me@gideonfarrell.co.uk>](mailto:me@gideonfarrell.co.uk)


Other Credits <a name="othercredits-"></a>
-------------------------------------

* JavascriptPacker from [http://joliclic.free.fr/php/javascript-packer/en/](http://joliclic.free.fr/php/javascript-packer/en/)
* LessPHP by leafo: [https://github.com/leafo/lessphp](https://github.com/leafo/lessphp)