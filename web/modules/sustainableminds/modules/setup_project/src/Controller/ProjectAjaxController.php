<?php
/**  
 * @file  
 * Contains Drupal\setup_project\Controller\ProjectAjaxController.  
 */  
namespace Drupal\setup_project\Controller;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;  
use Drupal\user\Entity\User;
class ProjectAjaxController{

    
    /**
     * Creates blank product
     */
    function sustainable_minds_project_new() {
        $db = \Drupal::service('setup_project.sbom_db');
        $id = $db->add_blank_product();
        echo $id ;
        exit();
    }

    /**
    * delete project from db
    */
    function sustainable_minds_delete_project($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->delete_project($_GET['pid']);
        exit();
    }

    /**
    * copy project from db
    */
    function sustainable_minds_copy_project($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->copy_project($_GET['pid']);
        $pid = $_GET['pid'];
        exit();
    }

    /**
    * copies project to targeted user
    */
    function sustainable_minds_copy_to_project($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $to_user =  user_load_by_name($_GET['target']);
        /* check if target username exists */
        // if it does, copy the project
        if ($to_user) {
            $result = $db->copy_from_to_project($_GET['pid'], $user->get('uid')->value, $to_user->get('uid')->value);
        } else {
            echo('noname');
        }
        exit();
    }

    function sustainable_minds_copy_update_project($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $new_pid = $db->copy_project($_GET['pid']);
        $failed_items = $db->update_project_dataset($new_pid, $_GET['version']);
        
        foreach ($failed_items as $f) {
          $message .= $f['name'].'<br />';
        }
        
        if ($message) {
            //$message = 'The following materials and processes were not updated because they did not have corresponding materials and processes in the new LCA dataset version: <br />'.$message;
            $message = 'The project was successfully copied and successfully updated to use the most recent methodology.<br /><br /> Due to differences between current and previous methodologies, the following materials and/or processes were not updated, but still remain in the concept SBOMs. Take note of these items, and manually assign new materials and processes in your concepts. <br />'.$message;
        }
    }


    function sustainable_minds_set_concept_reference($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->set_product_reference($_GET['pid'],$_GET['cid']);
        exit();
    }
    
    function sustainable_minds_set_concept_final($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->set_product_final($_GET['pid'],$_GET['cid']);
        exit();
    }
    
    function sustainable_minds_delete_concept($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->delete_concept($_GET['cid']);
        exit();
    }

    function sustainable_minds_concept_copy($str='') {
        $db = \Drupal::service('setup_project.sbom_db');
        $db->copy_concept($_GET['pid'], $_GET['cid']);
        exit();
    }
}