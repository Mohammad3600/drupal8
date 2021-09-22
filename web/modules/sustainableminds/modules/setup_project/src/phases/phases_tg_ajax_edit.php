<?php
/*
	Process the edits from a treegrid
*/
class phases_tg_ajax_edit extends xml_ajax {
	var $phaseID = 0 ; // the phase of the tree
	var $row_id = 0;
	var $rID = 0; // real ID of the edited row
	var $type = 0 ; // type of the edited row (compoent, item, matproc, etc.)
	var $status = 0 ; // type of edit: delete, update, etc.
	var $actionPerform ;
	var $eol ;
	var $out_row_id;
	var $conceptID;
	var $root;
	var $treegrid_get;
	function __construct($phaseid) {
		parent::__construct();
		$this->phaseID =  $phaseid ;
		$this->row_id = $this->clean_get('gr_id',true,false);
		$this->rID = $this->clean_get('rID',true,true); 
		$this->type = $this->clean_get('type',true); 
		$this->status = $this->clean_get('!nativeeditor_status');
		$this->eol = 'noteol';
		$this->out_row_id = $this->row_id; 
		$this->conceptID =$this->clean_get('conceptID',true);
		$this->root =$this->clean_get('root',true);  
		if ($this->status == 'deleted') $this->db->disable_message();
	}
	// fill top level phases_tg_get for getting total column info on updates. 
	function fill_treegrid_get() {
		if (empty($this->treegrid_get)) $this->treegrid_get = new  phases_tg_ajax_get($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root, 'type'=>COMPONENT_TYPE_CHECK)) ;
	}
	function fill_xml() {
		$this->r = $this->make_xml_node($this->doc, array('node'=>'data'));
		switch($this->status) {
			case 'deleted': // something was deleted
				$this->delete();
				break;
			case 'inserted': // something was moved/inserted
				$this->insert();
				break;
			default: // something was updated 
				if (!$this->status) $this->update();
				break;
		}
		if (!$this->treegrid_get) $this->fill_treegrid_get();
		$total =  $this->treegrid_get->total();
		 $this->make_xml_node($this->r, array('node'=>'action','attribute'=>array('type'=>'update_row', 
						'sid'=> 'total', 'tid'=> 'total',
						'okala'=>$total['okala'],'co2'=>$total['co2'],'ms'=>$total['ms']) ));
		 $this->make_xml_node($this->r, array('node'=>'action','attribute'=>array('type'=>$this->actionPerform, 
										'sid'=> $this->row_id, 'tid'=> $this->out_row_id) ));
		if ($this->db->get_pD()==true && $this->status != 'deleted') {
			$this->make_xml_node($this->r, array('node'=>'action','attribute'=> array('type'=>'permissionDenied')));
		}
	}
	
	// send action update_rows
	// send component new okala/co scores, starting at $componentid
	
	function send_components_update_row($componentid) {
		do { // update okala/co2 amounts effected by deleted row
				$component = $this->db->get_component($componentid);
				$okala =  format_numbers($component['okala_phase' . $this->phaseID]);
				$co2 = format_numbers($component['co2_phase' . $this->phaseID]);					
				$c = $this->make_xml_node($this->r, array('node'=>'action','attribute'=>array('type'=>'update_row', 
						'sid'=> COMPONENT_CHAR.$componentid, 'tid'=> COMPONENT_CHAR.$componentid,
						'okala'=>$okala,'co2'=>$co2,'ms'=>$component["ELM"]) ));	
				$componentid = $component['parentID'];
			} while($component['parentID'] > 0 && $component['componentTypeID'] == 0);
	}
	
	// perform insert based on type. Component and items can only be moved
	
	function insert() {
		$this->actionPerform='inserted';
		// real insert/move
		
		// see if it's a move if move is set 
		$moveID = $this->clean_get('move'); 
		switch($this->type) {
			case COMPONENT_TYPE_CHECK: // something was deleted
				if ($moveID)$this->move_component($moveID);
				break;
			case ITEM_TYPE_CHECK:
				if ($moveID) $this->move_item($moveID);
				break;	
		}
		$this->out_row_id = 'new_'.$this->out_row_id;
	}
	
	// change parent ID of component to moveID
	function move_component($moveID) {
		$this->db->update_component_parent($this->rID,$moveID);
	}
	// change parent ID of item to moveID
	function move_item($moveID) {
		$this->db->update_item_component($this->rID,$moveID);
	}
	// perform delete, change via what type it is
	function delete() {
		// no processing for a fake delete
		// will not effect database
		$this->actionPerform='delete';
		if ($this->clean_get('fake_delete')) { 
			return;
		}
		// real delete, will effect database
		switch($this->type) {
			case COMPONENT_TYPE_CHECK: // something was deleted
				$this->delete_component();
				break;
			case ITEM_TYPE_CHECK:
				$this->delete_item();
				break;	
			case PROCESS_TYPE_CHECK: // something was updated 
				$this->delete_process();
				break;
		}
	}
	
	
	/*DELETE component*/
	function delete_component() {
		$component = $this->db->get_component($this->rID);
		$componentid = $component['parentID'];
		$this->db->delete_component($this->rID);
		$this->send_components_update_row($componentid);
	}
	
	/*DELETE item */
	function delete_item() {
		$item = $this->db->get_item($this->rID);
		$this->db->delete_item($this->rID);
		$this->send_components_update_row($item['componentID']);
	}
	
	
	/* Delete Process
		Materials are only deleted through delete item./ 
	*/
	function delete_process() {
		$itemmatproc =$this->db->get_itemmatproc($this->rID);
 		$itemid = $itemmatproc['itemID']; 
 		
 		// delete item, update item row
		$this->db->delete_process($itemid,$this->rID);
		$item = $this->send_item_update_row($itemid);
		
		// send item_de_process in case the item no longer has processes (item to item_single)
		if (count($matprocs = $this->db->list_matproc($itemid, $this->eol)) == 1) {
			$matproc = $matprocs[0];
			$this->make_xml_node($this->r, array('node'=>'action',
				'attribute'=>array('type'=>'item_de_process', 
				'sid'=> ITEM_CHAR. $itemid, 'tid'=> ITEM_CHAR. $itemid,
				'name'=>$matproc['name'],'amount'=>$matproc['factor'],
				'unit_symbol'=>$matproc['unit_symbol'], 'matID'=>PROCESS_CHAR . $matproc['itemMatProcID'] 
				) ));
		}
		$this->send_components_update_row($item['componentID']);
	}
	
	// perform update, switch over type
	function update() {
		$this->actionPerform='update';
		$cellEdited = $this->clean_get('col_edited',true,false);
		$value = $this->clean_get($cellEdited,true,false);
		switch($this->type) {
			case COMPONENT_TYPE_CHECK: // something was deleted
				switch($cellEdited){
					case 'name': 
					$this->update_component_name($value);
					break;
					case 'desc': 
					$this->update_component_desc($value);
					break;
				}
				break;
			case ITEM_TYPE_CHECK:
				switch($cellEdited){
					case 'name': 
						$this->update_item_name($value);
					break;
					case 'desc': 
						$this->update_item_desc($value);
					break;
					case 'amt': 
						$this->update_item_amt($value);
					break;
				}
				break;	
			case PROCESS_TYPE_CHECK: // something was updated 
				switch($cellEdited){
					case 'amt': 
						$this->update_matproc_amt($value); // NOT just process
					break;
				}
				break;
		}
	}
	
	// update functions
	
	// component name
	function update_component_name($value) {
		$this->db->update_component_name($this->rID,$value);
	}
	
	// component comment (description)
	function update_component_desc($value) {
		$this->db->update_component_description($this->rID,$value);
	}
	
	
	// item name
	function update_item_name($value) {
		$this->db->update_item_name($this->rID,$value);
	}
	// item comment(description)
	function update_item_desc($value) {
		$this->db->update_item_description($this->rID,$value);
	}
	// item amt
	function update_item_amt($value) {
		$item = $this->db->get_item($this->rID);
		$this->db->set_imp_factor($item['itemMatProcID'],$value);
		$item = $this->send_item_update_row($this->rID);
		$this->send_components_update_row($item['componentID']);
	}
	
	function update_matproc_amt($amount) {		
		$itemmatproc =$this->db->get_itemmatproc($this->rID);
 		$itemid = $itemmatproc['itemID']; 
 		$item = $this->db->get_item($itemid);
		$proc =$this->db->get_matproc($itemmatproc['matProcID']);
		
		// sets the factors of all matprocs of this item that need to be set cause of this change
		$this->db->set_imp_factor($this->rID,$amount);	
		
		// go through processes, update if necessary
		$processes = $this->db->list_matproc($itemid,'both');
		foreach($processes as $key=>$value) {
			$this->send_process_update_row($value);				
		}	
		$item = $this->send_item_update_row($itemid);
		$this->send_components_update_row($item['componentID']);
						
	}
	
	function send_process_update_row($process) {
		$okala = round($process['okala'],ROUND_AMOUNT);
		$co2 = round($process['co2'],ROUND_AMOUNT);
		$info =array('type'=>'update_row', 
						'sid'=> PROCESS_CHAR. $process['itemMatProcID'], 'tid'=> PROCESS_CHAR.$process['itemMatProcID'],
						'okala'=>$okala,'co2'=>$co2, 'amount'=>$process['factor']); 
		$this->make_xml_node($this->r, array('node'=>'action','attribute'=>$info ));				
	}
	// item shared functions
	
	function get_item_okala($item) {
		return  format_numbers($item['okalaNotEOL']);
	}
	function get_item_co2($item) {
		return  format_numbers($item['co2NotEOL']);
	}
	
	// send the action to update an item row
	// return the item in case item is needed for further operations
	function send_item_update_row($itemid) {
		$item = $this->db->get_item($itemid);
		$okala = $this->get_item_okala($item);
		$co2 = $this->get_item_co2($item);
		$this->make_xml_node($this->r, array('node'=>'action','attribute'=>array('type'=>'update_row', 
						'sid'=> ITEM_CHAR. $itemid, 'tid'=> ITEM_CHAR. $itemid, 'amount'=>$item['factor'],
						'okala'=>$okala,'co2'=>$co2,'ms'=>$item["measurement"]) ));
		return $item;				
	}
	
}


/*
	Manufactoring phase treegrid edits
*/
class phases_tg_ajax_edit_manufacturing extends phases_tg_ajax_edit{
	function __construct(){
	 	parent::__construct(PHASEID_MANUFACTURE);
	}
	function fill_treegrid_get() {
		$this->treegrid_get = new  phases_tg_ajax_get_manufacturing($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root,'type'=>COMPONENT_TYPE_CHECK)) ;
	}
}

/*
	Use phase treegrid edits
*/
class phases_tg_ajax_edit_use extends phases_tg_ajax_edit{
	function __construct(){
	 	parent::__construct(PHASEID_USE);;
	}
	
	function fill_treegrid_get() {
		$this->treegrid_get = new  phases_tg_ajax_get_use($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root,'type'=>COMPONENT_TYPE_CHECK)) ;
	}
}

/*
	End of life phase treegrid edits
*/
class phases_tg_ajax_edit_eol extends phases_tg_ajax_edit{
	function __construct(){
	 	parent::__construct(PHASEID_EOL);
	 	$this->eol = 'eol';
	}
	function get_item_okala($item) {
		return  format_numbers($item['okalaEOL']);
	}
	function get_item_co2($item) {
		return  format_numbers($item['co2EOL']);
	}
	function fill_treegrid_get() {
		$this->treegrid_get = new  phases_tg_ajax_get_eol($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID,'parentID'=>$this->root,'type'=>COMPONENT_TYPE_CHECK)) ;
	}
}

/*
	Transportation phase treegrid edits
*/
class phases_tg_ajax_edit_transportation extends phases_tg_ajax_edit{
	function __construct(){
	 	parent::__construct(PHASEID_TRANSPORT);
	}	
	function fill_treegrid_get() {
		$this->treegrid_get = new  phases_tg_ajax_get_transportation($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root,'type'=>COMPONENT_TYPE_CHECK)) ;
	}
}
?>