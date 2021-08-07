<?php

namespace Drupal\setup_project\Services;
use Drupal\Core\Database\Database;

class ProjectService{
    public function getCategories(){
        $conn =  Database::getConnection();
        $statement = $conn->prepare("CALL SM_SBOM_List_PCategories();");
        $exec_result = $statement->execute();
        while($row = $statement->fetchAssoc()){
           $categories[$row['pcategoryID']] = $row['name'];
        }
        return $categories;
    }
}