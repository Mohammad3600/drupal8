<?php

/*
	General class for shared ajax/xml functions 
*/
class xml_ajax{
	var $doc ;
	var $r ; // root component, set by children
	var $db;
	var $headers_order;	
	function __construct(){
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->db = \Drupal::service('setup_project.sbom_db');
		$this->db->disable_defualt_pD_action();
		if ($headers_order=$this->clean_get('headers')) {
			$this->headers_order = $headers_order;
		}	
	}
	
	/* 	gets a variable from get and checks it's in a given format
		exists = variable must exist or error thrown
		is_numeric = variable must be numeric or error thrown
	*/
	function clean_get($name,$exists = false, $is_numeric=false,$default=null) {
		$get = $_GET[$name];
		if ($exists && !isset($get)) return '';  // FIXME send error!
		if (isset($get) && $is_numeric && !is_numeric($get)) return ''; // FIXME send error!
		if ($default !== null && !isset($get)) return $defualt; 
		return $get;
	}
	
	/*
		Returns information in XML format 
	*/
	function print_xml() {
		header("Content-type:text/xml");
		$this->fill_xml();
		print $this->doc->saveXML();
		exit();
	}
	
	/*
		Fills the grid with the data
	*/
	
	function fill_xml() {
	}
	
	// create,return a xml node in document $doc under parent $parent with $info 
	function make_xml_node(&$parent,$info ) {
		$c = $parent->appendChild($this->doc->createElement($info['node']));
		if (is_array($info['attribute'])) {
			foreach($info['attribute'] as $key=>$at) {
				$c->setAttribute($key,$at);
			}
		}
		if (is_array($info['textNode'])) {
			foreach($info['textNode'] as $txt) {
				$c->appendChild($this->doc->createTextNode($txt));
			}
		}
		if (is_array($info['cdata'])) {
			foreach($info['cdata'] as $cdata) {
				$c->appendChild($this->doc->createCDATASection($cdata));
			}
		}
		return $c;
	}
	
	/* Takes an root $c, row of cells $cells, fills the cells
		Keys of cell should correspond to header ids. 
	*/
	function add_cells(&$c, $cells) {
		//if ($c == null) $c = $this->r; 
		if (!$this->headers_order) {
			foreach ($cells as $key=>$cell) {
				$cell['node'] = 'cell';
				$cc = $this->make_xml_node($c, $cell);
			} 
		} else {
			foreach ($this->headers_order as $header) {
				$cell = $cells[$header];
				$cell['node'] = 'cell';
				$cc = $this->make_xml_node($c, $cell);
			} 
		}
		
	}
		
}


?>