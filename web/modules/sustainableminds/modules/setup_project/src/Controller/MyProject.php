<?php
/**  
 * @file  
 * Contains Drupal\setup_project\Form\MyProject.  
 */  
namespace Drupal\setup_project\Controller;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;  
use Drupal\user\Entity\User;
class MyProject{
    function newProject() {
        $db = \Drupal::service('setup_project.sbom_db');
        $id = $db->add_blank_product();
        echo $id ;
        exit();
    }
    public function myProjectMarkup(){
        return array('#markup' => '<div class="my_project_leftcol">
        <div class="my_project_list d-flex flex-wrap justify-content-space-between align-items-center">
        <div class="col-lg-8">
            <h1 class="heading_6 mb-0">My Projects</h1>
        </div>
        <div class="col-lg-4 text-end" id="NewProject" onclick="new_project();">
            <a class="project_btn btn btn-success btn-sm">Set up a new Project <img src="/drupal8/web/sites/default/files/2021-07/plus.svg" alt="plus-icon" title="plus"> </a>
        </div>
        </div>
    </div>');
    }
}