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

/*
	Manufactoring phase treegrid information
*/
class phases_tg_manufacturing extends phases_tg {
	function __construct($conceptid, $width='875', $height='200') {
	 	parent::__construct(PHASEID_MANUFACTURE,$conceptid,$width,$height);
	 	$this->component_type = COMPONENT_ROOT_MAN;
	}
	
	function construct_buttons() {
		$db = \Drupal::service('setup_project.sbom_db');
		//FIXME - removed bom import button
		/*
		$records = $db->bom_list_records_by_component($this->root);
		
		if (count($records) > 0) $output .= '<div class="tree-action-button"><a href="/'.URL_BOMLOAD_APPROVE.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root.'">'.DEFAULT_TREEACTION_IMPORTBOMPREV.'</a></div>';
		*/
        $output .= '<div class="concept-add-btns d-flex justify-content-center">';
		$output .= '<a class="project_btn btn btn-success btn-sm" href="'.SITE_PATH.'/'.URL_ADD_MANUFACTURING_MATERIAL.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root.'">Add a Part <img src='. SITE_PATH .'/sites/default/files/2021-07/plus.svg alt="plus-icon" title="plus"></a>';
		$output .= '<a class="project_btn btn btn-success btn-sm" href="'.SITE_PATH.'/'.URL_ADD_COMPONENT.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root.'">Add Sub-Assembly <img src='. SITE_PATH .'/sites/default/files/2021-07/plus.svg alt="plus-icon" title="plus"></a>';
		$output .= '<a class="project_btn btn btn-success btn-sm" href="'.SITE_PATH.'/'.URL_BOMLOAD_FILE.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root.'">Import BOM <img src='. SITE_PATH .'/sites/default/files/2021-07/plus.svg alt="plus-icon" title="plus"></a>';
		$output .= '</div>';
		return $output;
		//return 	make_gen_action_float('/'.URL_ADD_COMPONENT.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root, DEFAULT_TREEACTION_ADDSA)
		//		. make_gen_action_float('/'.URL_ADD_MANUFACTURING_MATERIAL.'/'.$this->conceptID.'/'.$this->phaseID.'/'.$this->root, DEFAULT_TREEACTION_ADDPART)	.'<div class="clear"></div>';
	}
	
	function fill_treegrid_get() {
		$this->treegrid_get = new phases_tg_ajax_get_manufacturing($this->conceptID);
		$this->treegrid_get->set_variables_from_array(array('phaseID'=>$this->phaseID ,'conceptID'=>$this->conceptID ,
			'phase_to_use'=>$this->phaseID ,'parentID'=>$this->root)) ;
	}
	
	function empty_text() {
		return TEXT_DEFAULT_SBOM_EMPTY_MAN;
	}
}