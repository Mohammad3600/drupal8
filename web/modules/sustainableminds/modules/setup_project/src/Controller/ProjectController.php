<?php
/**  
 * @file  
 * Contains Drupal\setup_project\Form\MyProject.  
 */  
namespace Drupal\setup_project\Controller;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;  
use Drupal\user\Entity\User;
include_once(dirname(__FILE__).'\..\..\products.inc');
class ProjectController{

    /**
    * returns blank project id
    */
    function newProject() {
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

    /**
    * returns html for listing projects 
    */
    public function projectMarkup(){
        $user_projects = sustainable_minds_user_products();
        return [
            '#children' => $user_projects,
          ];
    }
    public function uploadFile(){
    $t = $_FILES["img_upload"];
    // $dir = "drupal8/web/sites/default/files";
    // move_uploaded_file($_FILES["img_upload"]["tmp_name"], $dir. $_FILES["img_upload"]["name"]);
    }

    /**
     * return view of the product page
     */
    public function productView($page,$pid){
        $db = \Drupal::service('setup_project.sbom_db');
	    $product = $db->get_product($pid);
        if (!$product || !$pid || !$product['isComplete']) {
            sustainable_minds_access_denied();
        }
        $wizard = sustainable_minds_project_view_wizard($page, $pid, $product['name']);
        $output = '';
        switch($page){
            case 'view':
                // $editStep = 1 ; 
                $output .= sustainable_minds_product_view($product, $pid, $page); 
                break;
            case 'goal':
                // $editStep = 2 ;
                $output .= sustainable_minds_assessment_goal($pid, $product, $page);
                break ;
            case 'scope':
                // $editStep = 3 ; 
                $output .=  sustainable_minds_assessment_scope($pid, $product, $page);
                break;
            case 'concepts':
                // $editStep = 4 ;
                $output .=  sustainable_minds_product_concepts();
                break;
        }
        return [
            '#children' => $wizard . $output ,
        ];
    }

}