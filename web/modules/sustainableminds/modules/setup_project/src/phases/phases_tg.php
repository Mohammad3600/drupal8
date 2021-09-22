<?php
/*
	This is the base class for making a treegrid object
	based on phases
*/


/*
	Base class, can be used to render a basic phase treegrid 
	and includes the defualts
*/
namespace Drupal\setup_project\phases;
class phases_tg{
	var $edit = true; // whether the tree is in edit mode ; 
	var $component_type  = 0 ; // the type of component the root will be
	var $root = 0; // the root component, determinded via component_type 
	var $width = '100%' ;
	var $height = '200' ;
	var $phaseID = 0 ; // the phase of the tree
	var $conceptID = 0 ; // the concept the tree is displaying
	var $headers ; // array of headers 
	var $name = 'sbom'; // name used for treegrid dhtmlx
	var $autoheight='true';
	var $open_state_cookie;
	var $treegrid_get; // instanious of the phases_tg_get
	
	// sets the default elements. Child classes call this then set their special values
	function __construct($phaseid, $conceptid, $width='100%', $height='200'){
		$this->phaseID =  $phaseid ;
		$this->conceptID =  $conceptid ;
		$this->width =  $width;
		$this->height= $height; 
		// defualt headers 
		// Format of each header row :: array('name'=>$name,'width'=>$width,'type'=>$type,'c'=>$align,'sort'=>$sort,'visible'=>$visible,'id'=>$id);
		// by defualt headers are set for $edit == false
		// tweaked some of the numbers for the 'okala', 'co2' and 'actions' columns to accomodate the new copy icon. - KJH
		$this->headers = array(
			'desc'=> array('name'=>'', 'width'=>'0', 'type'=>'sub_row_new', 'c'=>'left', 'sort'=>'str','visible'=>'false','id'=>'desc' ),
			'name'=> array('name'=>'Name', 'width'=>'15', 'type'=>'tree', 'c'=>'left', 'sort'=>'sbom_treegrid_sort', 'visible'=>'false', 'id'=>'name' ),
			'matproc'=> array('name'=>'Material/Process', 'width'=>'16', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort', 'visible'=>'false', 'id'=>'matproc' ),
			'quantity'=> array('name'=>'Qty', 'width'=>'6', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort_num', 'visible'=>'false', 'id'=>'quantity' ),
			'amt'=> array('name'=>'Amt', 'width'=>'6', 'type'=>'ed', 'c'=>'left', 'sort'=>'sbom_treegrid_sort_num','visible'=>'false', 'id'=>'amt' ),
			'unit'=> array('name'=>'Unit', 'width'=>'4', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort', 'visible'=>'false', 'id'=>'unit' ),				
			'okala'=> array('name'=>MPTS_UNIT_LABEL, 'width'=>'10', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort_num', 'visible'=>'false', 'id'=>'okala' ),
			'co2'=> array('name'=>CO2_EQ_LABEL, 'width'=>'10', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort_num','visible'=>'false','id'=>'co2' ),
			'ms'=> array('name'=>'MS', 'width'=>'3', 'type'=>'ms', 'c'=>'left', 'sort'=>'sbom_treegrid_sort','visible'=>'false','id'=>'ms' ),
			'partID'=> array('name'=>'Part ID', 'width'=>'7', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort', 'visible'=>'false',
'id'=>'partID' ),
			'actions'=> array('name'=>'', 'width'=>'23', 'type'=>'ro', 'c'=>'left', 'sort'=>'sbom_treegrid_sort', 'visible'=>'false', 'id'=>'actions' ),
			);
	}
	
	function disable_autoheight() {
		$this->autoheight = 'false';
	}
	
	// set the name
	function set_name($name) {
		$this->name = $name;
	}
	
	// alter header of id $id, change $type to $new
	function alter_header($id, $type, $new) { 
		if ($this->headers[$id]) $this->headers[$id][$type] = $new ; 
	}
	
	// remove/add headers. 
	function remove_header($id) {
		if ($this->headers[$id]) unset($this->headers[$id]);
	}
	function add_header($id,$info) {
		$this->headers[$id] = $info ; 
	}
	
	/*
		Enable edit mode. 
	*/
	function set_edit($edit = true) {
		$this->edit = $edit ; 
		if ($edit) {
			$this->alter_header('actions', 'visible', 'false');
			$sizes=array(
  				'name'=>'20',
  				'matproc'=>'15',
  				'amt'=>'7',
  				'unit'=>'7',
  				'ms'=>'7',
  				'okala'=>'10',
  				'co2'=>'10',
  				'desc'=>'0',
  				'actions'=>'24'
  			);
		} else {
			//$this->alter_header('actions', 'visible', 'true');
			$this->remove_header('actions');
			$sizes=array(
  				'name'=>'25',
  				'matproc'=>'25',
  				'amt'=>'10',
  				'unit'=>'10',
  				'ms'=>'10',
  				'okala'=>'10',
  				'co2'=>'10',
  				'desc'=>'0',
  				'actions'=>'0'
  			);
  		}
  		foreach ($this->headers as $key=>$arg ) if($sizes[$key] !== null) $this->alter_header($key, 'width', $sizes[$key]);		
	}
	
	/*
		Construct the loading message
	*/
	function construct_loading_message($div) {
		return '<div class="loading-message-hidden" id="'.$div.'">Loading Data...</div>';
	}
	/*
		Construct the buttons/button area that will appear above treegrid
	*/
	function construct_buttons() {
		return '';
	}
	
	function construct_button_area() {
		if (!$this->edit) return '';
		$buttons = $this->construct_buttons() ; 
		return '<div id="tree-header">'.$buttons.'</div>';
	}
	
	// get the root component from the tree based on component_type
	function find_root() {
		$db = \Drupal::service('setup_project.sbom_db');
		$component = $db->get_components_by_concept_and_type($this->conceptID, $this->component_type);
		$this->root =  $component['componentID'];
	}
	
	function phase_draw(&$treegrid) {
	}
	
	function fill_treegrid_get() {
		$this->treegrid_get = new phases_tg_ajax_get($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID , 'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root)) ;
	}
	
	function total() {
		if (!$this->treegrid_get) $this->fill_treegrid_get();
		return $this->treegrid_get->total();
	}
	
	function is_empty() {
		if (!$this->treegrid_get) $this->fill_treegrid_get();
		return $this->treegrid_get->check_empty_state();
	}
	
	function empty_text() {
		return 'Sorry, but you have not added anything here yet.';
	}
	
	// construct the treegrid and any other areas that it needs
	function draw() {
		$this->find_root();	
		$loadingdiv = $this->name . $this->phaseID."-loading-message";
		$output .=$this->construct_loading_message($loadingdiv);
		$output .= $this->construct_button_area();
		// $editmode = $this->edit ? '1' : '0';
		
		// // make an array of headers and serlaize it to be passed to the tree 
		// foreach ($this->headers as $key=>$value) $headers .= '&headers[]='.$key;
		// $treegrid = new dhtmlx_treegrid($this->name . $this->phaseID,"/".URL_AJAX_CONCEPT_GET_COMPONENTS."?parent=".$this->root."&edit=".$editmode."&phase=".$this->phaseID."&concept=".$this->conceptID .$headers ,$this->width,$this->height);
		
		// $this->phase_draw($treegrid);
		// if ($this->open_state_cookie) {
		// 	$treegrid->set_cookie_postfix($this->open_state_cookie);
		// }
		// $treegrid->set_header_from_array($this->headers);
		// $treegrid->set_dataprocessor('/sbom/ajax/concept/editcomponents?phase='.$this->phaseID .'&root='.$this->root.'&conceptID='.$this->conceptID);
		// $treegrid->add_action('update_row','sbom_update_row');
		// $treegrid->add_action('item_de_process','sbom_item_de_process');
		// $treegrid->add_action('permissionDenied','do_permission_denied');
		
		// $treegrid->set_enableAutoHeight($this->autoheight);
		
		// //Enabled all editing, on double click JS cancels the edit if depending on the row
		// $treegrid->set_onRowDblClicked('sustainable_minds_grid_cancel_edit');
		
		// //Before sending for data
		// $treegrid->set_onXLS('on_loading_grid');
		// //After data has been recieved
		// $treegrid->set_onXLE('on_finish_loading_grid');
		
		// //Disable edit for the comment cell - but still expand for viewing
		// $treegrid->set_onEditCell('sbom_stop_cell_edit');
		
		// //callback function for when treegrid populates data - used to send in extra userdata
		// $treegrid->set_onDynXLS('sbom_loadChildURL');
		// $treegrid->set_onOpenEnd('sustainable_minds_treegrid_onOpenEnd');

		// $treegrid->set_onRowSelect('sbom_treegrid_onRowSelect');
		// //Send in column(headers) text id (names) instead of number of column.
		// $treegrid->set_enableDataNames('true');
		
		// //FIXME  testing drag
		// if ($this->edit) {
		// 	/*$treegrid->set_enableDragAndDrop('true');
		// 	$treegrid->set_onDrag('sbom_treegrid_onDrag');
		// 	$treegrid->set_onBeforeDrag('sbom_treegrid_onBeforeDrag');
		// 	$treegrid->set_onDragIn('sbom_treegrid_onDragIn');
		// 	$treegrid->set_onDrop('sbom_treegrid_onDrop');*/
		// }
		// //On cell mouseover
		// $treegrid->set_onMouseOver('cancel_mouseover_of_actions');
		
		// //Send the userdata to the grid. In dhtmlx mod User data is set after first xml load
		// $treegrid->add_userData('phase',$this->phaseID);
		// //$treegrid->add_userData('loading_message_div',$loadingdiv);
		// $treegrid->add_userData('editmode',$this->edit ? '1' : '0');
		// // root of the tree
		// $treegrid->add_userData('root',$this->root);
		// // set loading message div.
		// $treegrid->set_loading_message_div($loadingdiv);
		// $output .= $treegrid->draw();
		
		return $output;
	}
}



/*
	Use phase treegrid information
*/
class phases_tg_use extends phases_tg {
	function __construct($conceptid, $width='875', $height='200') {
	 	parent::__construct(PHASEID_USE,$conceptid,$width,$height);
	 	$this->component_type = COMPONENT_ROOT_USE;
	 	//$this->alter_header('matproc', 'name', 'Material');
	 	$this->open_state_cookie='use';
	 	$this->alter_header('name', 'width', '18');
	 	$this->remove_header('quantity');
	 	$this->remove_header('partID');
	 	$this->alter_header('matproc', 'width', '24');
	 	$this->alter_header('matproc', 'name', 'Consumables/water/power');
	 	$this->alter_header('unit', 'width', '7');
	}
	
	function fill_treegrid_get() {
		$this->treegrid_get = new phases_tg_ajax_get_use($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID, 'conceptID'=>$this->conceptID,
			'phase_to_use'=>$this->phaseID, 'parentID'=>$this->root)) ;
	}
}

/*
	End of life phase treegrid information
*/
class phases_tg_eol extends phases_tg {
	function __construct($conceptid, $width='875', $height='200') {
	 	parent::__construct(PHASEID_EOL,$conceptid,$width,$height);
	 	$this->component_type = COMPONENT_ROOT_MAN;
	 	$this->open_state_cookie='eol';
	 	$this->alter_header('matproc', 'name', 'End of life method');
	}
	/*
	function phase_draw(&$treegrid) {
		// call a function to get all that should be open 
		// save in array in dhtmlx that overides open components?
		
		$db = \Drupal::service('setup_project.sbom_db');
		$components = $db->list_item_tree_no_eol($this->conceptID);
		
		foreach ($components as $c) {
			if ($c['type'] == 'component') $open[] = COMPONENT_CHAR. $c['ID'];
		}
		
		if ($open) $treegrid->set_additional_open_ids($open);
	}
	*/
	function fill_treegrid_get() {
		$this->treegrid_get = new phases_tg_ajax_get_eol($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID,'parentID'=>$this->root)) ;
	}
	
	function empty_text() {
		return TEXT_DEFAULT_SBOM_EMPTY_EOL;
	}
}

/*
	Transportation phase treegrid information
*/
class phases_tg_transportation extends phases_tg {
	function __construct($conceptid, $width='875', $height='200') {
	 	parent::__construct(PHASEID_TRANSPORT,$conceptid,$width,$height);
	 	$this->component_type = COMPONENT_ROOT_MAN;
	 	$this->alter_header('matproc', 'name', 'Transportation mode');
	 	$this->alter_header('name', 'width', '19');
	 	$this->alter_header('actions', 'width', '17');
	 	//$this->alter_header('amt', 'name', 'Distance');

	 	$this->open_state_cookie = 'transport';
	}
	/*
	function construct_buttons() {
		return '<div class="tree-action-button"><a href="/'.URL_ADD_MANUFACTURING_MATERIAL.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root.'">'.DEFAULT_TREEACTION_ADDTRANSPORTATION.'</a></div><div class="clear"></div>';

	}
	*/
	function fill_treegrid_get() {
		$this->treegrid_get = new  phases_tg_ajax_get_transportation($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root)) ;
	}
	
	function empty_text() {
		return TEXT_DEFAULT_SBOM_EMPTY_TRAN;
	}
}
?>
