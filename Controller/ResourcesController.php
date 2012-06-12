<?php
/*

	Resources Controller
	---------------------

	@file 		ResourcesController.php
	@author 	Gideon Farrell <me@gideonfarrell.co.uk>

	Copyright (c) 2012 Gideon Farrell <http://www.gideonfarrell.co.uk>

*/

class ResourcesController extends ResourcesControllerAppController {
	var $layout = "blank";
	var $uses = null;
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array("css", "javascript"));
	}

	/**
	 * Loads CSS
	 * 
	 * @method css
	 * @access public
	 * @returns void
	*/
	
	public function css() {		
		$this->log("Compiling CSS resources at " . $_SERVER["REQUEST_URI"], "debug");
		$this->set("css", $this->_compile(CSS, "css", $this->params["named"]));
	}
	
	/**
	 * Minifies css to a certain extent.
	 * 
	 * @method _minifyCss
	 * @access private
	 * @param string $css
	 * @returns string minified css
	*/
	
	public function _minifyCss($css) {
		$regex = array(
			"`^([\t\s]+)`ism"=>'',
			"`^\/\*(.+?)\*\/`ism"=>"",
			"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
			"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
			"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n",
			"`\s+`" => " "
		);
		$css = preg_replace(array_keys($regex),$regex,$css);
		$css = preg_replace( '#\s+#', ' ', $css );
		$css = str_replace( '; ', ';', $css );
		$css = str_replace( ': ', ':', $css );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( '{ ', '{', $css );
		$css = str_replace( ', ', ',', $css );
		$css = str_replace( '} ', '}', $css );
		$css = str_replace( ';}', '}', $css );
	
		return trim( $css );
	}
	
	/**
	 * Loads Javascript
	 * 
	 * @method javascript
	 * @access public
	 * @returns void
	*/
	
	public function javascript() {	
		$this->log("Compiling Javascript resources at " . $_SERVER["REQUEST_URI"], "debug");	
		$this->set("js", $this->_compile(JS, "js", $this->params["named"]));
	}
	
	/**
	 * Packs Javascript
	 * 
	 * @method _packJavascript
	 * @access public
	 * @param string $js
	 * @returns string packed javascript
	*/
	
	public function _packJavascript($js) {
		//Minification disabled for production.
		//App::import("Vendor", "JavaScriptPacker", array("file"	=>	"class.JavaScriptPacker.php"));
		//$packer = new JavaScriptPacker($js, 0, true, true);
		//return $packer->pack();
		return $js;
	}
	
	/**
	 * Gets all the resources from an include array (pkg_name => comma_separated_list_of_files) and compiles them. Also handles caching.
	 * 
	 * @method _compile
	 * @access private
	 * @param string $root_dir
	 * @param string $extension
	 * @param array $include_array
	 * @returns string compiled resource files
	*/
	
	private function _compile($root_dir, $extension, $include_array) {
		ksort($include_array);
		$cache_file = "";
		$files = array();
		$i = 0;

		foreach($include_array as $pkg => $fls) {			
			if($fls == "" || is_null($fls)) {
				$fls = "all";
			} else {
				$tmp = explode(",", $fls);
				asort($tmp);
				$fls = $args[$pkg] = implode(",", $tmp);
			}
			if($i > 0) { $cache_file .= "+"; }
			$cache_file .= $pkg . "[" . $fls . "]";	
			
			$description_file = new File("{$root_dir}/{$pkg}/description.xml");
			
			$dscr = new SimpleXMLElement($description_file->read());
			
			if($fls == "all") {
				$flist = $dscr->xpath("file");
				foreach($flist as $f) {
					$this->_getResourceFileRequisites($root_dir, $dscr, $pkg . ":" . $f["name"], $files);
				}
			} else {
				$includes = explode(",", $fls);
				foreach($includes as $incl) {
					$this->_getResourceFileRequisites($root_dir, $dscr, $pkg . ":" . $incl, $files);
				}
			}
		}
		
		$cache_file = CACHE . md5($cache_file) . ".{$extension}.cache";
		
		$errors = array();
		
		foreach($files as $f) {
			list($pkg, $f) = explode(":", $f);
			if(!file_exists("{$root_dir}/{$pkg}/{$f}.{$extension}")) {
				$this->log("- [NOTICE] Specified file \"$f\" does not exist in the package \"$pkg\".", "debug");
				array_push($errors, "Specified file \"$f\" does not exist in package.");
				$k = array_search($f, $files);
				unset($files[$k]);
			}
		}
		
		$cache_valid = true;
		if(file_exists($cache_file)) {
			$cache_valid = true;
			$cmodt = filemtime($cache_file);
			foreach($files as $f) {
				list($pkg, $f) = explode(":", $f);
				if(filemtime("{$root_dir}/{$pkg}/{$f}.{$extension}") > $cmodt) {
					$cache_valid = false;
					break;
				}
			}
		} else {
			$cache_valid = false;
		}

		if($cache_valid) {
			$cache = new File($cache_file);
			if($cache->exists()) {
				$data = $cache->read();
			}
		} else {
			$data = "";
			foreach($files as $f) {
				list($pkg, $f) = explode(":", $f);
				$file = new File("{$root_dir}/{$pkg}/{$f}.{$extension}");
				if($file->exists()) {
					$data .= $file->read();
				}
			}
			
			switch($extension) {
				case "css":
					$data = $this->_minifyCss($data);
					break;
				case "js":
					$data =	$this->_packJavascript($data);
					break;
			}
			$cache = new File($cache_file, true);
			
			$cache->write($data);
		}
		
		foreach($errors as $error) {
			$data .= "\n /* $error */";
		}	
		
		return $data;
	}
	
	
	/**
	 * Gets requisites for a given resource file.
	 * 
	 * @method _getResourceFileRequisites
	 * @access private
	 * @param string $root_dir
	 * @param SimpleXMLElement $description
	 * @param string $file
	 * @param array &$files
	 * @returns void
	*/
	
	private function _getResourceFileRequisites($root_dir, $description, $file, &$files) {
		$tmp = explode(":", $file);
		$pkg = $tmp[0];
		$file = $tmp[1];
		$reqs = $description->xpath("file[@name = \"$file\"]/requires");

		if(is_array($reqs) && count($reqs) > 0) {
			foreach($reqs as $req) {
				$tmp = explode(":", $req);
				if(count($tmp) > 1) {
					$rpkg = $tmp[0];
					$req = $tmp[1];
					
					$dscrFile = new File("/{$root_dir}/{$rpkg}/description.xml");
					$dscr = new SimpleXMLElement($dscrFile->read());
				} else {
					$dscr = $description;
					$rpkg = $pkg;
				}
				$this->_getResourceFileRequisites($root_dir, $dscr, $rpkg . ":" . (string)$req, $files);
				
				if(!in_array($rpkg . ":" . (string)$req, $files)) {
					array_push($files, $rpkg . ":" . (string)$req);
				}

			}
		}

		if(!in_array($pkg . ":" . $file, $files)) {
			array_push($files, $pkg . ":" . $file);
		}
	}
}
?>