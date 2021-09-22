<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\CreateConcept.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
include_once(dirname(__FILE__).'\..\..\init.inc');

class CreateConcept extends FormBase {  
	/**  
	 * {@inheritdoc}  
	 */  
	protected function getEditableConfigNames() {  
		return [  
			'setup_project.create_concept',  
		];  
	}  

	/**  
	 * {@inheritdoc}  
	 */  
	public function getFormId() {  
		return 'create_concept';  
	}  
	/**  
	 * {@inheritdoc}  
	 */  
	public function buildForm(array $form, FormStateInterface $form_state, $mode=null, $pid=null) {  
		//If this is an edit, get all product information to populate the fields with.
		$db = \Drupal::service('setup_project.sbom_db');
		$current_user = \Drupal::currentUser();
		$user = \Drupal\user\Entity\User::load($current_user->id());
		if ($mode == "edit" && $pid > 0) {
			$is_add = false;
			// FIXME Check if the ID is a valid entry
			$concept = $db->get_concept($pid);
			if (!$concept) {
				$form_state->setErrorByName('title',t('The concept you are looking for does not exist.'));
				return '';
			}
			$form['conceptID'] = array(
				'#type'=>'hidden',
				'#value'=>$pid,
			);
			$productid = $concept['productID'];
			$default_values = array('title' => $concept['name'], 'description' => $concept['description'], 'icon'=> $concept['icon'], 'lifetimefuncunits'=> $concept['lifetimefuncunits'], 'funcunitnote'=>$concept['funcunitnote']);
			//select all product info and put it in $edit
			$form['productID'] = array(
				'#type'=>'hidden',
				'#value'=>$productid,
			);
			$bradtitle = 'Editing '.$default_values['title'];
			$product = $db->get_product($concept['productID']);
		} elseif ($mode == 'add' && is_numeric($pid)) {
			$is_add= true;
			$default_values = array('title' => null, 'description' => null, 'icon'=> null, 'lifetimefuncunits'=> null, 'funcunitnote'=> null);
			$bradtitle = 'Create a concept';
			$productid = $pid;
			$product = $db->get_product($pid);
			$form['productID'] = array(
				'#type'=>'hidden',
				'#value'=>$pid,
			);
			} else {
				$form_state->setErrorByName('title',t('The concept you are looking for does not exist.'));
				return '';
			}
			if (!$product) {
				$form_state->setErrorByName('title',t('The project you are looking for does not exist.'));
				return '';
			}
		$concept_breadcrumb = \Drupal::service('setup_project.utilities')->breadcrumb(['<a href ='.SITE_PATH.'/'.URL_PROJECT_VIEW.'/'. $productid .'>'.$product['name'].'</a>', '<a href ='.SITE_PATH.'/'.URL_PROJECT_CONCEPTS.'/'. $productid .'>Concepts</a>'], $bradtitle);
		$form['#attributes'] = array(
		'enctype' => "multipart/form-data",
		'class' => array('project_setup_form'),
		);
		$form['navigation'] = array(
			'#markup' => $concept_breadcrumb
		);
		$form['mode'] = array(
				'#type'=>'hidden',
				'#attributes' => array('id' => 'Product-Mode'),
				'#value'=>$mode
		);
		$form['title'] = array(
		'#twig_suggestion' => 'concept-fields',
			'#type' => 'textfield',
			'#title' => t('Concept name'),
			'#maxlength' => 255,
			'#default_value' =>$default_values['title'] ,
			'#star' => TRUE,
		);	
		$imagePath = $default_values['icon'] ? $default_values['icon'] : (SITE_PATH.'/sites/default/files/2021-07/no_scope.gif');
		$form['defaultImgPath'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#type' => 'hidden',
			'#attributes' => [
				'id' => 'defaultPath',
			],
			'#default_value' => SITE_PATH.'/web/sites/default/files/2021-07/no_scope.gif',
		);
		$form['icon'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#type' => 'hidden',
			'#attributes' => [
				'id' => 'Icon'
			],
			'#default_value' => $imagePath,
		);
					
		$form['img_upload'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#type' => 'file',
			'#element_validate' => ['::handle_import_validate_file_upload'],
			'#attributes' => [
				'class' => array('d-none'),
				'id' => 'edit-img-upload-upload'
			],
		);
		$form['image_wrapper'] = [
				'#markup' => '<div class="mb-3 form-group noscope_upload">
				<div class="change-text">Image or rendering</div>
				<img id="ShowImage" src="'.$imagePath.'" class="mb-2" alt="scope image" title="system upload">
				<ul class="d-flex mb-2">
					<li><a id="Default-img" class="btn">Use default image</a></li>
					<li><a id="Browse-img" class="btn">Browser</a></li>
				</ul>
				<div class="gl-upload-help">Image format: gif, jpg, png<br>Max width: 670px</div>
			</div>'
		];
		$form['description'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#type' => 'textarea',
			'#attributes' => [
				'class' => array('w-100'),
			],
			'#title' => t('Concept description'), 
			'#rows' => 6,
			'#default_value' => $default_values['description'],
			'#required' => FALSE,
			'#description' => t("Include what is distinctive about this concept along with ecodesign strategies being explored.<br /> <a href='JavaScript:void(0);' onclick='return node_popup(\"/helpview/19\",\"Help - Ecodesign strategies >\")'>View ecodesign strategy wheel ></a>"),
		);
		$form['lifetimefuncunits'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#type' => 'textfield',
			'#title' => t('Total amount of service delivered'),
			'#maxlength' => 6,
			'#size' => 20,
			'#default_value' => $default_values['lifetimefuncunits'],
			'#field_suffix' => t('<strong> X '. $product['funame'].' (functional unit)</strong><div id="fudesc">'.$product['fudesc'].'</div>'),
			//'#field_suffix' => t("Units of ".$product['funame'].' (functional unit)'),
			'#star' => TRUE,
			'#description' => '<em><strong>Example:</strong> the product concept is designed for 10 years of use, and the functional unit is 1 year of use (1x10 = 10), enter 10 as the amount of service delivered.</em>',
			//'#suffix' => $product['fudesc']
		);
		$form['funcunitnote'] = array(
			'#twig_suggestion' => 'concept-fields',
			'#attributes' => [
				'class' => array('w-100'),
			],
			'#type' => 'textarea', 
			'#prefix' => '<div class="no-top-border">',
			'#rows' => 6,
			'#default_value' => $default_values['funcunitnote'],
			'#required' => FALSE,
			'#description' => t("<div class='change-text'>Describe your rationale or calculations used to estimate the amount of service delivered.</div>"),
			'#suffix' => "</div>
			<!-- <a href='JavaScript:void(0);' onclick='return node_popup(\"/helpview/33\",\"Help - Total service delivered >\")'>Learn more about total service delivered. ></a> -->"
		);	
		$form[] = setup_project_form_open_div('form-button-actions mb-5 form-button-actions form-group d-flex justify-content-between');
		$form['editsbom'] = array(
		'#twig_suggestion' => 'concept-fields',
			'#type' => 'submit',
			'#value' => BUTTON_LABEL_SAVE,
		);
		$form['cancel'] = array(
			'#twig_suggestion' => 'concept-fields',
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
		);
		$form[] = setup_project_form_close_div();
		$form['#multistep'] = TRUE;
		$form['#redirect'] = FALSE;
		return $form;  
	}

  	public function handle_import_validate_file_upload(&$element, FormStateInterface $form_state, &$complete_form) {
		$validators = [
		  'file_validate_extensions' => ['png jpeg jpg gif'],
		];	
		$userid = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id())->get('uid')->value;
		if ($file = file_save_upload('img_upload', $validators, FALSE, 0, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE)) {
			$img_dir = 'public://u'.$userid.'/';
			$file->status = FILE_STATUS_PERMANENT;
			$directory_exists = \Drupal::service('file_system')->prepareDirectory($img_dir);
			if(!$directory_exists){
				\Drupal::service('file_system')->prepareDirectory($img_dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
			}
			if ($directory_exists) {
				$destination = $img_dir . '/' . $file->getFilename();
				if (file_copy($file, $destination, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE)) {
				$form_state->setValue('img_upload', $destination);
				$form_state->setValue('icon', file_create_url($destination));
				}
				else {
				$form_state->setErrorByName('img_upload', t('Unable to copy upload file to @dest', ['@dest' => $destination]));
				}
		  	}
		}
	}
    
  	public function validateForm(array &$form, FormStateInterface $form_state){
		if ($form_state->getValue('op') != BUTTON_LABEL_CANCEL) {
		if (!$form_state->getValue('title')) {
			$form_state->setErrorByName('title', t(TEXT_ERROR_CONCEPT_NAME));
		}
		if ($form_state->getValue('lifetimefuncunits')=='') {
			$form_state->setErrorByName('lifetimefuncunits', t(TEXT_ERROR_CONCEPT_FUNCUNIT));
		} elseif (!is_numeric($form_state->getValue('lifetimefuncunits'))) {
			$form_state->setErrorByName('lifetimefuncunits', t(TEXT_ERROR_CONCEPT_NUMERIC_FUNCUNIT));
		} elseif ($form_state->getValue('lifetimefuncunits') == 0) {
			$form_state->setErrorByName('lifetimefuncunits', t(TEXT_ERROR_CONCEPT_FUNCUNIT_ZERO));
		} elseif (substr(trim($form_state->getValue('lifetimefuncunits')), 0, 1) == '-') {
			$form_state->setErrorByName('lifetimefuncunits', t(TEXT_ERROR_CONCEPT_FUNCUNIT_ZERO));
		}
		} else {
		if ($form_state->getValue('mode') == 'edit')
			$form_state->setRedirect('setup_project.viewConcept',['conceptid'=>$form_state->getValue('conceptID')]);
		elseif ($form_state->getValue('mode') == 'add')
			$form_state->setRedirect('setup_project.viewProject',['page'=>'concepts' , 'pid'=>$form_state->getValue($productID)]);
		}
	}
	
	/**  
	 * { To save the values of submitted form }  
	 */ 
	public function submitForm(array &$form, FormStateInterface $form_state) {  
		if ($form_state->getValue('op') != BUTTON_LABEL_CANCEL) {
		$db = \Drupal::service('setup_project.sbom_db');
		if ($form_state->getValue('mode') == 'add') {
			$productid = $form_state->getValue('pid'); 
			$lastid = $db->add_concept($form_state->getValues());
			$form_state->setRedirect('setup_project.viewConcept',['conceptid'=>$lastid]);
		} elseif($form_state->getValue('mode') == 'edit') {
			$conceptid = $form_state->getValue('conceptID');
			$db->update_concept((int)$conceptid, $form_state->getValues());
			$form_state->setRedirect('setup_project.viewConcept',['conceptid'=>$conceptid]);
		}
		}else{

		}
	}  
}  
