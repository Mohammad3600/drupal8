<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\ProjectForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormState;
use Drupal\file\Entity\File;
include_once(dirname(__FILE__).'\..\..\init.inc');
include_once(dirname(__FILE__).'\..\..\products.inc');

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
   * Creates Setup project Form  
   */  
	public function buildForm(array $form, FormStateInterface $form_state, $mode=null, $pid=null, $page=null) { 
		$form_state->disableCache();
		return sustainable_minds_product_edit($pid, $form_state, $page, $mode);
	}

	public function validateForm(array &$form, FormStateInterface $form_state)
	{
		//save uploaded file
		$file = file_save_upload('img_upload', array(), FALSE, 0, 'rename');
		$file_data = $form_state->getValue(['img_upload']);
		// $file = \Drupal\file\Entity\File::load( $file_data[0] );
		// $file_name = $file->getFilename();
		// $file->setPermanent();
		// $file->save();
		// $uploaddir = '/drupal8/web/sites/default/files/2021-07/';
		// $uploadfile = $uploaddir . basename($_FILES['img_upload']['name']);
		// $t = move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadfile);
		// $form_state->set('icon',$uploadfile);
		// $y = $form_state->getValue('icon');
		$mode = $form_state->getValue('mode');
		$page = $form_state->getValue('page');
		$productID = $form_state->getValue('pid');
		switch ($form_state->getValue('page')) {
		case PROJECT_PAGE_NAME_DEFINITION:
			if (!$form_state->getValue('name')) {
			$form_state->setErrorByName('name',t(TEXT_ERROR_PROJECT_NAME));
			}
		break;
		case PROJECT_PAGE_NAME_SCOPE:
			if (!$form_state->getValue('funame')) {
			$form_state->setErrorByName('funame', t(TEXT_ERROR_PROJECT_FUNAME));
			}
		break;
		}
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {  
		$mode = $form_state->getValue('mode');
		$page = $form_state->getValue('page');
		$productID = $form_state->getValue('pid');
		//update product
		if($form_state->getValue('op') == BUTTON_LABEL_CANCEL)
		{
			if($mode == 'edit'){
				$page = $page=='definition'? $page : 'view';
				$page = $page=='goals'? $page : 'goal';
				$form_state->setRedirect('setup_project.viewProject',['page'=>$page, 'pid'=>$productID]);
			}else{
				$form_state->setRedirect('setup_project.projectMarkup');
			}
		}
		else{
		$db = \Drupal::service('setup_project.sbom_db');
		$product = $db->get_product($productID);
		foreach ($product as $key=>$value) {
			if ($form_state->getValue($key)!==null)
				$product[$key] = $form_state->getValue($key);
		} 
		$base_path = '/drupal8/web/';
		$pname = $product['name'];
		if ($pname && $product['funame'] && $form_state->getValue('op') != 'Cancel') {
		$db->update_product($productID, $product);
		if ($mode == 'edit' ) {
			$page = $page=='definition'? $page : 'view';
			$page = $page=='goals'? $page : 'goal';
			$form_state->setRedirect('setup_project.viewProject',['page'=>$page, 'pid'=>$productID]);
		} else {
			if ($form_state->getValue('to_page') && $form_state->getValue('to_page') !=$page) {
				$to_page = $form_state->getValue('to_page');
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
						$goto = $base_path . URL_CONCEPT_ADD.'/'.$productID;
					break;
					default:
						$goto = $base_path . URL_PROJECT_VIEW.'/'.$productID;
					break;
				}
			}
			
			if ($to_page) {
				$goto = $base_path.URL_PROJECT_ADD . '/'.$productID . '/'. $to_page;
				$form_state->setRedirect('setup_project.project',['mode'=>$mode,'pid'=>$productID,'page'=>$to_page]);
			} else {
				// set project to final
				$db->complete_product($productID); 
				$form_state->setRedirect('setup_project.projectMarkup');
			}
		}
	}}
		// drupal_goto($goto);
	}
}  