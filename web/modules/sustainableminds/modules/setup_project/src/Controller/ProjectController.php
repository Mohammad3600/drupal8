<?php
/**  
 * @file  
 * Contains Drupal\setup_project\Controller\ProjectController.  
 */  
namespace Drupal\setup_project\Controller;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;  
use Drupal\user\Entity\User;
class ProjectController{

    /**
    * returns html for listing projects 
    */
    public function projectMarkup(){
        $user_projects = sustainable_minds_user_products();
        return [
            '#children' => $user_projects,
          ];
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
                $output .=  sustainable_minds_concept_list($pid, $product, $page);
                break;
        }
        return [
            '#children' =>  $wizard . $output ,
        ];
    }

    public function viewConcept($conceptid = null){
        $db = \Drupal::service('setup_project.sbom_db');
        $concept = $db->get_concept($conceptid);
        $output = sustainable_minds_concept_view($concept, $conceptid);
        $wizard = sustainable_minds_concept_wizard($concept, $conceptid);
        return [
            '#children' => $wizard . $output ,
        ];
    }
    public function viewBOM($conceptid = null, $phaseid = null){
        $db = \Drupal::service('setup_project.sbom_db');
        $concept = $db->get_concept($conceptid);
        $wizard = sustainable_minds_concept_wizard($concept, $conceptid);
        $comp_type = 0;
        $sbom_tabs = sbom_tabs($conceptid, (int)$phaseid);
        // switch($phaseid){
        //     case PHASEID_MANUFACTURE:
        //         $treegrid = new  phases_tg_manufacturing($conceptid);       
        //     break;
        //     case PHASEID_USE:
        //         $treegrid = new phases_tg_use($conceptid);
        //     break;
        //     case PHASEID_EOL :
        //         $treegrid =  new phases_tg_eol($conceptid);
        //     break;
        //     case PHASEID_TRANSPORT:
        //         $treegrid = new phases_tg_transportation($conceptid);
        //     break;
        // }

        // $output .= $treegrid->draw();
        return [
            '#children' => $wizard .$sbom_tabs,
        ];
    }
    public function viewResults($conceptid = null){
        $db = \Drupal::service('setup_project.sbom_db');
        $concept = $db->get_concept($conceptid);
        // $output = sustainable_minds_concept_view($concept, $conceptid);
        $wizard = sustainable_minds_concept_wizard($concept, $conceptid);
        return [
            '#children' => $wizard ,
        ];
    }
}