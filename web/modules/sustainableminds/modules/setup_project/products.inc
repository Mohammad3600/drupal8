<?php
/**
 * Add/Edit product form
 */
include(dirname(__FILE__).'\security.inc');
include(dirname(__FILE__).'\dataload.inc');
include(dirname(__FILE__).'\init.inc');

function sustainable_minds_project_new() {
	$db = \Drupal::service('setup_project.sbom_db');
	$id = $db->add_blank_product();
	echo $id ;
	exit();
}
function sustainable_minds_product_edit($pid = NULL, $form_values = NULL, $page=null) {
	// sustainable_minds_project_new();
	$is_add = true;  
    $form = array();
	// drupal_add_js(SM_PATH.'/form.js');
	//If this is an edit, get all product information to populate the fields with.
	
	// if (arg(1) == "edit" && $pid > 0) {
	// 	$is_add = false;
	// } else {
	// 	$is_add = true; 
	// }	
	// 	// FIXME Check if the ID is a valid entry
	// $page = arg(3) ;	
	$page = $page ? $page:PROJECT_PAGE_NAME_DEFINITION;

	$db = \Drupal::service('setup_project.sbom_db');
	// $product = $db->get_product($pid);
	
	$default_values = array(
	 'name'        => '' , 
	 'client'      => '' , 
	 'description' => '' , 
	 'pcategoryID' => '' , 
	 'version'     => '' , 
	 'icon'        => '' , 
	 'assessment'  => '' , 
	 'development' => '' , 
	 'exclusion'   => '' , 
	 'inclusion'   => '' , 
	 'system'      => '' , 
	 'productID'   => '' , 
	 'funame'      => null , 
	 'fudesc'      => null 
  );
			
  global $user;
//   $is_admin = false;
  $is_admin = true;
  if (in_array("superadmin", $user->roles) || in_array("lca data manager", $user->roles)) {
    $is_admin = true;
  }
  
  if (is_null($default_values['version'])) {
    if ($is_admin) {
      $status = 'any';
      $default_values['version'] = $db->get_latest_version($status);
      //watchdog('1', $default_values['version']);
    } else {
      $status = 'published';
      $default_values['version'] = $db->get_latest_version($status);
      //watchdog('2', $default_values['version']);
    }
  }
	
	// get all the product categories
	// db_set_active('sbom');
	// $result = db_query('CALL SM_SBOM_List_PCategories();');
	// while($row = db_fetch_array($result)){
	// 	$categories[$row['pcategoryID']] = $row['name'];
	// }
	// setup_project_clear_db($result);

	// db_set_active();
	$db = \Drupal::service('setup_project.sbom_db');
	$categories = $db -> getCategories();
	$form['#attributes'] = array('enctype' => "multipart/form-data");
	$form['page'] = array(
		'#type'=>'hidden',
		'#value'=>$page 
	);
	$form['pid'] = array(
		'#type'=>'hidden',
		'#value'=>$pid
	);
	$form['to_page'] = array(
		'#type'=>'hidden',
		'#default_value'=>'' 
	);
	$versions = array(
		'1.0' => 'Dataset 1.0',
		'2011' => 'Dataset SM 2011',
		'2013' => 'Dataset SM 2013',
	);
    $form['actions'] = [
        '#type' => 'actions',
      ];
	//Call the wizard_step function that initializes the multi-step form and returns the step the user is on.
	switch($page) {
		case PROJECT_PAGE_NAME_DEFINITION: //if on step 1:
			$form['name'] = array(
				'#type' => 'textfield',
				'#title' => t('Project name'),
				'#maxlength' => 255,
				'#default_value' => $default_values['name'],
				'#star'=>true,
				'#size' => 40
			);
			$form['client'] = array(
				'#type' => 'textfield',
				'#title' => t('Client or group'),
				'#maxlength' => 255,
				'#default_value' => $default_values['client'],
				'#required' => FALSE,
				'#size' => 40
			);
			$form['pcategoryID'] = array(
				'#type' => 'select',
				'#title' => 'Product category',
				'#options' => $categories,
				'#default_value' => $default_values['pcategoryID'],
			);
			$form['description'] = array(
				'#type' => 'textarea', 
				'#title' => t('Project description'), 
				'#rows' => 6,
				'#description'=>'Description information can include:<ul><li>Project background info</li><li>Product backround info</li><li>Summarized design brief</li><li>Ecodesign strategies to be explored.</li></ul>There is no character limit on this or subsequent free text fields.',
				'#default_value' => $default_values['description'],
				'#required' => FALSE,
				'#cols' => 30
			);
			
			if ($is_admin) {
  			$form['version'] = array(
  				'#type' => 'select',
  				'#title' => 'LCA Dataset Version',
  				// '#options' => $versions,
				'#options' => sustainable_minds_list_versions(),
  				'#default_value' => $default_values['version'],
  			);
			} else {
  			$form['version'] = array(
  				'#type' => 'hidden',
  				'#default_value' => $default_values['version'],
  			);
		}


			//set default FuncUnit info here in case user skips Assessment Scope step
			$form['funame'] = array(
				'#type' => 'hidden',
				'#maxlength' => 255,
				'#default_value' => ($default_values['funame']) ? $default_values['funame']:"1 year of use",
				'#size' => 100
			);
			$form['fudesc'] = array(
				'#type' => 'hidden',
				'#maxlength' => 10000,
				'#default_value' => ($default_values['fudesc']) ? $default_values['fudesc']:"Year of use is a standard unit of measure when service delivered is measured by time.",
				'#size' => 100
			);
			
			$form[] = setup_project_form_open_div('form-button-actions');	
			$form[] = setup_project_form_open_div('general-action actions-right');
			if (!$is_add) {
				$form['actions']['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE_EXIT,
				);
			} else {
				$form['actions']['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_NEXT,
					// '#submit' => array('sustainable_minds_product_edit_submit')
				);
			}
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_open_div('general-action');
			$form['actions'][] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_close_div();
			break;
		
		case PROJECT_PAGE_NAME_GOALS:
			//Custom function for creating checboxes - drupal checkboxes elements don't support multi-page forms.
			//setup_project_checkboxes($form, 'phases', $phases, setup_project_element_default('phases', $form_values), 'Lifecycle Phase');
			$form['development'] = array(
				'#type' => 'textarea', 
				'#title' => t('Company goals and environmental policies'), 
				'#rows' => 6,
				'#description'=>'',
				'#default_value' => $default_values['development'],
				'#required' => FALSE,
			);
			$form['assessment'] = array(
				'#type' => 'textarea', 
				'#title' => t('Project assessment goals'), 
				'#rows' => 6,
				'#description'=>'Why are you conducting this assessment - what do you hope to learn? <br/><br/><em>Examples: <ul><li>Understand which ecodesign strategies result in the greatest environmental performance improvement</li><li>How to Increase environmental performance by at least 20% from the 2008 version (the reference)</li><li>Determine how the results support corporate goals to begin to better define product-level goals</li></ul></em>',
				'#default_value' => $default_values['assessment'],
				'#required' => FALSE,
			);
			
			$form[] = setup_project_form_open_div('form-button-actions');
			
			$form[] = setup_project_form_open_div('general-action actions-right');
			if (!$is_add) {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE_EXIT,
				);
			} else {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_NEXT,
				);
				$form[] = setup_project_form_close_div();
				$form[] = setup_project_form_open_div('general-action actions-right');
				$form['back'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_BACK,
				);
			}
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_open_div('general-action');
			$form[] = array(
				'#type' => 'button',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_close_div();
			
			break;
		
		case PROJECT_PAGE_NAME_SCOPE:
			// create name, delete buttons
			$form['func-explanation_hder'] = array(
				'#markup' => '<div class="form-item group-title"><label class="form-label">Functional unit</label></div>'
            );

            $form['funame'] = array(
				'#type' => 'hidden',
				'#maxlength' => 255,
				'#default_value' => ($default_values['funame']) ? $default_values['funame']:"1 year of use",
				'#size' => 100
			);
			$form['fudesc'] = array(
				'#type' => 'hidden',
				'#maxlength' => 10000,
				'#default_value' => ($default_values['fudesc']) ? $default_values['fudesc']:"Year of use is a standard unit of measure when service delivered is measured by time.",
				'#size' => 100
			);

			//fades out when clicking $change_button; fades in on cancel
			if(!$product['numConcepts'])
				$change_button = '<a id="product-functional-unit-change-button" class="btn btn-success btn-sm mb-2">Change</a>';
			else
				$change_button = '';

			$form['func-change'] =array(
				'#markup'=>'<div id="product-functional-unit-change">
				<div id="product-functional-unit-value-container"><span id="product-functional-unit-value"></span><span id="product-functional-unit-value-note"></span></div>
				<div id="product-functional-unit-desc"></div>'.$change_button.'</div>',
			);
			
			//fades in when clicking $change_button; fades out on cancel
			$form['open-product-functional-unit-edit'] =array(
				'#markup'=>'<div id="product-functional-unit-edit" class="functional_explain">',
			);

			$form['func-define-title'] =array(
				'markup'=>'<div class="form-item no-top-border"><label class="form-label">Change the functional unit for this product.</label></div>',
			);
			
			$form['product-functional-unit-edit-value'] = array(
				'#type' => 'textfield', 
				'#prefix' => '<div class="form-item no-top-border">',
				'#title' => 'Impacts per ',
				'#size' => 60,
				'#star'=>true,
				'#suffix' => '</div>'
			);
		
			$form['product-functional-unit-edit-desc'] = array(
				'#type' => 'textarea', 
				'#prefix' => '<div class="form-item no-top-border">',
				'#description' => 'Add a note describing why this functional unit was selected. This description will be displayed in each concept as a reminder.<br /><em>Example: Year of use is a standard unit of measure when service delivered is measured by time.</em>',
				'#attributes' => array('class' => 'form-textarea resizable'),
				'#default_value' => $default_values['product-functional-unit-edit-desc'],
				'#cols' => 60,
				'#rows' => 2,
				'#required' => FALSE,
				'#suffix' => '</div>'
			);
			
			$form['func-buttons'] = array(
				 '#markup' => '<a id="product-functional-unit-edit-cancel" class="btn btn-success btn-sm mb-2">Cancel</a>
				 <a id="product-functional-unit-edit-save" class="btn btn-success btn-sm mb-2">Update default</a>
                <div class="clear"></div>',
				'#suffix' => '</div>'
			);

			$form['close-product-functional-unit-edit'] =array(
				'#markup'=>'</div>',
			);
			if(!$product['numConcepts']) {
				$form['func-note'] =array(
					'#markup'=>'<div class="product-functional-unit-warning">Note: upon completing the project setup, the functional unit can no longer be changed.</div>',
				);
			}

			$form['func-explanation'] = array(
				'#markup' => '<div id="product-functional-unit-explanation">
        <div class="functional_explain">
            <p>
                <strong>The functional unit describes the service <em>the product delivers</em> to the end user.</strong> 
                <span class="d-block">It is used to normalize assessment results and enables your concepts to be compared. Results are displayed in &#39;Impacts per [your functional unit]&#39;.</span>
            </p>
            <p>
                <strong>
                    Choose a functional unit relevant to the entire intended service life.
                </strong>
                <span class="d-block">Example: If the product is intended to last 10 years, the functional unit should be years, not days.</span>
            </p>
            <p>
                <strong> When creating concepts, you will specify the <em>total amount of service delivered</em> (TASD) for each concept over its lifetime. </strong>
                <span>Example for a household appliance: functional unit of 1 year of use and a product concept with a TASD of 5 years.</span>
            </p>
        </div>
        <hr class="mb-2">
    </div>'
			// trying to force a break here. This makes the divider visible again.
			);
			
			// end of the copied "functional unit" code
			
			
			$form['product-system'] = array(
				'#markup' => '<div class="form-item group-title"><label class="form-label">Product system</label></div>'
				);
			$form['system'] = array(
				'#type' => 'textarea', 
				//'#prefix' => '<div class="no-top-border">',
				'#description' => 'Describe the product system and system boundaries.',
				'#rows' => 4,
				'#default_value' => $default_values['system'],
				//'#suffix' => t("</div>"),
				'#required' => FALSE,
			);/*
			
			$form['icon'] = array(
				'#type' => 'setup_project_multi_file_upload',
				'#prefix' => '<div class="form-item no-top-border">',
				'#description' => 'If you have a visual representation or description of the product system, upload it here.',
				'#default_value'=>$default_values['icon'],
				'#process' => array('_setup_project_multi_file_upload'=>array()),
				'#suffix' => t("</div>")
			);
*/
			/*
			$form['icon'] = array(
				'#prefix' => '<div class="form-item"><label class="form-label" for="edit-image">Product system</label><div class="product-title-image-desc">If you have a visual representation or description of the product system, upload it here.</div>',
				'#type' => 'setup_project_multi_file_upload',
				'#default_value'=>$default_values['icon'],
				'#process' => array('_setup_project_multi_file_upload'=>array()),
				'#suffix' => t("</div>")
			);*/
			$form['image_wrapper'] = [
				'#markup' => '<div class="mb-3 form-group noscope_upload">
				<label for="edit-system" class="form-label">If you have an image that illustrates the product system, upload it here.</label><br>
				<img src="/drupal8/web/sites/default/files/2021-07/no_scope.gif" class="mb-2" alt="scope image" title="system upload">
				<ul class="d-flex mb-2">
					<li><a href="#" class="btn">Use default image</a></li>
					<li><a href="#" class="btn">Browser</a></li>
				</ul>
				<div class="gl-upload-help">Image format: gif, jpg, png<br>Max width: 670px</div>
			</div>'
			];
			$form['file'] = array(
				'#prefix' => '<div class="form-item no-top-border no-top-pad no-bottom-margin">',
				'#description' => 'If you have an image that illustrates the product system, upload it here.',
				'#type' => 'uploadify_uploader',
				'#default_value'=> $default_values['icon'] ? $default_values['icon'] : DEFAULT_SCOPE_IMAGE,
				'#suffix' => t("</div>")
			);
			$form['upload_help'] =array(
				'#type'=>'markup',
				'#value'=>'<div class="gl-upload-help">Image format: gif, jpg, png<br />Max width: 670px</div>',
			);
			$form['exclusion'] = array(
				'#type' => 'textarea',
				//'#title' => t(''), 
				'#prefix' => '<div class="form-item no-top-border">',
				'#description' => 'List product system exclusions. <p class="forms-description"><em>Example: If you\'re designing a lighting fixture and DO NOT intend to include the bulb, indicate that here.</em></p>',
				'#default_value' => $default_values['exclusion'],
				'#attributes' => array('class' => 'form-textarea resizable'),
				'#cols' => 60,
				'#rows' => 3,
				'#required' => FALSE,
				'#suffix' => t("</div>")
			);


			$form[] = setup_project_form_open_div('form-button-actions');
			$form[] = setup_project_form_open_div('general-action actions-right');
			if (!$is_add) {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE_EXIT,
				);				
			} else {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_NEXT,
				);
				$form[] = setup_project_form_close_div();
				$form[] = setup_project_form_open_div('general-action actions-right');
				$form['back'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_BACK,
				);
			}
			$form[] = setup_project_form_close_div();

			$form[] = setup_project_form_open_div('general-action');
			$form[] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_close_div();
			break;
		
		case PROJECT_PAGE_NAME_CONCEPTS:
			$form[] = setup_project_form_open_div('project-complete');
			$form['func'] = array(
				'#markup' => '<h2>Project setup complete!</h2>
                <h2>You can now create concepts for assessment.</h2><br>
                The first concept created will automatically become the <b>reference concept</b> for this project. The reference concept is  the baseline to which subsequent concepts are compared. Once you have created more than one concept for this project, you can designate a different concept as the reference. 
                '
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_open_div('form-button-actions');
				
			$form[] = setup_project_form_open_div('general-action  actions-right');
			$form['concept'] = array(
				'#type' => 'submit',
				'#value' => 'Add first concept',
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_open_div('general-action');
			$form[] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form[] = setup_project_form_close_div();
			
			$form[] = setup_project_form_open_div('general-action  actions-right');
			$form['submit'] = array(
				'#type' => 'submit',
				'#value' => 'No thanks, I\'ll create concepts later',
			);
			$form[] = setup_project_form_close_div();
			
			$form[] = setup_project_form_open_div('general-action actions-right');
			$form['back'] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_BACK,
			);
			$form[] = setup_project_form_close_div();
			
			$form[] = setup_project_form_close_div();
			break;
	}
	
	$form['#multistep'] = TRUE;
 	$form['#redirect'] = FALSE;
 	
 	//set breadcrumb
 	// if(arg(1)=='add'){
 	// 	//$links = array(l(t('Projects'), URL_PROJECT_LIST_USER), t('Create a new project'));
 	// 	$links = array(t('Set up a Project'));	
 	// }else{
 	// 	//$links = array(l(t('Projects'), URL_PROJECT_LIST_USER), t('Edit '.$default_values['title']));
 	//   	$links = array(t('Edit '.$default_values['name'] .'\'s '. $page));
 	// }
	
	// drupal_set_breadcrumb($links);
 	
	return $form ;
}
function sustainable_minds_product_edit_submit(&$form, $form_state) {
   /*$final_step = 4; //set final step - dont submit unless you are submitting the last step.
	if ($form_values['step'] != $final_step
		&& !(arg(1) == 'edit' && $_SESSION['form_values']['edit_page'])
	) return;
	
	sustainable_minds_session_merge($form_values);*/
	
	//FIXME set the icon to the last in the array
	$page = $form_values['page'];
	/*
	if ($page==PROJECT_PAGE_NAME_SCOPE) {
		$current_files = sustainable_minds_edit_multi_image_final($form_values, '/');
		$form_values['icon'] = $current_files['first'];
		if (!$form_values['icon']) 
			$form_values['icon'] = '';
	}
	*/
	// $productID = sustainable_minds_arg_set(2);
    $productID ='';
	//update product
	$db = \Drupal::service('setup_project.sbom_db');
	// $product = $db->get_product($productID);
	// foreach ($product as $key=>$value) {
	// 	if (isset($form_values[$key])) 
	// 		$product[$key] = $form_values[$key];
	// } 

	// $db->update_product($productID, $product);
	
	// if (arg(1) == 'edit' ) {
	// 	switch ($page) {
	// 		case PROJECT_PAGE_NAME_GOALS:
	// 			$goto = URL_PROJECT_GOAL;
	// 			break ;
	// 		case PROJECT_PAGE_NAME_SCOPE:
	// 			$goto = URL_PROJECT_SCOPE;
	// 			break;
	// 		default:
	// 			$goto = URL_PROJECT_VIEW;
	// 			break;
	// 	}
	// 	$goto.='/'.$productID;
	// } else {
	// 	if ($form_values['to_page'] && $form_values['to_page'] !=$page) {
	// 		$to_page = $form_values['to_page'];
	// 	} else {
	// 		switch ($form_values['op']) {
	// 			case BUTTON_LABEL_NEXT:
	// 				switch ($page) {
	// 					case PROJECT_PAGE_NAME_DEFINITION:
	// 						$to_page = PROJECT_PAGE_NAME_GOALS;
	// 					break;
	// 					case PROJECT_PAGE_NAME_GOALS:
	// 						$to_page = PROJECT_PAGE_NAME_SCOPE;
	// 					break;
	// 					case PROJECT_PAGE_NAME_SCOPE:
	// 						$to_page = PROJECT_PAGE_NAME_CONCEPTS;
	// 					break;
	// 				}
	// 			break;
	// 			case BUTTON_LABEL_BACK:
	// 				switch ($page) {
	// 					case PROJECT_PAGE_NAME_GOALS:
	// 						$to_page = PROJECT_PAGE_NAME_DEFINITION ;
	// 					break;
	// 					case PROJECT_PAGE_NAME_SCOPE :
	// 						$to_page = PROJECT_PAGE_NAME_GOALS;
	// 					break;
	// 					case PROJECT_PAGE_NAME_CONCEPTS:
	// 						$to_page = PROJECT_PAGE_NAME_SCOPE ;
	// 					break;
	// 				}
	// 			break;
	// 			case 'Add first concept':
	// 				$goto = URL_CONCEPT_ADD.'/'.$productID;
	// 			break;
	// 			default:
	// 				$goto = URL_PROJECT_VIEW.'/'.$productID;
	// 			break;
	// 		}
	// 	}
		
	// 	if ($to_page) {
	// 		$goto =URL_PROJECT_ADD . '/'.$productID . '/'. $to_page;
	// 	} else {
	// 		// set project to final
	// 		$db->complete_product($productID); 
	// 	}
	// }
	
	// drupal_goto($goto);
}
