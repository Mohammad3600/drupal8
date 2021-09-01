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
include_once(dirname(__FILE__).'\..\..\products.inc');

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
	
		$default_values = array('title' => $concept['name'], 'description' => $concept['description'], 'icon'=> $concept['icon'], 'lifetimefuncunits'=> $concept['lifetimefuncunits'], 'funcunitnote'=>$concept['funcunitnote']);
		//select all product info and put it in $edit
		$bradtitle = 'Editing '.$default_values['title'];
		$product = $db->get_product($concept['productID']);
	} elseif ($mode == 'add' && is_numeric($pid)) {
		$is_add= true;
		$default_values = array('title' => null, 'description' => null, 'icon'=> null, 'lifetimefuncunits'=> null, 'funcunitnote'=> null);
		$bradtitle = 'Create a concept';
		$product = $db->get_product($pid);
	} else {
		$form_state->setErrorByName('title',t('The concept you are looking for does not exist.'));
		return '';
	}
	
	if (!$product) {
		$form_state->setErrorByName('title',t('The project you are looking for does not exist.'));
		return '';
	}

  $concept_path = '<div class="position-absolute top-9"><a href ='.SITE_PATH.'/'.URL_PROJECT_VIEW.'/'. $pid .'>'.$product['name'].'</a> > <a href ='.SITE_PATH.'/'.URL_PROJECT_CONCEPTS.'/'. $pid .'>Concepts</a> > <strong>Create a concept</strong></div>';
	// $form = array();
	// allows file upload
	$form['#attributes'] = array(
    'enctype' => "multipart/form-data",
    'class' => array('project_setup_form'),
  );
  $form['navigation'] = array(
    '#markup' => $concept_path
  );
  $form['mode'] = array(
		'#type'=>'hidden',
		'#attributes' => array('id' => 'Product-Mode'),
		'#value'=>$mode
	);
	$form['productID'] = array(
		'#type'=>'hidden',
		'#value'=>$pid,
	);
	$form['title'] = array(
    '#twig_suggestion' => 'concept-fields',
		'#type' => 'textfield',
		'#title' => t('Concept name'),
		'#maxlength' => 255,
		'#default_value' =>$default_values['title'] ,
		'#star' => TRUE,
	);
	
	//Custon file upload field
	/*$form['image'] = array(
    '#twig_suggestion' => 'concept-fields',
		'#type' => 'sustainable_minds_file_upload',
		'#use_title' => 'Image or rendering',
		'#process' => array('_sustainable_minds_file_upload'=>array()),
	);*/
	//
/*	$form['file'] = array(
  '#twig_suggestion' => 'concept-fields',
		'#title' => 'Image or rendering',
		'#type' => 'sustainable_minds_multi_file_upload',
		'#default_value'=>$default_values['image'],
		//'#process' => array('_sustainable_minds_file_upload'=>array()),
		'#process' => array('_sustainable_minds_multi_file_upload'=>array())
	);*/
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
	// $form['file'] = array(
  //  '#twig_suggestion' => 'concept-fields',
	// 	'#title' => 'Image or rendering',
	// 	'#type' => 'uploadify_uploader',
	// 	'#default_value'=> $default_values['icon'] ? $default_values['icon'] : DEFAULT_IMAGE,
	// );
	// $form['upload_help'] =array(
	// 	'#type'=>'markup',
	// 	'#markup'=>'<div class="gl-upload-help">Image format: gif, jpg, png<br />Max width: 670px</div>',
	// );	
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
	/*
	$form['funcunit'] = array(
    '#twig_suggestion' => 'concept-fields',
		'#type' => 'textfield',
		'#title' => t('Hours of use per year'),
		'#maxlength' => 6,
		'#size' => 10,
		'#default_value' => $default_values['funcunits'],
		'#field_suffix' => t("Hrs/Year"),
		'#star' => TRUE,
	);
	
	*/
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
	
	/*
	$form['lifetime'] = array(
    '#twig_suggestion' => 'concept-fields',
		'#type' => 'textfield',
		'#title' => t('Concept lifetime'),
		'#maxlength' => 6,
		'#size' => 10,
		'#default_value' => $default_values['lifetime'],
		'#field_suffix' => t("Years"),
		'#star' => TRUE,
	);
	*/
	$form[] = setup_project_form_open_div('form-button-actions mb-5 form-button-actions form-group d-flex justify-content-between');

	// $form[] = setup_project_form_open_div('general-action  actions-right');
	
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
	// '#attributes' => array('class' => '', 'onclick' =>$click)  );  //what is $click?
	// $form[] = setup_project_form_close_div();
	
	$form[] = setup_project_form_close_div();
	
	//FIXME not using the general breadcrumb function.
	// $links = array(
	// 		l(t($product['name']), URL_PROJECT_VIEW . '/'.$product['productID']),
	// 		l(t('Concepts'), URL_PROJECT_CONCEPTS .'/'.$product['productID']),
	// 		t(t($bradtitle))
	// 	);
	
	// drupal_set_breadcrumb($links);
	$form['#multistep'] = TRUE;
	$form['#redirect'] = FALSE;
	return $form;  
  }

  public function handle_import_validate_file_upload(&$element, FormStateInterface $form_state, &$complete_form) {
		$validators = [
		  'file_validate_extensions' => ['png jpeg jpg gif'],
		];	
		if ($file = file_save_upload('img_upload', $validators, FALSE, 0, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE)) {
		  $img_dir = 'public://2021-07/';
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
		}}
    
  public function validateForm(array &$form, FormStateInterface $form_state)
	{
	$image = $form_state->getValue('img_upload');
	$file = file_load( $image );
	$file->status = 1;
	
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
      // if ($form_state->getValue('mode') == 'edit')
      //   drupal_goto(URL_CONCEPT_VIEW .'/'. $form_state->getValue('pid'));
      // elseif ($form_state->getValue('mode') == 'add')
      //   drupal_goto(URL_PROJECT_CONCEPTS .'/'. $form_state->getValue('pid'));
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
        // $op= $form_state->getValue('op');
        // $conceptid = sustainable_minds_sbom_get_concept_arg();
        // $db->update_concept($conceptid, $form_state->getValues());
        // $goto = URL_CONCEPT_VIEW .'/'.$conceptid;
      }
      // drupal_goto($goto);
    }
  }  
}  
