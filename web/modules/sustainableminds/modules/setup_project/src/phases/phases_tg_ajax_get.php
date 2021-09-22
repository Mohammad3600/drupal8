<?php

/*
	Returns xml formated information based on phase for treegrids.
*/

class phases_tg_ajax_get extends xml_ajax {
	var $conceptID = 0; // conceptID
	var $phaseID = 0 ;
	var $phase_to_use; // phase to use when getting components 
	var $parentID = 0 ; // parentID
	var $returnParentID;
	var $parent ; // parent component/item
	var $eol ; // whether we want eol or noteol information
	// array containing the information that will be displayed
	var $components ;
	var $items;
	var $items_single;
	var $matprocs;
	var $edit = false;
	var $is_filled = false;
	var $ms_total =''; // ms amount for total
	var $ms_array = '';
	var $sub_components =array();
	var $sub_items= array();
	var $sub_items_single = array();
	var $enable_empty = true;
	
	function __construct($phaseid) {
		parent::__construct();
		$this->phaseID = $phaseid;
		$this->conceptID = $this->clean_get('concept',true,true);
		$this->type = $this->clean_get('type',false,false,COMPONENT_TYPE_CHECK);
		$this->gridDivName = $this->clean_get('gridDivName',true);
		$this->phase_to_use = $this->phaseID;
		$this->eol = 'noteol';
 		$id = $this->clean_get('rID',false,true);
 		
		if ($this->clean_get('edit',false)) $this->enable_edit();
		if (!$id) { // if id doesn't exist, parent is id but don't indicate that to grid
			$this->parentID = $this->clean_get('parent',false, true,0); // defualt parentID if id is not sent
			 // this is the root, first ajax call, so do not set returnParentID.
		} else { // the id is the parent
			$this->parentID = $id ;
			$this->returnParentID = $this->clean_get('id',true,false);
		}
		
		// get the parent component based on parentID, type
		if ($this->parentID && $this->conceptID) {
			if ($this->type == ITEM_TYPE_CHECK) {
				$this->parent = $this->db->get_item($this->parentID);
			}
			else {
				$this->parent = $this->db->get_component($this->parentID);
			}
		}
	}
	
	// Important: Match case and spelling of class variables
	
	function set_variables_from_array($array) {
		if (!is_array($array)) return; 
		foreach($array as $key=>$value) {
			$this->$key = $value; 
		}
		
		if ($this->parentID) {
			if ($this->type == ITEM_TYPE_CHECK) {
				$this->parent = $this->db->get_item($this->parentID);
			}
			else {
				$this->parent = $this->db->get_component($this->parentID);
			}
		}
	}
	
	// enable edit mode
	function enable_edit($enable=true) {
		$this->edit = $enable ; 	
	}
	
	// fill components array, might be overidden by child classes
	function fill_components() { 
		$this->components = $this->db->list_components_by_parent_and_phase($this->parentID, $this->phase_to_use);
		if (!is_array($this->components)) $this->components = array();
	}
	
	// fill items array, might be overided
	function fill_items() {
		$this->items = $this->db->list_items_with_process($this->parentID, $this->eol);
		if (!is_array($this->items))
			$this->items = array();
	}
	
	// fill items_single array (items without processes), might be overided
	function fill_items_single() {
		$this->items_single = $this->db->list_items_no_process($this->parentID, $this->eol);
		if (!is_array($this->items_single))
			$this->items_single = array();
	}
	
	// fill matprocs (only called with ITEM_TYPE_CHECK == type)
	function fill_matprocs() {
		$this->matprocs = $this->db->list_matproc_by_type($this->parentID, $this->eol, array('material','process'));
		if (!is_array($this->matprocs)) $this->matprocs = array();
	}
	
	// fills all the valid arrays
	function fill_all() {
		if ($this->type == ITEM_TYPE_CHECK):
			$this->fill_matprocs();
		else:
			$this->fill_components() ;
			$this->fill_items();
			$this->fill_items_single();
		endif;
		$this->is_filled = true;
	}
	
	// Call fill_all before calling either of these functions
	function check_empty_state($parent = null) {
		if ($this->returnParentID  || $parent) 
			return false; /*not top level*/ 
			
		if (!$this->is_filled) 
			$this->fill_all();
			
		if (empty($this->matprocs) && empty($this->items_single) && empty($this->items) && empty($this->components)) {
			return true;
		}
		return false;
	}
	
	function total() {
		if (!$this->is_filled) $this->fill_all();
		foreach($this->components as $component) {
			$okala += $this->get_component_okala($component);
			$co2 += $this->get_component_co2($component);
			$this->ms_total_update($component['ELM']);
			//watchdog('phases_tg_ajax_get', 'comp '.$component['componentID'].' CO2: '.$co2);
		}
		foreach($this->items as $items) {
			$okala += $this->get_item_okala($items);
			$co2 += $this->get_item_co2($items);
			$this->ms_total_update($items['measurement']);
			//watchdog('phases_tg_ajax_get', 'item '.$items['itemID'].' CO2: '.$co2);
		}
		foreach($this->items_single as $items) {
			$okala += $this->get_item_single_okala($items);
			$co2 += $this->get_item_single_co2($items);
			$this->ms_total_update($items['measurement']);
			//watchdog('phases_tg_ajax_get', 'item single '.$items['itemID'].' CO2: '.$co2);
		}
		return array('okala'=>$okala,'co2'=>$co2, 'ms'=>$this->ms_total); 
	}
	
	// helper functions for processing functions
	
	// div wrapper for add_actions 
	function ms_total_update($measurement) {
		if (!is_array($this->ms_array)) {
			$ms_temp = $this->db->list_measurementTypes();
			foreach ($ms_temp as $key=>$ms) {
				$this->ms_array[$ms['name']] = $ms['measurementTypeID'] ;
			}
		}
		if (!$measurement) return;
		if ( empty($this->ms_total) || $this->ms_array[$measurement] < $this->ms_array[$this->ms_total]) $this->ms_total = $measurement;
	}
	
	function ms_cell(&$measurement, $attribute_array='') {
		//$attribute_def_array = array('title'=>$measurement);
		//if (is_array($attribute_array)) $attribute_def_array = array_merge($attribute_array,$attribute_def_array);
		return array('attribute'=>$attribute_array,'cdata'=>array($measurement));
		//return array('attribute'=>$attribute_def_array,'cdata'=>array(substr($measurement,0,1)) );
	}
	
	function action_wrapper($actions) {
		return '<div class="concept-grid-actions">'.$actions . '</div>';
	}
	
	function comment_wrapper($comment,$id) {
		return $comment;
		return '<div id="concept-grid-comment-'.$id.'">'.$comment . '</div>';
	}
	function get_component_okala($component) {
		return $component['okala_phase' . $this->phaseID] ;
	}
	function get_component_co2($component) {
		return $component['co2_phase' . $this->phaseID];
		//watchdog('phases_tg_ajax_get', '1: comp '.$component['componentID'].' CO2: '.$co2);
	}	
	function get_component_quantity($component) {
		return format_numbers($component['quantity']) ;
	}
	function get_component_partID($component) {
		return $component['partID'];
	}	
	
	// returns bool whether a component has children
	function component_has_children($component) {
		if (count($this->db->list_components_by_parent_and_phase($component['componentID'], $this->phase_to_use)) > 0
				|| count($this->db->list_items_no_process($component['componentID'])) > 0 
				|| count($this->db->list_items_with_process($component['componentID'])) > 0) 
			return true;
			
		return false;
	}
	
	// returns the text for buttons and other text places
	function component_text($component) {
		return array('addMaterialText'=>DEFAULT_SBOM_ICON_ADDPART
			,'addComponentText'=>DEFAULT_SBOM_ICON_ADDSA
			,'editComponentText'=> DEFAULT_SBOM_ICON_EDIT
			,'copyText'=> DEFAULT_SBOM_ICON_COPY_URL     // KJH - new icon for copy functions
			,'addMaterialTitle'=> 'Add a ' . DEFUALT_ITEM_TEXT
			,'addComponentTitle'=>'Add a '. DEFUALT_COMPONENT_TEXT
			,'editComponentTitle'=>'Edit'
			,'deleteTitle'=>'Delete'
			,'copyTitle'=>'Copy'
			,'deleteText'=> DEFAULT_SBOM_ICON_DELETE_URL
			);
	}
	// BAG this is where we insert the new Copy component or item stuff
	// returns the action values
	// added the '$copy' - this is for a subassembly
	function component_actions($component, $addComponent='',$addMaterial='',$del='',$edit='',$copy='') {
		return $addMaterial . ' '. $addComponent .' '. $copy .' '. $edit .' '. $del ;
	}
	function will_have_sub($componet) {
		return false;
	}
	function top_level($component) {
		return false; 
	}
	
	// Processing functions, takes in root $r
	// uses fill functions to fill the corresponding variables.
	function process_components(&$r, $parent=0) {
		if ($parent) {
		 	if ($this->sub_components[$parent]) $components = $this->sub_components[$parent];
			else $components = array();
		} else {
			$this->fill_components();
			$components =  $this->components;
		}
		
		if (!$components) return;
                // debugUtils::watchdogCallStack(4); // singhj
		foreach ($components as $component) {
			$temp_id = COMPONENT_CHAR . $component['componentID'];
			$id = $component['componentID'];
			$texts = $this->component_text($component); // get text associated with the actions for phase
			$cid = $component['componentID'];
			
			$toplevel = $this->top_level($component);	
			if ($toplevel)
				$kids = $this->will_have_sub($component); /*seperated out due special empty handling*/
			else
				$kids = $this->component_has_children($component); // if the component has any children
				
                        $texts_string = implode(" ", $texts);
                        // watchdog('phases_tg_ajax_get', 'A1: texts '.$texts_string); // singhj
                        // watchdog('phases_tg_ajax_get', 'A2:  cid '.$cid.' kids '.(($kids) ? 'true' : 'false')); // singhj
			//edit specific information
			if ($this->edit) {
				$addComponent = '<a href="/'.URL_ADD_COMPONENT.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$component['componentID'].'" title="'.$texts['addComponentTitle'].'">'.$texts['addComponentText'].'</a>';
				$addMaterial = '<a href="/'.URL_ADD_MANUFACTURING_MATERIAL.'/'. $this->conceptID .'/'.$this->phaseID.'/'.$component['componentID'].'" title="'.$texts['addMaterialTitle'].'">'.$texts['addMaterialText'].'</a>';
				
				if ($component['phaseID'] == PHASEID_TRANSPORT) { //trans for assembled product - add trans to entire prod's manf component
					$addMaterial = '<a href="/'.URL_ADD_MANUFACTURING_MATERIAL.'/'. $this->conceptID .'/'.$this->phaseID.'/'.$component['parentID'].'" title="'.$texts['addMaterialTitle'].'">'.$texts['addMaterialText'].'</a>';

				}

				//if the component is a regular component or a trans cmpt, add delete & edit actions
				if ($component['componentTypeID'] == 0 || $component['phaseID'] == PHASEID_TRANSPORT) {
					$edit = '<a href="/'.URL_EDIT_COMPONENT.'/'.$this->conceptID.'/'.$this->phaseID.'/'
							.$component['componentID'].'" title="'.$texts['editComponentTitle'].'">'.$texts['editComponentText'].'</a>';
							/*
							$del = '<a href="JavaScript:void(0);" onclick="deleteRow(\''. $temp_id .'\',\''
							.$this->gridDivName.'\')"  title="'.$texts['deleteTitle'].'">'.$texts['deleteText'].'</a>';
							*/
					$del = '<input  type="image" onclick="deleteRow(\''. $temp_id .'\',\''
							.$this->gridDivName.'\')"  title="'.$texts['deleteTitle'].'" name="image" src="'.$texts['deleteText'].'"/>';
					// do not add a copy button for transportation phase items
					if ($component['phaseID'] != PHASEID_TRANSPORT) {
						// ultimately calling: function copy_sbom_component($componentID, $parentID, $conceptID, $isTop) {
						// KJH - attempt to put a copy component functionality on the button, using a javascript call
						$isTop=1; // temporary?
						$copy = '<input type="image" onclick="javascript:copy_sbom_component(\''. $component['componentID'] .'\',\'' .$this->parentID.'\',\'' .$this->conceptID .'\',\'' .$isTop.'\')"'
							.' name="image" title="'.$texts['copyTitle'].'" src="'.$texts['copyText'].'"/>';
					}
				}
				$actionValue = $this->component_actions($component, $addComponent, $addMaterial, $del, $edit, $copy); // added $copy - KJH
				// BAG more action stuff - this is where the actual div is defined for sub-assemblies - above
			}
			
			$attributeArray =  array('id'=>$temp_id, 'class'=>'component');
			// xmlkids = any value specifies kids exists for the treegrid
			if ($kids) {
				$attributeArray['xmlkids'] = '1';
				if ($toplevel)$attributeArray['open'] = 1;
			}
			
			if ($toplevel)
				$attributeArray['class'] .= ' toplevel'; 
				
			// container row 
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>$attributeArray ));
				 
			// make the xml nodes
			// nonvisible but accessible information
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'phase'), 'textNode'=>array( $component['phaseID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'componentTypeID'), 'textNode'=>array( $component['componentTypeID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( COMPONENT_TYPE_CHECK) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array( $component['componentID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'parent'), 'textNode'=>array( $parentComponent['name']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'substate'), 'textNode'=>array( $toplevel) ));
			$okala = $this->get_component_okala($component) ;
			$co2 = $this->get_component_co2($component) ;
			//watchdog('phases_tg_ajax_get', '5: comp '.$component['componentID'].' CO2: '.$co2);
			$quantity = $this->get_component_quantity($component) ;
			$partID = $this->get_component_partID($component) ;
			
			// Cells that will be displayed. 
			if ($toplevel) {
				$component_cells = array(
					 'name'		=>array('attribute'=>array('image'=>'folder.gif', 'class'=>'cell_title','rowspan'=>'9'), 
										'cdata'=>array($component["name"])),
					 'partID'	=>array( 'cdata'=>array('') ),
					 'quantity'	=>array( 'cdata'=>array('') ),
					 'matproc'	=>array( 'cdata'=>array('') ),
					 'amt'		=>array( 'cdata'=>array('') ),
					 'unit'		=>array( 'cdata'=>array('') ), 
					 'ms'		=>array( 'cdata'=>array('') ),
					 'okala'	=>array( 'cdata'=>array('') ),
					 'co2'		=>array( 'cdata'=>array('') ),
					 'desc'		=>array( 'cdata'=>array('') ),
					 'actions'	=>array('attribute'=>array('title'=>'  '), 'cdata'=>array($this->action_wrapper($actionValue)))
				);
			} else {
				$component_cells = array(
					 'name'		=>array('attribute'=>array('image'=>'folder.gif', 'class'=>'cell_title'), 
										'cdata'=>array($component["name"])),
					 'partID'	=>array('cdata'=>array($partID)),
					 'quantity'	=>array('cdata'=>array($quantity)),
					 'matproc'	=>array('cdata'=>array('')),
					 'amt'		=>array('attribute'=>array('type'=>'ro'), 'cdata'=>array(' ')),
					 'unit'		=>array('attribute'=>array('type'=>'ro'), 'cdata'=>array(' ')), 
					 'ms'		=>$this->ms_cell($component["ELM"]),
					 'okala'	=>array('cdata'=>array(nice_exponential_notation(scientific_notation($okala, 3, SN_LOWER, SN_UPPER)))),
					 'co2'		=>array('cdata'=>array(nice_exponential_notation(scientific_notation($co2, 3, SN_LOWER, SN_UPPER)))),
					 'desc'		=>array('cdata'=>array($this->comment_wrapper($component["description"],$temp_id))),
					 'actions'	=>array('attribute'=>array('title'=>'  '), 'cdata'=>array($this->action_wrapper($actionValue)))
				);
			}
			
			// check for sub stuff
						
			if ($toplevel) 
				$component_cells['name']['attribute']['image'] = 'blanc.gif';
			$this->add_cells($c, $component_cells); 
			
			if ($toplevel && $kids) {
				$this->fill_xml($cid, $c);
			}

			//empty rows
			if (!$kids && $this->phaseID == PHASEID_USE) {
				$w = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>'emptytg_'.$component["name"], 'class'=>'empty_tg') ));
				// nonvisible but available data
				$ww = $this->make_xml_node($w, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( EMPTY_TYPE_CHECK) ));
				$ww =  $this->make_xml_node($w, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array('empty_tg') ));
				
				// Cells that will be displayed.
				$verb = ($component["name"][strlen($component["name"])-1] == 's') ? 'have' : 'has';
				$message = 'No '. strtolower($component["name"]). " $verb been added to this SBOM.";
				$empty_cells = array( 
					'name'=>array('attribute'=>array('colspan'=>8,'image'=>'blank.gif'), 
					'cdata'=> array($message))
				);
                                // watchdog('phases_tg_ajax_get', 'B: use message '.$message); // singhj
				$this->add_cells($w, $empty_cells); 
			}
		}
	}
	
	// helper functions for items 

	function item_has_children($item) {
		return true;
	}
	
	function isTransportationItem($item) {
		//has trans and only trans matProcs
		if (count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('transportation')))
			&& count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('material','process'))) == 0)
			return true;
		return false;
	}

	function hasTransportationMatProc($item) {
		if (count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('transportation'))))
			return true;
		return false;
	}
	
	// checks if the item is a item should be displayed 
	//show only items with material or process (don't show transport items)
	function valid_item($item) {
		if (count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('material','process'))) > 0)
			return true;
		return false;
	}
	
	function get_item_okala($item) {
		return $item['okalaNotEOL'];
	}
	function get_item_co2($item) {
		return $item['co2NotEOL'];
	}
	function get_item_partID($item) {
		return $item['partID'];
	}
	function get_item_quantity($item) {
		return format_numbers($item['quantity']);
	}

	function item_text($item) {
		return array('addProcessText'=>DEFAULT_SBOM_ICON_ADDPROC
			,'addProcessText_disable'=>DEFAULT_SBOM_ICON_ADDPROC_D
			,'addProcessTitle'=> 'Add a Process'
			, 'deleteTitle'=>'Delete'
			, 'editText'=>DEFAULT_SBOM_ICON_EDIT
			, 'deleteText'=>DEFAULT_SBOM_ICON_DELETE_URL
			, 'copyText'=>DEFAULT_SBOM_ICON_COPY_URL
			, 'copyTitle'=>'Copy'
			);
	}
	// BAG need to add the new copy action - KJH - added new copy param
	function item_actions($item, $addProcess='', $del='',$edit='',$copy='') {
		return $addProcess.' '. $copy.' '. $edit . ' '.$del ;
	}
	
	function empty_button($text) {
		return '<span class="empty-button">'.$text.'</span>';
	}
	
	function item_class(&$item, $parent=0) {
		return 'item';
	}
	
	function process_items(&$r, $parent=0) {
		$this->fill_items();
		if ($parent) {
			if ($this->sub_items[$parent]) 
				$items =$this->sub_items[$parent];
			else 
				$items = array();
		} else {
			$items = $this->items;		
		}
		
		foreach ($items as $item) {
			if (!$this->valid_item($item)) continue; 
			$temp_id = ITEM_CHAR . $item['itemID'];
			$texts = $this->item_text($item);
			$kids = $this->item_has_children($item);
			if ($this->edit) {
				$addProcess = '';
				if ($this->phaseID == PHASEID_TRANSPORT) //should make overriding function for trans instead?
					$addProcess = '<a href="/'.URL_ADD_PROCESS.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="'.$texts['addTransTitle'].'">'.$texts['addTransText'].'</a>';	 
				// if the material has viable process, make the link
				elseif (count($this->db->list_process_by_material($item['matProcID'],$this->eol)))
					$addProcess = '<a href="/'.URL_ADD_PROCESS.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="'.$texts['addProcessTitle'].'">'.$texts['addProcessText'].'</a>';	 
				else 
					$addProcess = $this->empty_button($texts['addProcessText_disable']);

				if ($this->phaseID == PHASEID_TRANSPORT && !$this->isTransportationItem($item)) {
					//on trans page, should not be able to edit/delete non-trans parts
					$edit = '';
					$del = '';
					$copy = '';
				} else {
					$edit = '<a href="/'.URL_EDIT_MANUFACTURING_MATERIAL.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="Edit">'.$texts['editText'].'</a>';
					/*$del = '<a href="JavaScript:void(0);" onclick="deleteRow(\''.$temp_id .'\',\''.$this->gridDivName.'\')" title="'.$texts['deleteTitle'].'">'.$texts['deleteText'].'</a>';*/
					$del = '<input  type="image" onclick="deleteRow(\''. $temp_id .'\',\''
							.$this->gridDivName.'\')"  title="'.$texts['deleteTitle'].'" name="image" src="'.$texts['deleteText'].'"/>';
					// copy item functionality, using a javascript call
					$isTop=1; // temporary?
					$copy = '<input type="image" onclick="javascript:copy_sbom_item(\''. $item['itemID'] .'\',\'' .$item['componentID'] .'\',\'' .$isTop.'\',\''.$this->conceptID.'\')"'
							.' name="image" title="'.$texts['copyTitle'].'" src="'.$texts['copyText'].'"/>';
					
				}
			}
			// BAG more action for item copy - this is where the div is defined for parts (items) - KJH - added $copy param
			$actionValue = $this->item_actions($item, $addProcess, $del, $edit, $copy);

			$attributeArray =  array('id'=>$temp_id, 'class'=>$this->item_class($item,$parent));
			if ($kids) {
				$attributeArray['xmlkids'] = '1';
				if ($willHaveSub)
					$attributeArray['open'] = 1;
			}

			// container row 
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>$attributeArray ));

			// container row. Items always have children (vs items_single that do not)
			//$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>$temp_id, 'xmlkids'=>'1', 'class'=>'item') ));
			
			// nonvisible but accessible data	
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( ITEM_TYPE_CHECK) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array( $item['itemID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'parent'), 'textNode'=>array( $parentComponent['name']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'add_proc_button'), 'cdata'=>array($this->action_wrapper($addProcess)) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'hasProcess'), 'textNode'=>array('1') ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'amt'), 'textNode'=>array($item['factor']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'matProcName'), 'textNode'=>array($item['matProcName']) ));
			
			$okala = $this->get_item_okala($item);
			$co2 = $this->get_item_co2($item);
			//watchdog('phases_tg_ajax_get', '6: item '.$item['itemID'].' CO2: '.$co2);
			$partID = $this->get_item_partID($item);
			$quantity = $this->get_item_quantity($item);
	
			// Cells that will be displayed. 
			$item_cells = array(
				 'name'		=>array('attribute'=>array('image'=>'books.gif', 'class'=>'cell_title'), 'cdata'=>array($item['name'])),
				 'partID'	=>array('cdata'=>array($partID)),
				 'matproc'	=>array('cdata'=>array($item['matProcName'])),
				 'ms'		=>$this->ms_cell($item['measurement']),
				 'quantity'	=>array('cdata'=>array($quantity)),
				 'unit'		=>array('attribute'=>array('title'=>$item['unit_description']), 'cdata'=>array($item['unit_symbol'])), 
				 'amt'		=>array('cdata'=>array(nice_exponential_notation($item['factor']))),
				 'okala'	=>array('cdata'=>array(nice_exponential_notation(scientific_notation($okala, 3, SN_LOWER, SN_UPPER)))),
				 'co2'		=>array('cdata'=>array(nice_exponential_notation(scientific_notation($co2, 3, SN_LOWER, SN_UPPER)))),
				 'desc'		=>array('cdata'=>array($this->comment_wrapper($item["description"], $temp_id))),
				 'actions'	=>array('attribute'=>array('title'=>'  '), 'cdata'=>array($this->action_wrapper($actionValue))) 
			);

			$this->add_cells($c, $item_cells);
		}
	}
	
	// item single functions
	
	function item_single_has_children($item) {
		return false;
	}
// BAG more copy stuff maybe for item single actions
	function item_single_actions($item, $addProcess='', $del='',$edit='',$copy='') {
		return $this->item_actions($item, $addProcess, $del,$edit,$copy);
	}
	function get_item_single_okala($item) {
		return $item['okalaNotEOL'];
	}
	function get_item_single_co2($item) {
		return $item['co2NotEOL'];
	}
	function get_item_single_partID($item) {
		return $item['partID'];
	}
	function get_item_single_quantity($item) {
		return format_numbers($item['quantity']);
	}
	
	// These items have a material but no process, so formated diffrently
	// shares many method with items
	function process_items_single(&$r, $parent=0) {
		$this->fill_items_single();
		if ($parent) {
			if ($this->sub_items_single[$parent]) $items_single = $this->sub_items_single[$parent];
			else $items_single  = array();
		} else {
			$items_single = $this->items_single;
		}
		
		if (!$items_single) return;
		foreach ($items_single as $item) {
			if (!$this->valid_item($item)) continue;
			$temp_id = ITEM_CHAR . $item['itemID'];
			$texts = $this->item_text($item);
			$kids = $this->item_single_has_children($item);

			if ($this->edit) {
				$addProcess = '';
				
				//should make overriding 'process_items_single' function for trans instead?
				if ($this->phaseID == PHASEID_TRANSPORT && !$this->isTransportationItem($item)) 
					$addProcess = '<a href="/'.URL_ADD_PROCESS.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="'.$texts['addTransTitle'].'">'.$texts['addTransText'].'</a>';	 
				elseif (count($this->db->list_process_by_material($item['matProcID'],$this->eol)))
				 	$addProcess = '<a href="/'.URL_ADD_PROCESS.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="'.$texts['addProcessTitle'].'">'.$texts['addProcessText'].'</a>';	
				else 
				 	$addProcess = $this->empty_button($texts['addProcessText_disable']);	 
			
				if ($this->phaseID == PHASEID_TRANSPORT && !$this->isTransportationItem($item)) {
					//on trans page, should not be able to edit/delete non-trans parts
					$edit = '';
					$del = '';
				} else {
					// if the material has viable process, make the link
					$edit = '<a href="/'.URL_EDIT_MANUFACTURING_MATERIAL.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $item['componentID'].'/'.$item['itemID'].'" title="Edit">'.$texts['editText'].'</a>';
					/*$del = '<a href="JavaScript:void(0);" onclick="deleteRow(\''.$temp_id .'\',\''.$this->gridDivName.'\')" title="'.$texts['deleteTitle'].'">'.$texts['deleteText'].'</a>';*/
					$del = '<input  type="image" onclick="deleteRow(\''. $temp_id .'\',\''
							.$this->gridDivName.'\')"  title="'.$texts['deleteTitle'].'" name="image" src="'.$texts['deleteText'].'"/>';
					// KJH - invoke the code to invoke the copy_item function, it should not put the button on a transportation item
					if (!$this->isTransportationItem($item)) {
						$isTop=1; // temporary? Not sure if it should change for an item - have to figure out what it does
						$copy = '<input type="image" onclick="javascript:copy_sbom_item(\''. $item['itemID'] .'\',\'' .$item['componentID'] .'\',\'' .$isTop.'\',\''.$this->conceptID.'\')"'
							.' name="image" title="'.$texts['copyTitle'].'" src="'.$texts['copyText'].'"/>';
					}

				}
			}
			// BAG more action stuff - the div is defined above for single action items 
			$actionValue = $this->item_single_actions($item, $addProcess, $del, $edit, $copy);
			
			$attributeArray = array('id'=>$temp_id, 'class'=>$this->item_class($item, $parent));
			if ($kids) {
				$attributeArray['xmlkids'] = '1';
				if ($willHaveSub)
					$attributeArray['open'] = 1;
			}

			// container row 
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>$attributeArray ));
			
			// container row. They do not have children
			//$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>$temp_id, 'class'=>'item') ));
			
			// nonvisable but accessible data	
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( ITEM_TYPE_CHECK) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array( $item['itemID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'parent'), 'textNode'=>array( $parentComponent['name']) ));
			// BAG copy stuff maybe
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'add_proc_button'), 'cdata'=>array($this->action_wrapper($addProcess)) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'hasProcess'), 'textNode'=>array('0') ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'matProcName'), 'textNode'=>array($item['matProcName']) ));
			
			$okala = $this->get_item_single_okala($item) ; 
			$co2 = $this->get_item_single_co2($item);
			//watchdog('phases_tg_ajax_get', '7: item single '.$item['itemID'].' CO2: '.$co2);
			$partID = $this->get_item_single_partID($item) ; 
			$quantity = $this->get_item_single_quantity($item);

			// Cells tha will be displayed. 
			$item_single_cells = array(
				 'name'		=>array('attribute'=>array('image'=>'book.gif', 'class'=>'cell_title'), 'cdata'=>array($item['name'])),
				 'partID'	=>array('cdata'=>array($partID)),
				 'quantity'	=>array('cdata'=>array($quantity)),
				 'matproc'	=>array('cdata'=>array($item['matProcName'])),
				 'amt'		=>array('cdata'=>array(nice_exponential_notation($item['factor']))),
				 'unit'		=>array('attribute'=>array('title'=>$item['unit_description']), 'cdata'=>array($item['unit_symbol'])), 
				 'ms'		=>$this->ms_cell($item['measurement']),
				 'okala'	=>array('cdata'=>array(nice_exponential_notation(scientific_notation($okala, 3, SN_LOWER, SN_UPPER)))),
				 'co2'		=>array('cdata'=>array(nice_exponential_notation(scientific_notation($co2, 3, SN_LOWER, SN_UPPER)))),
				 'desc'		=>array('cdata'=>array($this->comment_wrapper($item["description"], $temp_id))),
				 'actions'	=>array('attribute'=>array('title'=>'  '), 'cdata'=>array($this->action_wrapper($actionValue))) 
			);	
			$this->add_cells($c, $item_single_cells);
		}
	}
	
	// matproc specific functions
	
	function matproc_text($matproc) {
		return array('deleteTitle'=>'Delete'
					,'deleteText'=>DEFAULT_SBOM_ICON_DELETE_URL
					,'editTitle'=>'Edit'
					,'editText'=>DEFAULT_SBOM_ICON_EDIT
		);
	}
	
	// no deletion of material, only of processes
	function matproc_actions($matproc, $edit='',$del ='') {
		if ($matproc['type'] != 'material') return $edit . ' ' .$del;
		return '';
	}
	function get_matproc_okala($matproc) {
		return $matproc['okala'];
	}
	function get_matproc_co2($matproc) {
		return $matproc['co2'];
	}
	
	// fills the matproc cells depending on if it's in edit/what phase
	function process_matprocs(&$r,$parent=0) {
		if ($parent)
			$matprocs = $this->sub_matprocs;
		else {
			$this->fill_matprocs();
			$matprocs = $this->matprocs;
		}
		
		foreach ($matprocs as $matproc) {
			$temp_id = PROCESS_CHAR . $matproc['itemMatProcID'];
			$texts = $this->matproc_text($matproc);
			if ($this->edit) {
				/*$del = '<a href="JavaScript:void(0);" onclick="deleteRow(\''. $temp_id .'\',\''.$this->gridDivName.'\')" title="'.$texts['deleteTitle'].'">'.$texts['deleteText'].'</a>';*/
				$del = '<input  type="image" onclick="deleteRow(\''. $temp_id .'\',\''
							.$this->gridDivName.'\')"  title="'.$texts['deleteTitle'].'" name="image" src="'.$texts['deleteText'].'"/>';

				$edit = '<a href="/'.URL_EDIT_PROCESS.'/'.$this->conceptID .'/' . $this->phaseID .'/'. $this->parent['componentID'].'/'.$matproc['itemID'] .'/'.$matproc['itemMatProcID'].'" title="'.$texts['editTitle'].'">'.$texts['editText'].'</a>';
			}
			// BAG copy stuff matprocs need copy as well
			$actionValue = $this->matproc_actions($matproc, $edit, $del);
			
			$okala = $this->get_matproc_okala($matproc);
			$co2 = $this->get_matproc_co2($matproc);
			//watchdog('phases_tg_ajax_get', '8: matproc '.$matproc['matProcID'].' CO2: '.$co2);
			
			// container row 
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>$temp_id, 'class'=>'process') ));
			
			// nonvisible but available data
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( PROCESS_TYPE_CHECK) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array( $matproc['itemMatProcID']) ));
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'parent'), 'textNode'=>array( $this->parent['name']) ));
			
			// Cells that will be displayed. 
			$matproc_cells = array(
				'name'		=>array('attribute'=>array('image'=>'leaf.gif', 'class'=>'cell_title'), 
				'cdata'		=>array(ucfirst($matproc['type']))),
				'matproc'	=>array('cdata'=>array($matproc['name'])),
				'amt'		=>array('cdata'=>array(nice_exponential_notation($matproc['factor']))),
				'unit'		=>array('attribute'=>array('title'=>$matproc['unit_description']),'cdata'=>array($matproc['unit_symbol'])),
				'ms'		=>$this->ms_cell($matproc['measurement']),
				'okala'		=>array('cdata'=>array(nice_exponential_notation(scientific_notation($okala, 3, SN_LOWER, SN_UPPER)))),
				'co2'		=>array('cdata'=>array(nice_exponential_notation(scientific_notation($co2, 3, SN_LOWER, SN_UPPER)))),
				'desc'		=>array('cdata'=>array($this->comment_wrapper($matproc["proc_desc"], $temp_id))), //$matproc["description"]
				'actions'	=>array('attribute'=>array('title'=>'  '), 'cdata'=>array($this->action_wrapper($actionValue))) 
			);
			$this->add_cells($c, $matproc_cells);
		}
	}
	
	
	/*
		Returns information in XML format 
	*/
	function fill_xml($parent = null, &$parent_row=null) {
		// row container, has parent if not root 
		$parArray = array();
		if ($parent) {
			$parArrey = array('parent'=> COMPONENT_CHAR .$parent);
		} elseif ($this->returnParentID) 
			$parArrey = array('parent'=>$_GET['id']);
			
		if (!$parent_row) 
			$r = $this->make_xml_node($this->doc, array('node'=>'rows', 'attribute'=>$parArrey ));
		else 
			$r = &$parent_row;
			
		if ($this->type == ITEM_TYPE_CHECK)
			$this->process_matprocs($r, $parent);
		else {
			$this->process_components($r, $parent);
			if ($this->phaseID == PHASEID_TRANSPORT) {
				$this->process_items_single($r, $parent);
				$this->process_items($r, $parent);
			} else {
				$this->process_items($r, $parent);
				$this->process_items_single($r, $parent);
			}
		}
		
		$this->is_filled = true;
		$phaseName = sustainable_minds_get_phase_name_from_id($this->phaseID);
		if ($this->enable_empty && $this->check_empty_state($parent)) {
			// send back an empty row.
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>'emptytg_'.$parent, 'class'=>'empty_tg') ));
			// nonvisable but avilable data
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( EMPTY_TYPE_CHECK) ));
			$cc =  $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array('empty_tg') ));
			// Cells that will be displayed.
			
			switch ($this->phaseID) {
				case PHASEID_MANUFACTURE:
					$colspan = 10;
					$message = 'No items have been added to this SBOM. Manually add materials and processes for parts or sub-assemblies, or import a BOM.';
					break;
				case PHASEID_TRANSPORT:
					$colspan = 10;
					if ($parent == -1)
						$message = 'Transportation for parts and sub-assemblies has not been added to this SBOM.';
					else
						$message = 'Transportation for the assembled product has not been added to this SBOM.';
					break;
				case PHASEID_EOL:
					$colspan = 10;
					$message = 'End of life applies to materials. No materials have been added to this SBOM.';
					break;
				//case use is handled elsewhere
			}
			
			$empty_cells = array( 
				'name'=>array('attribute'=>array('colspan'=>$colspan,'image'=>'blank.gif'), 
				'cdata'=> array($message)) //array('There are no items in the '.$phaseName.' phase of this concept.'))
				);
			$this->add_cells($c, $empty_cells); 
		}
		
		if (!$this->returnParentID && !$parent) { // THIS is the top level
			$total = $this->total();
			if (empty($total['okala'])) $total['okala'] = '--';
			if (empty($total['co2'])) $total['co2'] = '--';
			if (empty($this->ms_total)) $this->ms_total = '--';
			
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>'total', 'class'=>'total') ));
			// nonvisable but avilable data
			$cc = $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'type'), 'textNode'=>array( TOTAL_TYPE_CHECK) ));
			$cc =  $this->make_xml_node($c, array('node'=>'userdata','attribute'=>array('name'=>'rID'), 'textNode'=>array('total') ));
			
			// Cells that will be displayed.
			if ($this->phaseID == PHASEID_USE) 
				$total_label_span = '3';
			else
				$total_label_span = '4';
				
			$total_cells = array(
				'name'		=>array('attribute'=>array('image'=>'blank.gif')),
				'matproc'	=>array('attribute'=>array('class'=>'emp', 'colspan'=>$total_label_span), 'cdata'=>array($phaseName . ' total')),
				'ms'		=>$this->ms_cell($this->ms_total, array('class'=>'emp')),	
				'okala'		=>array('attribute'=>array('class'=>'emp'),
									'cdata'=>array(nice_exponential_notation(scientific_notation($total['okala'], 3, SN_LOWER, SN_UPPER)))),
				'co2'		=>array('attribute'=>array('class'=>'emp'),
									'cdata'=>array(nice_exponential_notation(scientific_notation($total['co2'], 3, SN_LOWER, SN_UPPER))))
			);
			$this->add_cells($c, $total_cells);
		}
		
		if ($this->db->get_pD()) {
			$c = $this->make_xml_node($r, array('node'=>'row','attribute'=>array('id'=>'permissionDenied', 'class'=>'total') ));
		}
	}
	

}



class phases_tg_ajax_get_manufacturing extends phases_tg_ajax_get{
	function __construct() {
	 	parent::__construct(PHASEID_MANUFACTURE);
	}
}



class phases_tg_ajax_get_use extends phases_tg_ajax_get {
	function __construct() {
	 	parent::__construct(PHASEID_USE);
	}
	
	// returns the text for buttons and other text places
	function  component_text($component) {
		while($component['componentTypeID'] == 0 && $component) {		
			$component = $this->db->get_component($component['parentID']);
		}
		
		if ($component['componentTypeID'] == COMPONENT_ROOT_CON) {
			$materialText = DEFAULT_SBOM_ICON_ADDCONSUME;
			$materialTitle = 'Add Consumables';
		}
		elseif ($component['componentTypeID'] == COMPONENT_ROOT_POW) {
			$materialText = DEFAULT_SBOM_ICON_ADDPOWER;
			$materialTitle = 'Add Power Use';
		}
		elseif ($component['componentTypeID'] == COMPONENT_ROOT_WAT) {
			$materialText = DEFAULT_SBOM_ICON_ADDWATER;
			$materialTitle = 'Add Water Use';
		}
		
		return array('addMaterialText'=>$materialText
			,'addComponentText'=>DEFAULT_SBOM_ICON_ADDSA
			,'editComponentText'=>DEFAULT_SBOM_ICON_EDIT
			,'addMaterialTitle'=> $materialTitle
			,'addComponentTitle'=>'Add a ' .DEFUALT_COMPONENT_TEXT
			,'editComponentTitle'=>'Edit'
			,'deleteTitle'=>'Delete');
	}
	// BAG copy stuff add the copy item but all are '', this is use phase
	function component_actions($component, $addComponent='',$addMaterial='',$del='', $edit='') {
		return  $addMaterial . ' ' . $edit .' '.$del ;
	}
	// BAG copy stuff all are '' this is use phase
	// item specific functions
	function item_actions($item, $addProcess='', $del='',$edit='') {
		return $edit . ' ' . $del;
	}
	
	function top_level($component) {
		$cid = $component['componentID'];
		if ($cid == -1 || $cid == $this->parentTranID) return true; 
		return false; 
	}	
}



class phases_tg_ajax_get_eol extends phases_tg_ajax_get {
	function __construct() {
	 	parent::__construct(PHASEID_EOL);
	 	$this->phase_to_use = PHASEID_MANUFACTURE;
	 	$this->eol = 'eol';
	}
	// BAG copy stuff - add copy but none for eol phase
	function component_actions($component, $addComponent='',$addMaterial='',$del='', $edit='') {
		return '';
	}
	
	// item specific functions
	
	function get_item_okala($item) {
		return $item['okalaEOL'];
	}
	function get_item_co2($item) {
		return $item['co2EOL'];
	}
	function item_text($item) {
		return array('addProcessText'=>DEFAULT_SBOM_ICON_ADDMETHOD
			,'addProcessText_disable'=>DEFAULT_SBOM_ICON_ADDMETHOD_D
			,'addProcessTitle'=> 'Add End of Life method'
			,'deleteTitle'=>'Delete'
			);
	}
	// BAG copy stuff - add copy arg but no copy for eol phase
	function item_actions($item, $addProcess='', $del='',$edit='') {
		return '';
	}
	// items single specific actions
	
	// BAG no copy for eol phase add copy arg
	// single items do not have a process already, so in they have one
	function item_single_actions($item, $addProcess='', $del='', $edit ='') {
		return $addProcess;
	}
	
	// item singles should not have okala/co2, enforces that
	function get_item_single_okala($item) {
		return '';
	}
	function get_item_single_co2($item) {
		return '';
	}
	
	//matproc specific actions
	
	function  get_matproc_okala($matproc) {
		if ($matproc['type'] == 'material') return '';
		return parent::get_matproc_okala($matproc);
	}
	function  get_matproc_co2($matproc) {
		if ($matproc['type'] == 'material') return '';
		return parent::get_matproc_co2($matproc);
	}
	// BAG copy stuff - add copy arg but no copy for matproc in eol phase
	function matproc_actions($matproc, $edit='',$del ='') {
		if ($matproc['type'] != 'material') return $edit . ' ' .$del;
		return '';
	}
}



class phases_tg_ajax_get_transportation extends phases_tg_ajax_get {
	var $parentTranID = 0;
	
	function __construct() {
	 	parent::__construct(PHASEID_TRANSPORT);
	}
	
	function top_level($component) {
		$cid = $component['componentID'];
		if ($cid == -1 || $cid == $this->parentTranID) 
			return true; 
		return false; 
	}
	
	function will_have_sub($component) {
		$cid = $component['componentID'];
		if ($this->enable_empty && $this->top_level($component) ) return true; 
		return $this->component_has_children($component); 	
	}
	
	// override parent fill_components
	function fill_components() { 
		$components= $this->db->list_components_by_parent($this->parentID);	
		
		$this->sub_components = array(); 
		
		/*Fix the component to include a fake row. */
		if ($this->parent['componentTypeID']==COMPONENT_ROOT_MAN) { //parent component type manf.
			if ($this->sub_components) 
				return; // already filled
			
			foreach ($components as $key=>$comp) {
				if ($comp['componentTypeID'] == COMPONENT_ROOT_TRA) { //the top-level trans component
					// should only be one
					$comp['name'] =  'Assembled product';
					$topcomponents[$key] = $comp;
					$this->parentTranID = $comp['componentID'];
				}
				elseif ($comp['phaseID'] != PHASEID_TRANSPORT)  {
					$okala += $this->get_component_okala($comp);
					$co2 += $this->get_component_co2($comp);
			//watchdog('phases_tg_ajax_get', '2: comp '.$comp['componentID'].' CO2: '.$co2);
					$this->sub_components['-1'][$key] = $comp;
					$this->ms_total_update($comp['measurement']);	
				}
			}
			
			$topcomponents[] = array('name'=>'Sub-assemblies and parts','componentID' =>'-1','okala_phase3'=>$okala,'co2_phase3'=>$co2);
			// make sure items and items single is filled
			$this->components  = $topcomponents;
			$this->fill_items();
			$this->fill_items_single();
		} else {	//trans component for subassemblies
			foreach($components as $key=>$comp) {
				// don't show transportation components
				if ($comp['phaseID'] != PHASEID_TRANSPORT)
					$topcomponents[$key] = $comp;
			}
			$this->components = $topcomponents;
		}
	}
	
	//look for index in $this->components of 'Sub-assemblies and parts' component (only useful after fill_components())
	function get_sa_and_parts_index() {
		foreach ($this->components as $i=>$c) {
			if ($c['componentID']==-1)
				return $i;
		}
	}
	
	// fill items array to sub items instead 
	function fill_items() {
		if ($this->parent['componentTypeID']==COMPONENT_ROOT_MAN) {
			if ($this->sub_items)
				return; // already filled
			
			$this->sub_items['-1'] = $this->db->list_items_with_process($this->parentID, $this->eol, ONLY_TRANS_SCORES);
			$this->sub_items[$this->parentTranID] = $this->db->list_items_with_process($this->parentTranID, $this->eol, ONLY_TRANS_SCORES);
			$index = $this->get_sa_and_parts_index();
			if ($this->sub_items['-1'] ) {
				foreach($this->sub_items['-1'] as $value) {
					$this->components[$index]['okala_phase3'] += $this->get_item_okala($value);
					$this->components[$index]['co2_phase3'] += $this->get_item_co2($value);
			//watchdog('phases_tg_ajax_get', '3: item '.$value['itemID'].' CO2: '.$co2);
					$this->ms_total_update($items['measurement']);
				}
			}
			
			if (!is_array($this->items))
				$this->items = array();
		} else {
			$this->items = $this->db->list_items_with_process($this->parentID, $this->eol, ONLY_TRANS_SCORES);
			if (!is_array($this->items))
				$this->items = array();
			// get
		}
	}
	
	
	
	// fill items_single to sub_items single instead
	function fill_items_single() {
		if ($this->parent['componentTypeID']==COMPONENT_ROOT_MAN ) {
			if ($this->sub_items_single)
				return; // already filled
			
			$this->sub_items_single['-1'] = $this->db->list_items_no_process($this->parentID, $this->eol, ONLY_TRANS_SCORES);
			$this->sub_items_single[$this->parentTranID] = $this->db->list_items_no_process($this->parentTranID, $this->eol, ONLY_TRANS_SCORES);
			$index = $this->get_sa_and_parts_index();
			if ($this->sub_items_single['-1'] ) {
				foreach ($this->sub_items_single['-1'] as $value) {
					$this->components[$index]['okala_phase3'] += $this->get_item_single_okala($value);
					$this->components[$index]['co2_phase3'] += $this->get_item_single_co2($value);
			//watchdog('phases_tg_ajax_get', '4: item single '.$value['itemID'].' CO2: '.$co2);
					$this->ms_total_update($items['measurement']);
				}
			}

			if (!is_array($this->items_single))
				$this->items_single = array();
		} else {
			$this->items_single = $this->db->list_items_no_process($this->parentID, $this->eol, ONLY_TRANS_SCORES); // show trans items 
			if (!is_array($this->items_single))
				$this->items_single = array();
		}
	}
	
	
	// returns whether component has any child
	function component_has_children($component) {
		if ($component['componentID']=='-1' || $component['componentID']==$this->parentTranID ) { // special component
			if (count($this->sub_components[$component['componentID']]) 
				|| count($this->sub_items[$component['componentID']]) 
				|| count($this->sub_items_single[$component['componentID']]) 
			 ) return true;
		}
		elseif ($component['phaseID'] == PHASEID_MANUFACTURE) {
			if (count($this->db->list_components_by_parent_and_phase($component['componentID'], PHASEID_MANUFACTURE)) > 0 
				|| count($this->db->list_items_no_process($component['componentID'],$this->eol, ONLY_TRANS_SCORES)) > 0 
				|| count($this->db->list_items_with_process($component['componentID'],$this->eol,ONLY_TRANS_SCORES)) > 0) return true;	
			// check transportation component for  children , only comes here if all above is blank  
			foreach ($this->db->list_components_by_parent_and_phase($component['componentID'], PHASEID_TRANSPORT) as $key=>$comp ) {
				if ( count($this->db->list_items_no_process($comp['componentID'])) > 0 ) return true; // trans never has processes
			}
		} elseif ($component['componentTypeID'] ==COMPONENT_ROOT_TRA) {
			// if component has children 
			if ( count($this->db->list_items_no_process($component['componentID'])) > 0 ) return true;
		} 
		else
			return parent::component_has_children($component);
		return false;
		
	}

	// returns the text for buttons and other text places
	function component_text($component) {
		return array(
			'addMaterialText'=> DEFAULT_SBOM_ICON_ADDTRANSPORTATION
			,'addMaterialTitle'=> 'Add transportation mode'
			,'addComponentText'=>DEFAULT_SBOM_ICON_ADDTRANSPORTATION
			,'addComponentTitle'=>'Add transportation mode'
			,'editComponentText'=> DEFAULT_SBOM_ICON_EDIT 
			,'editComponentTitle'=>'Edit'
			,'deleteTitle'=>'Delete'
			,'deleteText'=> DEFAULT_SBOM_ICON_DELETE_URL
			);
	}

	// returns the action values
	// BAG copy stuff all '' - no copy for transportation phase
	function component_actions($component, $addComponent='',$addMaterial='',$del='', $edit='') {
		//If the component is of type manufacturing, allow adding transportation sub component
		if ($component['componentID']>0) return $addMaterial;
		//if the component is of type transportation, allow editing or deleting the component
		return '';
	}

	// returns true if item has transportation matProcs, AND a material
	function item_has_children($item) {
 		if (count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('transportation')))
 			&& count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('material'))) > 0)
 			return true;
 		return false;
 	}

	function valid_item($item) {
		return true; 
	}
	
	function item_text($item) {
		return array('addTransText'=>DEFAULT_SBOM_ICON_ADDTRANSPORTATION
			, 'addTransTitle'=> 'Add transportation to this part'
			, 'deleteTitle'=>'Delete this transportation component'
			, 'editText'=>DEFAULT_SBOM_ICON_EDIT
			, 'deleteText'=>DEFAULT_SBOM_ICON_DELETE_URL
			);
	}

	function get_item_okala($item) {
		//show okala only if item has trans matproc
		if ($this->hasTransportationMatProc($item))
			return $item['okalaNotEOL'];
		else
			return '';
	}

	function get_item_co2($item) {
		//show co2 only if item has trans matproc
		if ($this->hasTransportationMatProc($item))
			return $item['co2NotEOL'];
		else
			return '';
	}

	function get_item_quantity($item) {
		//show quantity for material items only
		if (!$this->isTransportationItem($item))
			return format_numbers($item['quantity']);
		else return '';
	}

	// returns true if item has transportation matProcs, AND a material
	function item_single_has_children($item) {
		if (count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('transportation')))
			&& count($this->db->list_matproc_by_type($item['itemID'], $this->eol, array('material'))) > 0)
			return true;
		return false;
	}

	function get_item_single_okala($item) {
		//show okala only if item has trans matproc
		if ($this->hasTransportationMatProc($item))
			return $item['okala'];
		else
			return '';
	}

	function get_item_single_co2($item) {
		//show co2 only if item has trans matproc
		if ($this->hasTransportationMatProc($item))
			return $item['co2'];
		else
			return '';
	}

	function get_item_single_quantity($item) {
		//show quantity for material items only
		if (!$this->isTransportationItem($item))
			return format_numbers($item['quantity']);
		else return '';
	}

	function item_class(&$item,$parent=0) {
		return ($item['type']=='transportation' && ($parent == 0 || $parent != $this->parentTranID) ?'component':(parent::item_class($item)));
	}

	// fill matprocs (only called with ITEM_TYPE_CHECK == type)
	function fill_matprocs() {
		$this->matprocs = $this->db->list_matproc_by_type($this->parentID, $this->eol, array('material','transportation'));
		if (!is_array($this->matprocs)) $this->matprocs = array();
	}
	
	function  get_matproc_okala($matproc) {
		if ($matproc['type'] == 'transportation') 
			return parent::get_matproc_okala($matproc);
		return '';
	}

	function  get_matproc_co2($matproc) {
		if ($matproc['type'] == 'transportation') 
			return parent::get_matproc_co2($matproc);
		return '';
	}
	
	function check_empty_state($parent = null) {
		if ($this->returnParentID  || !$parent) 
			return false; /*not second top level*/ 
		if (!$this->is_filled) 
			$this->fill_all(); 
		
		if (empty($this->sub_matprocs[$parent]) && empty($this->sub_items_single[$parent]) && empty($this->sub_items[$parent]) && empty($this->sub_components[$parent])) {
			return true;
		}
		return false; 
	}
}
?>
