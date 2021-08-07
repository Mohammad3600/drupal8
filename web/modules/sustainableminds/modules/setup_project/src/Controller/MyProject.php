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
    public function myProjectMarkup(){
        return array('#markup' => '<div class="my_project_leftcol">
        <div class="my_project_list d-flex flex-wrap justify-content-space-between align-items-center">
        <div class="col-lg-8">
            <h1 class="heading_6 mb-0">My Projects</h1>
        </div>
        <div class="col-lg-4 text-end">
            <a href="../add/definition" class="project_btn btn btn-success btn-sm">Set up a new Project <img src="/drupal8/web/sites/default/files/2021-07/plus.svg" alt="plus-icon" title="plus"> </a>
        </div>
        </div>
    </div>');
    }
    public function checkTaxonomyTerm(){
        // $vocab_name='learning_center';
        // $query = \Drupal::entityQuery('taxonomy_term');
        // $query->condition('vid', $vocab_name);
        // $tids = $query->execute();
        // $terms = Term::loadMultiple($tids);
        // $res = '';
        // foreach($terms as $term) {
        //      $name = $term->getName();
        //      $res .= $name;
        // }
        // return array('#markup'=>$res);
        // db_set_active('sbom');
	// $statement = db_query('CALL SM_SBOM_List_PCategories();');
	// while($row = mysqli_fetch_array($result)){
	// 	$categories[$row['pcategoryID']] = $row['name'];
	// }
    $conn =  Database::getConnection();

// Prepare the statement and bind params
$statement = $conn->prepare("CALL SM_SBOM_List_PCategories();");

// Execute the statement and reset the connection's statement class to the original.
$exec_result = $statement->execute();
while($row = $statement->fetchAssoc()){
		$categories[$row['pcategoryID']] = $row['name'];
	}
    return array('#markup'=>'hey');
    }
}