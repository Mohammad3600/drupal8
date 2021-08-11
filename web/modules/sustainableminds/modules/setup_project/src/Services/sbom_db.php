<?php

namespace Drupal\setup_project\Services;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Driver\pgsql\PDO;
class sbom_db{
    var $userid;
	var $pD = false;
	var $defaultpDAction = true;
	function __construct($userid=false){
		if(!$userid){
			$userid = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
		}
		$this->userid = $userid->get('uid')->value;
	}
	
	/*
		check for permission denied.
		Takes in result set
			or a row
			or a string from db_result
	*/ 
	
	function check_for_errors($row='') {
		if ($row === true || $row === false) return $row; 
		if ($row && !is_array($row) && !is_string($row)) {
			$result = $row; 
			if (count($result)>0) 
				$row = $result->fetchAssoc();
			else {
				$row=array();
			}
		}
		
		if ($row['permissiondenied'] == 'permissiondenied' || $row === 'permissiondenied') {
			if ((!$this->pD || $this->debug) && !$this->disable_message) drupal_set_message('Sorry, you have tried to access an area or item that you do not have permission for.','error');
			$this->pD = true;
			
			if (is_resource($result)) sustainable_minds_clear_db($result);
			
			if ($this->defaultpDAction) {
				Database::setActiveConnection();
				sustainable_minds_access_denied();
			}
			if (is_array($row)) return array();
			else return ''; 
		}
		if (is_resource($result)) return $result; 
		return $row;
	}
	
	function disable_defualt_pD_action(){
		$this->defaultpDAction = false; 
	}
	function disable_message(){
		$this->disable_message = true;
	}
	
	function get_pD(){
		return $this->pD ; 
	}
	var $disable_message = false; 
	var $debug = false; 
	/* initlization and check if error*/
	function init_and_check_if_error($func) {
		if ($this->debug) drupal_set_message($func . ' called @ '.date(DATE_ATOM) . '. Error state:' . ($this->pD?'1':'0'),'error');
		if (!$this->pD) Database::setActiveConnection('d5_dump');
		return $this->pD? true:false; 
	}
	
	////////////////////////////////Products\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function get_product($id){
		//Get product
		if ($this->init_and_check_if_error('get_product')) return ''; 
        $conn =  Database::getConnection();
		// $statement = $conn->prepare('CALL SM_SBOM_Get_Product(%d, %d);', array($id, $this->userid));
		// $result = $statement->execute();
		$statement = db_query('CALL SM_SBOM_Get_Product(?, ?);', array($id, $this->userid));
		// $object = $this->check_for_errors(db_fetch_array($result));
		$object = $this->check_for_errors($statement->fetchAssoc());
		sustainable_minds_clear_db($statement);
		Database::setActiveConnection();
		// db_set_active();

		//DO PERMISSION CHECKS
		//Temp sprint 3-4 check
		return $object;
		// if($this->userid == $object['userID']){
		// 	return $object; 
		// }else{
		// 	$this->access_denied();
		// }
	}
	
	function list_products_by_user(){
		if ($this->init_and_check_if_error('list_products_by_user')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Products_By_User('.$this->userid.');');
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			if($this->userid == $currentobject['userID']){
				$object[]=$currentobject;
			}
		}
		
		sustainable_minds_clear_db($result);
		db_set_active();
		
		//DO PERMISSION CHECKS
		
		return $object;
	}
	
	function add_product($values){
		if ($this->init_and_check_if_error('add_product')) return ''; 
		
		//Create product userid, title, description,client, categoryid, icon, assessment, development
		$lastid = $this->check_for_errors(db_result(db_query("CALL SM_SBOM_Add_Product(%d, '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s');", array($this->userid, $values['title'], $values['description'], $values['client'], $values['category'], $values['image'], $values['assessment'], $values['development'], $values['inclusion'], $values['exclusion'], $values['system']))));
		
		//switch back to drupal db after inserting data
		db_set_active();
		
		//drupal_set_message(t('Your have successfully completed project set up.'));
		
		return $lastid;
	}
	
	function add_blank_product(){
		if ($this->init_and_check_if_error('add_blank_product')) return ''; 
		$conn =  Database::getConnection();
		$query = db_query("CALL SM_SBOM_Add_Blank_Product(?);",array($this->userid));
		// $r = $query->bindParam(1);
		$fetchlast = $query ->fetchField();
		$lastid = $this->check_for_errors($fetchlast);
		//switch back to drupal db after inserting data
		Database::setActiveConnection();
		return $lastid;
	}
	
	function complete_product($productID){
		if ($this->init_and_check_if_error('complete_product')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Complete_Product(%d, %d);", array($productID, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		return '';
	}
	
	function update_product($id, $values){
		//die($values['funame']);
		if ($this->init_and_check_if_error('update_product')) return ''; 
		//Update product id, userid, title, description,client, categoryid, icon, assessment, development
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Product(%d, '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d);", array($id, $values['name'], $values['description'], $values['client'], $values['pcategoryID'], $values['icon'], $values['assessment'], $values['development'], $values['inclusion'], $values['exclusion'], $values['system'], $values['funame'], $values['fudesc'], $values['version'], $this->userid)));
		//switch back to drupal db after updateing data
		db_set_active();
		//set message, unset session and redirect
		//drupal_set_message(t('Your product has been updated!'));
		
		return true;
	}
	
	function copy_project($pid) {
	if ($this->init_and_check_if_error('copy_product')) return '';
		$result = db_query("CALL SM_SBOM_Copy_Product(%d, %d);", array($pid, $this->userid));
		$projectID = $this->check_for_errors(db_result($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $projectID;
	}
	
	function copy_from_to_project($pid, $from, $to) {
	if ($this->init_and_check_if_error('copy_from_to_project')) return '';
		$result = db_query("CALL SM_SBOM_Copy_From_To_Product(%d, %d, %d);", array($pid, $from, $to));
		$projectID = $this->check_for_errors(db_result($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $projectID;
	}
	
	function update_project_dataset($pid, $dest_version) {
		if ($this->init_and_check_if_error('update_project_dataset')) return '';
		$result = db_query("CALL SM_Update_Project_Dataset(%d, '%s', %d);", array($pid, $dest_version, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}

		sustainable_minds_clear_db($result);
		db_set_active();
		return $object;
	}
	
	function get_product_id_by_name($pname, $puserid) {
		if ($this->init_and_check_if_error('get_product_id_by_name')) return '';
		$result = db_query("CALL SM_SBOM_Get_ProductID_By_Name('%s', %d);", array($pname, $puserid));
		$projectID = $this->check_for_errors(db_result($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $projectID;
	}		

	function delete_product($id){
		// see delete_project
	}
	
	function set_product_reference($pid, $cid){
		if ($this->init_and_check_if_error('set_product_reference')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Set_Product_Reference(%d, %d, %d);", array($pid, $cid, $this->userid)));
		db_set_active();
	}
	
	function unset_product_final($pid){
		if ($this->init_and_check_if_error('unset_product_final')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Unset_Product_Final(%d, %d);", array($pid, $this->userid)));
		db_set_active();
	}

	function set_product_final($pid, $cid){
		if ($this->init_and_check_if_error('set_product_final')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Set_Product_Final(%d, %d, %d );", array($pid, $cid, $this->userid)));
		db_set_active();
	}
	
	function products_using_image($filename, $productID=0) {
		if ($this->init_and_check_if_error('products_using_image')) return ''; 
		$count = $this->check_for_errors(db_result(db_query("CALL SM_SBOM_Products_Using_Image(%d, '%s');", array($productID, $filename))));
		sustainable_minds_clear_db();
		db_set_active();
		
		return $count;
	}
	
	function delete_product_icon($productID){
		if ($this->init_and_check_if_error('delete_product_icon')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Product_Icon(%d, %d);", array($productID, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return '';
	}

	function get_func_unit_for_product($productID){
		if ($this->init_and_check_if_error('get_func_unit_for_product')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Func_Unit_For_Product(%d, %d);', array($productID, $this->userid));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}	
	////////////////////////////////Concepts\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function get_concept($id){
		if ($this->init_and_check_if_error('get_concept')) return '';
		$result = db_query('CALL SM_SBOM_Get_Concept(%d, %d);', array($id, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $object;
		//DO PERMISSION CHECKS
		if($this->userid == $object['userID']){
			return $object;
		}else{
			$this->access_denied();
		}
	}

	function list_concepts($productid){
		if ($this->init_and_check_if_error('list_concepts')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Concepts(%d, %d);', array($productid, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			//if($this->userid == $currentobject['userID']){
				//DO PERMISSION CHECKS
				$object[]=$currentobject;
			//}
			
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function add_concept($values){
		if ($this->init_and_check_if_error('add_concept')) return ''; 
		//Create product userid, title, description,client, categoryid, icon, assessment, development
		$lastid = $this->check_for_errors(db_result(db_query("CALL SM_SBOM_Add_Concept(%d, '%s', '%s', '%s', %f, '%s', %d);", array($values['productID'], $values['title'], $values['description'], $values['icon'], $values['lifetimefuncunits'], $values['funcunitnote'], $this->userid))));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return $lastid;
	}
	
	function update_concept($conceptid, $values){
		if ($this->init_and_check_if_error('update_concept')) return ''; 
		//Update concept: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Concept(%d, '%s', '%s', '%s', %f, '%s', %d);", array($conceptid, $values['title'], $values['description'], $values['icon'], $values['lifetimefuncunits'], $values['funcunitnote'], $this->userid)));
		db_set_active();
		//drupal_set_message(t('Your concept has been updated!'));
		
		return true;
	}
	
	function get_reference_concept($pid){
		if ($this->init_and_check_if_error('get_reference_concept')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Ref_Concept(%d, %d);', array($pid, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		//DO PERMISSION CHECKS
		
		return $object;
		if($this->userid == $object['userID']){
			return $object;
		}else{
			$this->access_denied();
		}
	}
	
	function get_final_concept($pid){
	 	if ($this->init_and_check_if_error('get_final_concept')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Final_Concept(%d, %d);', array($pid, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		//DO PERMISSION CHECKS
		
		return $object;
		if($this->userid == $object['userID']){
			return $object;
		}else{
			$this->access_denied();
		}
	}
	
	function get_best_concept($pid){
		if ($this->init_and_check_if_error('get_best_concept')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Best_Concept(%d, %d);', array($pid, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		//DO PERMISSION CHECKS
		
		return $object;
		if($this->userid == $object['userID']){
			return $object;
		}else{
			$this->access_denied();
		}
	}
	
	function copy_concept($pid, $cid){
		if ($this->init_and_check_if_error('copy_concept')) return '';
		// @id is not used, just a placeholder for the out parameter
		$this->check_for_errors(db_query("CALL SM_SBOM_Copy_Concept(%d, %d, %d, %d, %d, @id);", array($cid, $pid, 1, $this->userid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return '';
	}
	
	function delete_concept($componentid){
		if ($this->init_and_check_if_error('delete_concept')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Concept(%d, %d);", array($componentid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return '';
	}
	
	function concepts_using_image($filename, $conceptID=0) {
		if ($this->init_and_check_if_error('concepts_using_image')) return ''; 
		$count = $this->check_for_errors(db_result(db_query("CALL SM_SBOM_Concepts_Using_Image(%d, '%s');", array($conceptID, $filename))));
		sustainable_minds_clear_db();
		db_set_active();
		
		return $count;
	}
	
	function delete_concept_icon($conceptID){
		if ($this->init_and_check_if_error('delete_concept_icon')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Concept_Icon(%d, %d);", array($conceptID, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return '';
	}
	
	////////////////////////////////Components\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function delete_project($componentid){
		if ($this->init_and_check_if_error('delete_project')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Product(%d, %d);", array($componentid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return '';
	}
	
	function add_component($conceptid, $parentid, $values, $phaseid){
		
		if ($this->init_and_check_if_error('add_component')) return ''; 
		//Create component: conceptID,parentid,name,partID,description,quantity,phaseid
		//FIXME: Add parentid
		$lastid = $this->check_for_errors(db_result(db_query("CALL SM_SBOM_Add_Component(%d, %d, '%s', '%s', '%s', %d, %d, %d, %d);", array($conceptid, $parentid, $values['title'], $values['partID'], $values['description'], $values['quantity'], $phaseid, 0, $this->userid ))));
		//$lastid = sustainable_minds_get_last_id();
		//switch back to drupal db after inserting data
		db_set_active();
		
		//drupal_set_message(t('Your Component has been created! You can now start adding items.'));
		
		return $lastid;
	}
	
	function get_component($id){
		if ($this->init_and_check_if_error('get_component')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Component(%d, %d);', array($id, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		//DO PERMISSION CHECKS
		
		return $object;
		if($this->userid == $object['userID']){
			return $object;
		}else{
			$this->access_denied();
		}
	}
	
	function delete_component($componentid){
		if ($this->init_and_check_if_error('delete_component')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Component(%d, %d);", array($componentid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return;
	}
	
	function update_component($componentid, $values){
		if ($this->init_and_check_if_error('update_component')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Component(%d, '%s', '%s', '%s', %d, 0, %d);", array($componentid, $values['title'], $values['partID'], $values['description'],  $values['quantity'], $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		//drupal_set_message(t('The component has been updated!'));
		
		return true;
	}
	
	function update_component_name($componentid, $name){
		if ($this->init_and_check_if_error('update_component_name')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Component_Name(%d, '%s', %d);", array($componentid, $name, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}
	
	function update_component_parent($componentid, $parentid){
		if ($this->init_and_check_if_error('update_component_parent')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Component_Parent(%d, %d, %d);", array($componentid, $parentid, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}
	
	function update_component_description($componentid, $desc){
		if ($this->init_and_check_if_error('update_component_description')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Component_Description(%d, '%s', %d);", array($componentid, $desc, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}

	function list_components(){
	}
	
	function list_components_by_parent_and_phase($parentid, $phaseid){
		$components = array();
		if ($this->init_and_check_if_error('list_components_by_parent_and_phase')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Components_By_Parent_And_Phase(%d, %d, %d);', array($parentid, $phaseid, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$components[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $components;
	}

	function get_components_by_concept_and_type($conceptid, $typeid){
		if ($this->init_and_check_if_error('get_components_by_concept_and_type')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Component_By_Concept_And_Type(%d, %d, %d);', array($conceptid, $typeid, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();		
		return $object;
	}
	
	function list_components_by_parent($parentid){
		$components = array();
		if ($this->init_and_check_if_error('list_components_by_parent')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Components_By_Parent(%d, %d);', array($parentid, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$components[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $components;
	}
	
	function get_weight($compID) {
		if ($this->init_and_check_if_error('get_weight')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Weight_Return(%d, %d);', array($compID, 0));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row['weight'];
	}

	
	function get_trans_weight($compID) {
		if ($this->init_and_check_if_error('get_trans_weight')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Trans_Weight_Return(%d, %d);', array($compID, 0));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row['weight'];
	}
	
	////////////////////////////////Item\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function add_item($componentid, $values){
		if ($this->init_and_check_if_error('add_item')) return ''; 
		//Create item :componentid,name,description,sequence,factor
		$lastid =$this->check_for_errors(db_result(db_query("CALL SM_SBOM_Add_Item_And_Material(%d, '%s', '%s', '%s', %d, %d, %f, %d, 1, %f, '%s', %d);", array($componentid, $values['title'], $values['partID'], $values['description'], $values['quantity'], $values['newMatProcID'], $values['convertedAmount'], $values['measure'], $values['displayUnitID'], $values['displayAmount'], $this->userid))));
		sustainable_minds_clear_db();
		//switch back to drupal db after inserting data
		db_set_active();
		
		//drupal_set_message(t('A new item has been created.'));
	
		return $lastid;
	}
	
	function get_item($id){
		if ($this->init_and_check_if_error('get_item')) return ''; 
		$result = db_query('Call SM_SBOM_Get_Item(%d, %d)', array($id, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function delete_item($itemid){
		if ($this->init_and_check_if_error('delete_item')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Delete_Item(%d, %d);", array($itemid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return true;
	}
	
	function update_item($itemid, $values){
		if ($this->init_and_check_if_error('update_item')) return ''; 
		//Update item: title, description, measure
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Item(%d, '%s', '%s', '%s', %d, %d, %d);", array($itemid, $values['title'], $values['partID'], $values['description'], $values['quantity'], $values['measure'], $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		return true;
	}
	
	function update_item_name($itemid, $name){
		if ($this->init_and_check_if_error('update_item_name')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Item_Name(%d, '%s', %d);", array($itemid, $name, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}
	
	function update_item_component($itemid, $component){
		if ($this->init_and_check_if_error('update_item_component')) return ''; 
		//Update component: conceptid, name, description
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Item_Component(%d, '%s', %d);", array($itemid, $component, $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}
	
	function update_item_description($itemid, $desc){
		if ($this->init_and_check_if_error('update_item_description')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_Item_Description(%d, '%s', %d);", array($itemid, $desc, $this->userid)));
		db_set_active();
		return true;
	}
	
	function list_item_tree_no_eol($parentid){
		$components = array();
		if ($this->init_and_check_if_error('list_item_tree_no_eol')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Item_Tree_No_EOL(%d);', array($parentid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$components[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $components;
	}

	function list_items_with_process($parentid, $eol='both', $trans=0){
		$items = array();
		if ($this->init_and_check_if_error('list_items_with_process')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Items_With_Process(%d, \'%s\',%d, %d);', array($parentid, $eol, $trans, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$items[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $items;
	
	}
	
	function list_items_no_process($parentid, $eol='both', $trans=0) {
		if ($this->init_and_check_if_error('list_items_no_process')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Items_No_Process(%d, \'%s\',%d, %d);', array($parentid, $eol, $trans, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))) {
			$processes[]= $row;
		}
		
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $processes;
	}

	function list_transportation_items_with_process($parentid, $eol='noteol', $trans=1){
		$items = array();
		if ($this->init_and_check_if_error('list_transportation_items_with_process')) {
			return ''; 
		}
		$result = db_query('CALL SM_SBOM_List_Items_With_Process(%d, \'%s\',%d, %d);', array($parentid, $eol, $trans, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$items[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $items;
	
	}

	function list_transportation_items_no_process($parentid, $eol='noteol', $trans=1){
		$items = array();
		if ($this->init_and_check_if_error('list_transportation_items_no_process')) return ''; 
		$result = db_query('CALL SM_SBOM_List_Items_No_Process(%d, \'%s\',%d, %d);', array($parentid, $eol, $trans, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$items[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $items;
	
	}
	
	function add_process($itemid, $values){
    if ($this->init_and_check_if_error('add_proccess')) return ''; 
	  // bmagee - modified for unit of measure conversion
	  $this->check_for_errors(db_query("CALL SM_SBOM_Add_MatProc_To_Item(%d, %d, '%s', %d, '%s', 0, %d, '%s', %d);", array($itemid, $values['newMatProcID'], $values['convertedAmount'], $values['measure'], $values['description'], $values['displayUnitID'], $values['displayAmount'], $this->userid)));
	
	  db_set_active();
		
	  return $lastid;
	}

	function delete_process($itemid,$processid){
		if ($this->init_and_check_if_error('delete_process')) return ''; 
		$this->check_for_errors( db_query("CALL SM_SBOM_Remove_MatProc_From_Item(%d, %d, %d);", array($itemid, $processid, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	
		return true;
	}
	
	function list_process_by_item($itemid,$eol='both'){
		$processes = array();
		if ($this->init_and_check_if_error('list_process_by_item')) return ''; 
		$result = db_query('CALL SM_SBOM_Get_Procs_By_Item(%d, \'%s\', %d);', array($itemid, $eol,$this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$processes[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $processes;
	}
	
	function list_process_by_material($matid,$eol='both'){
		if ($this->init_and_check_if_error('list_process_by_material')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Process_By_Material(%d, \'%s\');', array($matid,$eol));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$processes[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $processes;
	}
	
	function list_process_for_type($type){
		if ($this->init_and_check_if_error('list_process_for_type')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Process_For_Type(\'%s\');', array($type));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$processes[]= $row;
		}
		
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $processes;
	}
	
	function update_itemmatproc($itemmatprocid, $values){
		if ($this->init_and_check_if_error('update_itemmatproc')) return ''; 
		//Update item: title, description, measure
		$this->check_for_errors(db_query("CALL SM_SBOM_Update_ItemMatProc(%d, %d, %d, '%s', %d);", 
				array($itemmatprocid, $values['newMatProcID'], $values['measure'], $values['description'], $this->userid)));
		//switch back to drupal db after inserting data
		db_set_active();
		
		return true;
	}
	
	function list_matproc($parentid, $eol='both'){
		$items = array();
		if ($this->init_and_check_if_error('list_matproc')) return ''; 
		$arr = array($parentid, $eol, $this->userid);
		$result = db_query('CALL SM_SBOM_List_MatProcs_By_Item(%d, \'%s\', %d);', $arr);
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$items[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $items;
	}
	
	function list_matproc_by_type($parentid, $eol='both', $types){
		$items = array();
		if ($this->init_and_check_if_error('list_matproc_by_type')) return ''; 
		$arr = array($parentid, $eol);
		$arr[] = in_array('material', $types);
		$arr[] = in_array('process', $types);
		$arr[] = in_array('transportation', $types);
		$arr[] = $this->userid;
		$result = db_query('CALL SM_SBOM_List_MatProcs_For_Item_By_Type(%d, \'%s\', \'%s\', \'%s\', \'%s\', %d);', $arr);
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$items[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $items;
	}
	
	function get_itemmatproc($id){
		if ($this->init_and_check_if_error('get_itemmatproc')) return ''; 
		$result = db_query('Call SM_SBOM_Get_ItemMatProc(%d, %d)', array($id, $this->userid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}

	
	////////////////////////////////OkalaScore\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	function okala_concept_by_impact($conceptid){
		if ($this->init_and_check_if_error('get_version_for_concept')) return '';
		$version = $this->get_version_for_concept($conceptid);
		if ($this->init_and_check_if_error('okala_concept_by_impact')) return '';
		if ($version == "SM 2013") {
			$result = db_query("CALL SM_SBOM_Concept_Okala_Exp(%d, %d);", array($conceptid, $this->userid));
		} else {
			$result = db_query("CALL SM_SBOM_Concept_Okala_Exp_pre2013(%d, %d);", array($conceptid, $this->userid));
		}
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_total($conceptid){
		if ($this->init_and_check_if_error('okala_concept_total')) return '';
		$object=  array();
		$result = db_query('CALL SM_SBOM_Concept_Scores(%d, %d);', array($conceptid, $this->userid));
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_by_phase($conceptid){
		if ($this->init_and_check_if_error('okala_concept_by_phase')) return '';
		$result = db_query('CALL SM_SBOM_Concept_Okala_By_Phase(%d, %d);', array($conceptid, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function co2_concept_by_phase($conceptid){
		if ($this->init_and_check_if_error('co2_concept_by_phase')) return '';
		$result = db_query('CALL SM_SBOM_Concept_Co2_By_Phase(%d, %d);', array($conceptid, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_top_okala_impacts($conceptid){
		if ($this->init_and_check_if_error('okala_concept_top_okala_impacts')) return '';
		$result = db_query('CALL SM_SBOM_Get_Top_Okala_MatProcs_For_Concept(%d, %d);', array($conceptid, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_top_okala_impacts_total($conceptid){
		if ($this->init_and_check_if_error('okala_concept_top_okala_impacts_total')) return '';
		$result = db_query('CALL SM_LCA_Get_Top_Okala_Items_By_Concept_B_Total(%d);', array($conceptid));
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_top_co2_impacts($conceptid){
		if ($this->init_and_check_if_error('okala_concept_top_co2_impacts')) return '';
		$result = db_query('CALL SM_SBOM_Get_Top_CO2_Items_For_Concept(%d, %d);', array($conceptid, $this->userid));
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_top_co2_impacts_total($conceptid){
		if ($this->init_and_check_if_error('okala_concept_top_co2_impacts_total')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Top_CO2_Items_By_Concept_B_Total(%d);', array($conceptid)); 
		$object = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}

	function score_concept_by_phase($conceptid){
		if ($this->init_and_check_if_error('score_concept_by_phase')) return ''; 
		$result = db_query('CALL SM_LCA_Concept_By_Phase_B(%d);', array($conceptid)); 
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	
	function okala_concept_top_impacts($conceptid){
		if ($this->init_and_check_if_error('okala_concept_top_impacts')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Top_Items_By_Concept(%d);', array($conceptid)); 
		$object = array();
		while($currentobject = $this->check_for_errors(db_fetch_array($result))){
			$object[]=$currentobject;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $object;
	}
	////////////////////////////////Other\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	
	function list_phases(){
		$allphases = array();
		if ($this->init_and_check_if_error('list_phases')) return ''; 
		$result = db_query('CALL SM_LCA_List_Phases();'); 
		while($row = $this->check_for_errors(db_fetch_array($result) ) ){
			$allphases[$row['phaseID']]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $allphases;
	}
	
	
	function list_measurementTypes() {
	
		$allphases = array();
		if ($this->init_and_check_if_error('list_measurementTypes')) return ''; 
		$result = db_query('CALL SM_SBOM_List_MeasurementTypes();'); 
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$allphases[$row['measurementTypeID']]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $allphases;
	}
	
	
	/////////////////////////////////Ajax\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	
	function list_matproc_by_category($categoryid){
		$matproc = array();
		if ($this->init_and_check_if_error('list_matproc_by_category')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MatProc_By_Cat(%d);', array($categoryid, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$matproc[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $matproc;
	}

	//this one also returns dataset version
	function list_matproc_by_category2($categoryid){
		$matproc = array();
		if ($this->init_and_check_if_error('list_matproc_by_category2')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MatProc_By_Cat2(%d);', array($categoryid, $this->userid));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$matproc[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $matproc;
	}

	
	function list_mp_category($parentid){
		$category = array();
		if ($this->init_and_check_if_error('list_mp_category')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MPC_Children(%d);', array($parentid));
		
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$category[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $category;
	}
	
	function get_matproc($matprocid){
		if ($this->init_and_check_if_error('get_matproc')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MatProc(%d);', array($matprocid));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function list_matproc_category_parents($matprocid){
		if ($this->init_and_check_if_error('list_matproc_category_parents')) return ''; 
		$result = db_query('CALL SM_LCA_List_MatProc_Category_Parents(%d);', array($matprocid));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	////////////////////////////////Misc\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	function access_denied(){
		sustainable_minds_access_denied();
		die();
		return false;
	}
	
	function set_imp_factor($impid, $factor, $displayUnit = NULL, $baseUnit = NULL) {
	  if ($displayUnit != NULL && $baseUnit != NULL) {
	    $info = sm_unit_convert($displayUnit, $factor, $baseUnit);
	  }
		$displayUnitID = unitsapi_getUnitID($displayUnit);
		$convertedAmount = isset($info['result']) ? $info['result'] : $factor;
  	if ($this->init_and_check_if_error('set_imp_factor')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Set_IMP_Factor(%d, '%s', '%s', '%s', %d);", array($impid, $convertedAmount, $displayUnitID, $factor, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	  return;
	}
	
	function set_rimp_factor($rimpid, $factor, $displayUnit = NULL, $displayFactor = NULL, $baseUnit = NULL) {
	  if ($displayUnit != NULL && $baseUnit != NULL) {
	    $info = sm_unit_convert($displayUnit, $factor, $baseUnit);
	  }
	  if ($displayFactor == NULL) {
		$displayFactor = $factor;
	  }
    $displayUnitID = unitsapi_getUnitID($displayUnit);
	$convertedAmount = isset($info['result']) ? $info['result'] : $factor; 
  	if ($this->init_and_check_if_error('set_rimp_factor')) return ''; 
		$this->check_for_errors(db_query("CALL SM_SBOM_Set_RIMP_Factor(%d, '%s', %d, '%s', %d);", array($rimpid, $convertedAmount, $displayUnitID, $displayFactor, $this->userid)));
		sustainable_minds_clear_db();
		db_set_active();
	  return;
	}
	
	function list_matproc_category($matprocid){
		if ($this->init_and_check_if_error('list_matproc_category')) return ''; 
		$result = db_query('CALL SM_LCA_List_MatProc_Category(%d);', array($matprocid));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function update_mpclink($mpclinkID, $matprocID, $categoryID) {
		if ($this->init_and_check_if_error('update_mpclink')) return ''; 
		db_query("CALL SM_LCA_Update_MPCLink(%d, %d, %d)", array($mpclinkID, $matprocID, $categoryID));
		//sustainable_minds_clear_db($result);
		db_set_active();
		return ;
	}

	function get_result_label_option($productID) {
	    if ($this->init_and_check_if_error('get_result_label_option')) return '';
		// Fetch the JSON string from the User Profile table
		$result = db_query('CALL SM_User_Profile_Get(%d,%d);', array($this->userid, $productID));
		$user_profile = $this->check_for_errors(db_result($result));
		// decode JSON and fetch the Option: value
		sustainable_minds_clear_db($result);
		db_set_active();
		return $user_profile;
    }

	function set_unit_name($unit_symbol) {
		if ($this->init_and_check_if_error('set_unit_name')) return '';
		$unit_name = unitsapi_getunitname($unit_symbol);
//		sustainable_minds_clear_db($result);
		db_set_active();
		return $unit_name;
	}
	
	function set_result_label_option($productID, $sel_label) {
		if ($this->init_and_check_if_error('set_result_label_option')) return '';
		// JSON encode the selected label
		$save_label = '{option: '.$sel_label.'}';
		db_query("CALL SM_User_Profile_Set(%d,%d,'%s');", array($this->userid, $productID, $save_label));
		db_set_active();
		return true;
	}
		// KJH - invokes the stored procedure to perform the copy of an item in the sbom
	function copy_sbom_item($itemID, $componentID, $isTop) {
		if ($this->init_and_check_if_error('copy_sbom_item'))
		return '';
		db_query("CALL SM_SBOM_Copy_Item(%d, %d, %d);", array($itemID, $componentID, $isTop));
		db_set_active();
		return true;
	}
	// KJH - invokes the stored procedure to perform the copy of a component in the sbom
	function copy_sbom_component($componentID, $parentID, $conceptID, $isTop) {
		if ($this->init_and_check_if_error('copy_sbom_component'))
		return '';
		db_query("CALL SM_SBOM_Copy_Component(%d, %d, %d, %d);", array($componentID, $parentID, $conceptID, $isTop));
		db_set_active();
		return true;
	}
	
	//////////////////////////////// DATALOAD \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
	function get_matproc_category_by_name($category, $categoryparent='') {
		if ($this->init_and_check_if_error('get_matproc_category_by_name')) return ''; 
		if (empty($categoryparent)) {
			$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name('%s');", array($category));
		} else {
			$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_Parent('%s', '%s');", array($category, $categoryparent));
		}

		$row = $this->check_for_errors(db_fetch_array($result));
		$parentID = $row['matProcCategoryID'];
		sustainable_minds_clear_db($result);
		db_set_active();
		return $parentID;
	}
	
	function add_matproc_category($category, $parentID=0) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc_Category(%d, '%s', '%s')", array($parentID, $category, ''));
		$catID = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $catID;
	}
	
	function get_matproc_category_by_name_parentID($category, $parentID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_ParentID('%s', %d);", array($category, $parentID));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_name_matproc_alias($material) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_Name_MatProc_Alias('%s')", array($material));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_matproccategory_temp($categoryID, $material) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProcCategory_Temp(%d, '%s')", array($category, $material));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function delete_matProcCategory_temp_by_name($material) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("SM_LCA_Delete_MatProcCategory_Temp_By_Name('%s')", array($material));
		sustainable_minds_clear_db($result);
		db_set_active();
		return ;
	}
	
	function add_matproc_category_temp($categoryID, $material) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc_Category_Temp(%d, '%s', '%s')", array($categoryID, $material, ''));
		$catID = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $catID;
	}
	
	function get_matproc_by_name($material) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_By_Name('%s')", array($material));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function delete_mpclink_by_mp($matProcID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Delete_MPCLink_By_MP(%d)", array($matProcID));
		sustainable_minds_clear_db($result);
		db_set_active();
		return;
	}
	
	function add_mpc_to_matproc($matProcID, $categoryID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MPC_To_MatProc(%d, %d)", array($matProcID, $categoryID));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}	
	
	function get_matproc_links_by_materialID($materialID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProcLinks_By_MaterialID('%s')", array($materialID));
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}
	
	function get_matproc_links_by_processID($processID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProcLinks_By_ProcessID('%s')", array($processID));
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}
	
	function get_matproc_link_by_ids($materialID, $processID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Link_By_Ids(%d, %d);", array($materialID, $processID));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function add_matproc_link($materialID, $processID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc_Link(%d, %d)", array($materialID, $processID));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function update_matproclink($materialID, $processID, $mplinkID) {
		if ($this->init_and_check_if_error('')) return ''; 
		db_query("CALL SM_LCA_Update_MatProcLink(%d, %d, %d)", array($materialID, $processID, $mplinkID));
		//sustainable_minds_clear_db($result);
		db_set_active();
		return;
	}

	function update_matproc_desc($matProcID, $desc) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Update_MatProc_Description(%d,'%s')", array($matProcID, $desc));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $result;
	}	

	function update_matproc_name($matProcID, $name) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Update_MatProc_Name(%d,'%s')", array($matProcID, $name));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $result;
	}

	function get_matproc_id_alias($matprocid, $alias) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Id_Alias(%d, '%s')", array($matprocid, $alias));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function add_matproc_alias($matprocid, $alias) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc_Alias(%d, '%s')", array($matprocid, $alias));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}
	
	function get_unit_by_name($matUnit) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_Get_Unit_By_Name('%s')", array($matUnit));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}
	
	function add_unit($matUnit) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_Unit('%s', '%s')", array($matUnit, ''));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}
	
	function list_matproc_alias($myname) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Alias('%s')", array($myname));
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row['alias'];
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}
	
	function get_matproc_category_by_name_temp($pname) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_Temp('%s')", array($pname));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function update_matproc($matprocID, $myname, $desc, $matID, $enum, $endoflife) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Update_MatProc(%d, '%s', '%s', %d, '%s', %d)", array($matprocID, $myname, $desc, $matID, $enum, $endoflife));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $result;
	}
	
	function add_matproc($myname, $desc, $matID, $mpcid, $enum, $endoflife) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc('%s', '%s', %d, %d, '%s', %d)", array($myname, $desc, $matID, $mpcid, $enum, $endoflife));
		$lastid = db_result($result, 0);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $lastid;
	}
	
	function get_matproc_impact_by_mp_i($id, $col) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_Get_MatProc_Impact_By_MP_I(%d, %d)", array($id, $col));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function add_matproc_impact($lastid, $col, $val, $phaseid) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_MatProc_Impact(%d, %d, '%s', %d)", array($lastid, $col, $val, $phaseid));
		$lastid = db_result($result,0);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $lastid;
	}
	
	function update_matproc_impact($mpcid, $lastid, $col, $val, $phaseid) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Update_MatProc_Impact(%d, %d, %d, '%s', %d)", array($mpcid, $lastid, $col, $val, $phaseid));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $result;
	}
	
	function list_impacts() {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_List_Impact()");
		$cols = array();
		while($row = db_fetch_array($result)) $cols[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $cols;
	}
	
	function list_impacts_version($version) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_List_Impact_version('%s')", array($version));
		$cols = array();
		while($row = db_fetch_array($result)) $cols[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $cols;
	}
	
	/**************************************
 	 * New procs for Dataset Versioning
 	 * - bmagee 7-6-2010
 	 **************************************/
	function add_dataset($version, $revision, $description, $published) {
		if ($this->init_and_check_if_error('')) return ''; 
		//$description = "Enter dataset description here.";
		db_query("CALL SM_LCA_Add_DatasetInfo('%s', '%s', '%s', %d)", array($version, $revision, $description, $published));
		sustainable_minds_clear_db($result);
		db_set_active();
	}

	function map_dataset($src_version, $dest_version) {
		if ($this->init_and_check_if_error('')) return ''; 
		//$description = "Enter dataset description here.";
		db_query("CALL SM_LCA_Map_DataSet('%s', '%s')", array($src_version, $dest_version));
		sustainable_minds_clear_db();
		db_set_active();
	}

	function update_dataset($version, $revision, $description, $published) {
		if ($this->init_and_check_if_error('')) return ''; 
		//$description = "Enter dataset description here.";
		db_query("CALL SM_LCA_Update_DatasetInfo('%s', '%s', '%s', %d)", array($version, $revision, $description, $published));
		sustainable_minds_clear_db($result);
		db_set_active();
	}

	function delete_dataset($version, $revision='1.0') {
		if ($this->init_and_check_if_error('')) return ''; 
		//$description = "Enter dataset description here.";
		db_query("CALL SM_LCA_Delete_Dataset('%s', '%s')", array($version, $revision));
		sustainable_minds_clear_db($result);
		db_set_active();
	}

	function list_datasetInfo() {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_List_DatasetInfo()");
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}

	function get_latest_version($status) {
		if ($this->init_and_check_if_error('')) return ''; 
        $conn =  Database::getConnection();
		$result = $statement = $conn->prepare("CALL SM_LCA_Get_Latest_Dataset_Version('%s')", array($status));
		$exec_result = $statement->execute();
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		// db_set_active();
		Database::setActiveConnection();
		return $id;
	}

	function get_datasetInfo($version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_DatasetInfo('%s')", array($version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	function find_datasetInfo($version, $revision) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Find_DatasetInfo('%s', '%s')", array($version, $revision));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function add_datasetFiles($version, $revision, $datatype, $filename) {
		if ($this->init_and_check_if_error('')) return ''; 
		//$description = "Enter dataset description here.";
		db_query("CALL SM_LCA_Add_DatasetFiles('%s', '%s', '%s', '%s')", array($version, $revision, $datatype, $filename));
		sustainable_minds_clear_db($result);
		db_set_active();
	}
	function list_datasetFiles($version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_DatasetFiles('%s')", array($version));
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row;
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}

	function get_datasetFiles($version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_DatasetFiles('%s')", array($version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function add_dataset_version($version, $revision, $datatype, $recordID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Add_Dataset_Version('%s', '%s', '%s', '%s')", array($version, $revision, $datatype, $recordID));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}

	function update_dataset_version_recordID($version, $revision, $datatype, $recordID) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Update_Dataset_Version_RecordID('%s', '%s', '%s', '%s')", array($version, $revision, $datatype, $recordID));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function update_dataset_version($version, $revision, $datatype, $recordID) {
		if ($this->init_and_check_if_error('')) return ''; 
		db_query("CALL SM_LCA_Update_Dataset_Version('%s', '%s', '%s', '%s')", array($version, $revision, $datatype, $recordID));
		//sustainable_minds_clear_db($result);
		db_set_active();
		return;
	}

	function update_impact_for_matproc_version($matprocID, $version, $revision) {
		if ($this->init_and_check_if_error('')) return ''; 
		db_query("CALL SM_LCA_Update_Impact_MatProc_Version(%d, '%s', '%s')", array($matprocID, $version, $revision));
		//sustainable_minds_clear_db($result);
		db_set_active();
		return;
	}

	function list_dataset_version() {
		if ($this->init_and_check_if_error('')) return ''; 
        $conn =  Database::getConnection();
		$statement =  $conn->prepare("CALL SM_LCA_List_Dataset_Version()");
		$arr = array();
		while ($row = $statement->fetchAssoc()) {
			$arr[] = $row;
		}
		sustainable_minds_clear_db($statement);
		Database::setActiveConnection();
		return $arr;
	}

	function find_version_map($src_version, $dest_version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Find_DataSet_Map('%s', '%s')", array($src_version, $dest_version));
		$count = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $count;
	}

	function get_version_for_concept($cid) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_SBOM_Get_Version_For_Concept(%d)", array($cid));
		$version = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $version;
	}

	function get_version_for_project($pid) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_SBOM_Get_Product_Version(%d)", array($pid));
		$version = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $version;
	}
	
	function get_matproc_version($matprocid, $version){
		if ($this->init_and_check_if_error('get_matproc')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MatProc_Version(%d, "%s");', array($matprocid, $version));
		$row = $this->check_for_errors(db_fetch_array($result));
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_matproc_by_name_version($material, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_By_Name_Version('%s', '%s')", array($material, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_name_matproc_alias_version($material, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_Name_MatProc_Alias_Version('%s', '%s')", array($material, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function get_matprocid_alias_version($matprocid, $alias, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProcID_Alias_Version(%d, '%s', '%s')", array($matprocid, $alias, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

	function list_matproc_alias_version($myname, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Alias_Version('%s', '%s')", array($myname, $version));
		$arr = array();
		while ($row = db_fetch_array($result)) $arr[] = $row['alias'];
		sustainable_minds_clear_db($result);
		db_set_active();
		return $arr;
	}
	
	function get_matproc_category_by_name_temp_version($pname, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_Temp_Version('%s', '%s')", array($pname, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function delete_matproc_category_by_name_temp_version($pname, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Delete_MatProcCategory_Temp_By_Name_Version('%s', '%s')", array($pname, $version));
		sustainable_minds_clear_db($result);
		db_set_active();
		return;
	}
	
	function get_matproccategory_temp_version($categoryID, $material, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProcCategory_Temp_Version(%d, '%s', '%s')", array($category, $material, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_MPC_for_matproc_version($mpname, $version) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_Get_MatProcCategory_For_MatProc_Version('%s', '%s')", array($mpname, $version));
		$id = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $id;
	}
	
	function get_matproc_impact_by_mp_i_version($id, $col, $version) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_Get_MatProc_Impact_By_MP_I_Version(%d, %d, '%s')", array($id, $col, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	
	function get_matproc_category_by_name_version($category, $categoryparent='', $version) {
		if ($this->init_and_check_if_error('get_matproc_category_by_name')) return ''; 
		if (empty($categoryparent)) {
			$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_Version('%s', '%s');", array($category, $version));
		} else {
			$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_Parent_Version('%s', '%s', '%s');", array($category, $categoryparent, $version));
		}

		$row = $this->check_for_errors(db_fetch_array($result));
		$parentID = $row['matProcCategoryID'];
		sustainable_minds_clear_db($result);
		db_set_active();
		return $parentID;
	}
	
	function get_matproc_category_parentid_by_id($categoryid) {
		if ($this->init_and_check_if_error('get_matproc_category_parent_by_id')) return '';
		$result = db_query("CALL SM_LCA_Get_MatProc_Category_ParentID_by_ID('%d');", array($categoryid));
		$row = db_fetch_array($result);
		$parentID = $row['parentID'];
		sustainable_minds_clear_db($result);
		db_set_active();
		return $parentID;
	}
		
	function get_matproc_category_by_name_parentID_version($category, $parentID, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Category_By_Name_ParentID_Version('%s', %d, '%s');", array($category, $parentID, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}
	function get_matproc_link_by_ids_version($materialID, $processID, $version) {
		if ($this->init_and_check_if_error('')) return ''; 
		$result = db_query("CALL SM_LCA_Get_MatProc_Link_By_Ids_Version(%d, %d, '%s');", array($materialID, $processID, $version));
		$row = db_fetch_array($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $row;
	}

  /* UI */
	function get_version_for_component($componentID) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_SBOM_Get_Version_For_Component(%d, %d)", array($componentID, $this->userid));
		$version = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $version;
	}
	
	function get_owner_private_mpc($mpcid) {
		if ($this->init_and_check_if_error('')) return '';
		$result = db_query("CALL SM_LCA_Get_Owner_Private_Mpc(%d)", array($mpcid));
		$ownerid = db_result($result);
		sustainable_minds_clear_db($result);
		db_set_active();
		return $ownerid;
	}

	function list_mp_category_version($parentid, $cattype, $version){
		$category = array();
		if ($this->init_and_check_if_error('list_mp_category')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MPC_Children_Version(%d, "%s", "%s");', array($parentid, $cattype, $version));

		while($row = $this->check_for_errors(db_fetch_array($result))){
			$category[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $category;
	}
	
	function list_matproc_by_category_version($categoryid, $version){
	//drupal_set_message('categoryid: '.$categoryid.' - version: '.$version);
	
		$matproc = array();
		if ($this->init_and_check_if_error('list_matproc_by_category')) return ''; 
		$result = db_query('CALL SM_LCA_Get_MatProc_By_Cat_Version(%d, "%s");', array($categoryid, $version));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$matproc[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $matproc;
	}

	function list_process_for_type_version($type, $version){
		if ($this->init_and_check_if_error('list_process_for_type')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Process_For_Type_Version("%s", "%s");', array($type, $version));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$processes[]= $row;
		}
		
		sustainable_minds_clear_db($result);
		db_set_active();
		
		return $processes;
	}

	function list_process_by_material_version($matid,$eol='both', $version){
		if ($this->init_and_check_if_error('list_process_by_material')) return ''; 
		$result = db_query('CALL SM_LCA_Get_Process_By_Material_Version(%d, "%s", "$s");', array($matid,$eol,$version));
		while($row = $this->check_for_errors(db_fetch_array($result))){
			$processes[]= $row;
		}
		sustainable_minds_clear_db($result);
		db_set_active();
		return $processes;
	}
    public function getCategories(){
		Database::setActiveConnection('d5_dump');
        $conn =  Database::getConnection();
        $statement = $conn->prepare("CALL SM_SBOM_List_PCategories();");
        $exec_result = $statement->execute();
        while($row = $statement->fetchAssoc()){
            if($row['name']!=''){
                $categories[$row['pcategoryID']] = $row['name'];
            }else{
                $categories[$row['pcategoryID']] = '(not entered)';
            }
        }
		sustainable_minds_clear_db($statement);
        return $categories;
    }
}