<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\ProjectForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormState;
include(dirname(__FILE__).'\..\..\init.inc');
include(dirname(__FILE__).'\..\..\products.inc');

class SetupProject extends FormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'setup_project.product_details',  
    ];  
  }
  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'project_form';  
  }  

  public $page;

  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state, $pid=null, $page=null) { 
	return sustainable_minds_product_edit($pid, $form_state, $page);
}

public function nextToGoal(array &$form, FormStateInterface $form_state){
	$this->page = 'goals';
	$form_state -> setRebuild(TRUE);
}
public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->getValue('op') == 'Cancel') {
      $goto = URL_PROJECT_LIST_USER;
      // if (arg(1) == 'edit' ) {
      //   switch (arg(3)) {
      //     case PROJECT_PAGE_NAME_DEFINITION:
      //       $goto = URL_PROJECT_VIEW  ; 
      //       break;
      //     case PROJECT_PAGE_NAME_GOALS:
      //       $goto = URL_PROJECT_GOAL ; 
      //       break ;
      //     case PROJECT_PAGE_NAME_SCOPE:
      //       $goto = URL_PROJECT_SCOPE ; 
      //       break;
      //   }
      //   // $goto .='/' . sustainable_minds_arg_set(2);
      // } 
      /*elseif ($form_values['funame']) {
      
      }*/
  
      
      // drupal_goto($goto);
      $form_state->setRedirect('setup_project.my_project');
      drupal_set_message();
      // die();
    }
  
    switch ($form_state->getValue('page')) {
      case PROJECT_PAGE_NAME_DEFINITION:
        if ($form_state->getValue('pcategoryID') == 0) {
          // setError('pcategoryID', t('You must select a category'));
          drupal_set_message(t('You must select a category'), 'error');
        }
        if (!$form_state->getValue('name')) {
          // setError('name', t(TEXT_ERROR_PROJECT_NAME));
          drupal_set_message(t(TEXT_ERROR_PROJECT_NAME), 'error');
        }
      break;
      case PROJECT_PAGE_NAME_SCOPE:
        if (!$form_state->getValue('funame')) {
          // setError('funame', t(TEXT_ERROR_PROJECT_FUNAME));
          drupal_set_message(t(TEXT_ERROR_PROJECT_FUNAME), 'error');

        }
      break;
    }
  }
public function submitForm(array &$form, FormStateInterface $form_state) {  
    
	$page = $form_state->getValue('page');
	/*
	if ($page==PROJECT_PAGE_NAME_SCOPE) {
		$current_files = sustainable_minds_edit_multi_image_final($form_values, '/');
		$form_values['icon'] = $current_files['first'];
		if (!$form_values['icon']) 
			$form_values['icon'] = '';
	}
	*/
	$productID = $form_state->getValue('pid');
	//update product
	$db = \Drupal::service('setup_project.sbom_db');
	// $product = $db->get_product($productID);
	// foreach ($product as $key=>$value) {
	// 	if ($form_state->getValue($key)!==null)
	// 		$product[$key] = $form_state->getValue($key);
	// } 
  $base_path = '/drupal8/web/';

	// $db->update_product($productID, $product);
	if ($form_state->getValue('name')) {
	if ($page == 'edit' ) {
		switch ($page) {
			case PROJECT_PAGE_NAME_GOALS:
				$goto = URL_PROJECT_GOAL;
				break ;
			case PROJECT_PAGE_NAME_SCOPE:
				$goto = URL_PROJECT_SCOPE;
				break;
			default:
				$goto = URL_PROJECT_VIEW;
				break;
		}
		$goto.='/'.$productID;
	} else {
		if ($form_values['to_page'] && $form_values['to_page'] !=$page) {
			$to_page = $form_values['to_page'];
		} else {
			switch ($form_state->getValue('op')) {
				case BUTTON_LABEL_NEXT:
					switch ($page) {
						case PROJECT_PAGE_NAME_DEFINITION:
							$to_page = PROJECT_PAGE_NAME_GOALS;
						break;
						case PROJECT_PAGE_NAME_GOALS:
							$to_page = PROJECT_PAGE_NAME_SCOPE;
						break;
						case PROJECT_PAGE_NAME_SCOPE:
							$to_page = PROJECT_PAGE_NAME_CONCEPTS;
						break;
					}
				break;
				case BUTTON_LABEL_BACK:
					switch ($page) {
						case PROJECT_PAGE_NAME_GOALS:
							$to_page = PROJECT_PAGE_NAME_DEFINITION ;
						break;
						case PROJECT_PAGE_NAME_SCOPE :
							$to_page = PROJECT_PAGE_NAME_GOALS;
						break;
						case PROJECT_PAGE_NAME_CONCEPTS:
							$to_page = PROJECT_PAGE_NAME_SCOPE ;
						break;
					}
				break;
				case 'Add first concept':
					$goto = $base_path+URL_CONCEPT_ADD.'/'.$productID;
				break;
				default:
					$goto = $base_path+URL_PROJECT_VIEW.'/'.$productID;
				break;
			}
		}
		
		if ($to_page) {
			$goto = $base_path.URL_PROJECT_ADD . '/'.$productID . '/'. $to_page;
		} else {
			// set project to final
			// $db->complete_product($productID); 
		}
	}
  $form_state->setRedirect('setup_project.project',['pid'=>$productID,'page'=>$to_page]);
	// drupal_goto($goto);
  }}  
}  
