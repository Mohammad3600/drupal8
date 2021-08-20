<?php
include_once(dirname(__FILE__).'\dataload.inc');
include_once(dirname(__FILE__).'\init.inc');


/**
 * View Product
 */
function sustainable_minds_product_edit_tab($page, $productid) {
	$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	$is_admin = false;
	if (in_array("superadmin", $user->getRoles()) || in_array("lca data manager", $user->getRoles())) {
	  $is_admin = true;
	}
  
	//   $productid = sustainable_minds_arg_set(2);
	//   $page = arg(1);
	  $class="project-edit-block";
	  $db = \Drupal::service('setup_project.sbom_db');
	  $version = $db->get_version_for_project($productid);
	  switch($page) {
		  case 'view':
			// determine whether to show option to update project  
			if ($is_admin) {
			  $status = 'any';
			  $latest_version = $db->get_latest_version($status);
			} else {
			  $status = 'published';
			  $latest_version = $db->get_latest_version($status);
			}
		  
			$version_map_exists = $db->find_version_map($version, $latest_version);
  
			$page = PROJECT_PAGE_NAME_DEFINITION;
			$img = '';
			$open = '<div class="project-edit-right-title"><h2 class="font_11">This project:</h2><ul class="d-flex flex-wrap">';
			$del = '<li><a onclick="delete_project('.$productid.');return false;" />Delete</a></li>';
			$copy = '<li><a onclick="copy_project('.$productid.');return false;" />Copy</a></li>';
			if ($version_map_exists) {
				  $copyupdate = '<li><a onclick="copy_update_project('.$productid.',\''.$latest_version.'\');return false;" />Copy and Update</a></li>';
			  }
			$close='</ul></div>';
			  $img .= 'Edit Overview';
			  $class ="project-edit-tab";
			  break;
		  case 'goal':
			  $page = PROJECT_PAGE_NAME_GOALS;
			  $img .= 'Edit Assessment goal';
			  break;
		  case 'scope':
			  $page = PROJECT_PAGE_NAME_SCOPE;
			  $img .= 'Edit Assessment Scope';
			  break;
		  default:$page='';
	  }
	  if ($page) {
		  $edit = $open.'<span><li><a href="'.SITE_PATH.'/'.URL_PROJECT_EDIT.'/'.$productid.'/'.$page.'">'.$img.'</a></li></span>'.$del.$copy.$copyupdate.$close;
	  }
	  return '<div class="'.$class.'">'.$edit.'</div>';
  }

/**
 * Creates blank product
 */
function sustainable_minds_project_new() {
	$db = \Drupal::service('setup_project.sbom_db');
	$id = $db->add_blank_product();
	echo $id ;
	exit();
}

/**
 * returns user products
 */
function sustainable_minds_user_products() {
	$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	// $links = array('');
	// drupal_set_breadcrumb($links);
	//set userid here to argument to show other user's products
	$userid = $user->get('uid')->value;
	
	$output = '<div class="product-list-container">';
	
	$db = \Drupal::service('setup_project.sbom_db');
	$products = $db->list_products_by_user();
	// if ($user->uid == $userid || count($products)<1) {
		$output .= '<div class="my_project_leftcol">
        <div class="my_project_list d-flex flex-wrap justify-content-space-between align-items-center">
        <div class="col-lg-8">
            <h1 class="heading_6 mb-0">My Projects</h1>
        </div>
        <div class="col-lg-4 text-end" id="NewProject" onclick="new_project();">
            <a class="project_btn btn btn-success btn-sm">Set up a new Project <img src="/drupal8/web/sites/default/files/2021-07/plus.svg" alt="plus-icon" title="plus"> </a>
        </div>
        </div>';
	// }
	if(count($products)<1) {	
		$output .= sustainable_minds_product_empty();
	}
	elseif ($user->get('uid')->value == $userid && count($products)>0) {
		foreach($products as $product) {
			$output .= sustainable_minds_product_list($product);
		}
	}
	
	$output .= '</div></div>';
	
	return $output;
}

/**
 * Add/Edit product form
 */
function sustainable_minds_product_edit($pid = NULL, $form_values = NULL, $page=null, $mode=null) {
	// sustainable_minds_project_new();
	$is_add = true;  
    $form = array();
	// drupal_add_js(SM_PATH.'/form.js');
	//If this is an edit, get all product information to populate the fields with.
	
	if ($mode == "edit" && $pid > 0) {
		$is_add = false;
	} else {
		$is_add = true; 
	}	
	// 	// FIXME Check if the ID is a valid entry
	// $page = arg(3) ;	
	$page = $page ? $page:PROJECT_PAGE_NAME_DEFINITION;

	$db = \Drupal::service('setup_project.sbom_db');
	$product = $db->get_product($pid);
	
	$default_values = array(
	'name'        => $product['name'], 
	'client'      => $product['client'], 
	'description' => $product['description'], 
	'pcategoryID' => $product['pcategoryID'], 
	'version'     => $product['version'], 
	'icon'        => $product['icon'], 
	'assessment'  => $product['assessment'], 
	'development' => $product['development'], 
	'exclusion'   => $product['exclusion'], 
	'inclusion'   => $product['inclusion'], 
	'system'      => $product['system'], 
	'productID'   => $pid, 
	'funame'      => $product['funame'], 
	'fudesc'      => $product['fudesc']
  );
			
  $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());;
//   $is_admin = false;
  $is_admin = false;
  if (in_array("superadmin", $user->getRoles()) || in_array("lca data manager", $user->getRoles())) {
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
	$form['#attributes'] = array('enctype' => "multipart/form-data","class" => array("project_setup_form"));
	$form['page'] = array(
		'#type'=>'hidden',
		'#attributes' => array('id' => 'Product-Page'),
		'#value'=>$page 
	);
	$form['mode'] = array(
		'#type'=>'hidden',
		'#attributes' => array('id' => 'Product-Mode'),
		'#value'=>$mode
	);
	$form['pid'] = array(
		'#type'=>'hidden',
		'#value'=>$pid,
		'#attributes' => array('id' => 'Pid'),
	);
	$form['to_page'] = array(
		'#type'=>'hidden',
		'#attributes' => array('id' => 'edit-to-page'),
		'#default_value'=>'' 
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
				'#attributes' => array('class' => array('form-control')),
				'#star'=>true,
				'#size' => 40
			);
			$form['client'] = array(
				'#type' => 'textfield',
				'#title' => t('Client or group'),
				'#attributes' => array('class' => array('form-control')),
				'#maxlength' => 255,
				'#default_value' => $default_values['client'],
				'#required' => FALSE,
				'#size' => 40
			);
			$form['pcategoryID'] = array(
				'#type' => 'select',
				'#title' => 'Product category',
				'#attributes' => array('class' => array('form-control form-select')),
				'#options' => $categories,
				'#default_value' => $default_values['pcategoryID'],
			);
			$form['description'] = array(
				'#type' => 'textarea', 
				'#title' => t('Project description'), 
				'#rows' => 6,
				'#attributes' => array('class' => array('full_width')),
				'#description'=>'Description information can include:<ul><li>Project background info</li><li>Product backround info</li><li>Summarized design brief</li><li>Ecodesign strategies to be explored.</li></ul>There is no character limit on this or subsequent free text fields.',
				'#default_value' => $default_values['description'],
				'#required' => FALSE,
				'#cols' => 30
			);
			
			if ($is_admin) {
  			$form['version'] = array(
  				'#type' => 'select',
  				'#title' => 'LCA Dataset Version',
				'#attributes' => array('class' => array('form-control form-select')),
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
			$form['actions'][] = setup_project_form_open_div('mb-5  form-button-actions form-group d-flex justify-content-between');	
			$form['actions'][] = setup_project_form_open_div('general-action actions-right');
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
			$form['actions'][] = setup_project_form_close_div();
			$form['actions'][] = setup_project_form_open_div('general-action');
			$form['actions'][] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form['actions'][] = setup_project_form_close_div();
			$form['actions'][] = setup_project_form_close_div();
			break;
		
		case PROJECT_PAGE_NAME_GOALS:
			//Custom function for creating checboxes - drupal checkboxes elements don't support multi-page forms.
			//setup_project_checkboxes($form, 'phases', $phases, setup_project_element_default('phases', $form_values), 'Lifecycle Phase');
			$form['development'] = array(
				'#type' => 'textarea', 
				'#title' => t('Company goals and environmental policies'), 
				'#rows' => 6,
				'#description'=>'',
				'#attributes' => array('class' => array('w-100')),
				'#default_value' => $default_values['development'],
				'#required' => FALSE,
			);
			$form['assessment'] = array(
				'#type' => 'textarea', 
				'#title' => t('Project assessment goals'), 
				'#rows' => 6,
				'#attributes' => array('class' => array('w-100')),
				'#description'=>'<p class="mb-2"><strong>Why are you conducting this assessment - what do you hope to learn?</strong></p><em>Examples: <ul><li>Understand which ecodesign strategies result in the greatest environmental performance improvement</li><li>How to Increase environmental performance by at least 20% from the 2008 version (the reference)</li><li>Determine how the results support corporate goals to begin to better define product-level goals</li></ul></em>',
				'#default_value' => $default_values['assessment'],
				'#required' => FALSE,
			);
			
			$form[] = setup_project_form_open_div('mb-5  form-button-actions form-group d-flex justify-content-between');
			
			$form[] = setup_project_form_open_div('general-action actions-right');
			if (!$is_add) {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE_EXIT,
				);
			} else {
				$form['back'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_BACK,
				);
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_NEXT,
				);
				// $form[] = setup_project_form_close_div();
				// $form[] = setup_project_form_open_div('general-action actions-right');
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
				'#markup' => '<div class="assement_function_unit mt-2 mb-2"><h3>Functional unit</h3>'
            );

            $form['funame'] = array(
				'#type' => 'hidden',
				'#maxlength' => 255,
				'#attributes' => array('id' => array('edit-funame')),
				'#default_value' => ($default_values['funame']) ? $default_values['funame']:"1 year of use",
				'#size' => 100
			);
			$form['fudesc'] = array(
				'#type' => 'hidden',
				'#maxlength' => 10000,
				'#attributes' => array('id' => array('edit-fudesc')),
				'#default_value' => ($default_values['fudesc']) ? $default_values['fudesc']:"Year of use is a standard unit of measure when service delivered is measured by time.",
				'#size' => 100
			);

			//fades out when clicking $change_button; fades in on cancel
			if(!$product['numConcepts'])
				$change_button = '<div class="planyear_use"><a id="product-functional-unit-change-button" class="btn btn-success btn-sm mb-2">Change</a></div>';
			else
				$change_button = '';

			$form['func-change'] =array(
				'#markup'=>'<div id="product-functional-unit-change">
				<div id="product-functional-unit-value-container"><span id="product-functional-unit-value" class="fw-bold"></span><span id="product-functional-unit-value-note"></span></div>
				<div id="product-functional-unit-desc"></div>'.$change_button.'</div>',
			);
			
			//fades in when clicking $change_button; fades out on cancel
			$form['open-product-functional-unit-edit'] =array(
				'#type'=>'markup',
				'#markup'=>'<div id="product-functional-unit-edit" class="pf-unit-edit">',
			);

			// $form['func-define-title'] =array(
			// 	'#type'=>'markup',
			// 	'#markup'=>'',
			// );
			
			$form['product-functional-unit-edit-value'] = array(
				'#type' => 'textfield', 
				'#prefix' => '<div class="form-item no-top-border change-text"><label class="form-label">Change the functional unit for this product.</label></div><div class="form-item no-top-border">',
				'#title' => 'Impacts per ',
				'#size' => 60,
				'#star'=>true,
				'#suffix' => '</div>'
			);
		
			$form['product-functional-unit-edit-desc'] = array(
				'#type' => 'textarea', 
				'#prefix' => '<div class="form-item no-top-border">',
				'#description' => 'Add a note describing why this functional unit was selected. This description will be displayed in each concept as a reminder.<br /><em>Example: Year of use is a standard unit of measure when service delivered is measured by time.</em>',
				'#attributes' => array('class' => array('form-textarea resizable w-100')),
				'#default_value' => $default_values['fudesc'],
				'#cols' => 60,
				'#rows' => 2,
				'#required' => FALSE,
				'#suffix' => '</div>'
			);
			
			$form['func-buttons'] = array(
				'#type' => 'markup', 
				'#prefix' => '<div class="submit-buttons">',
				'#markup' => 
				'<span id="product-functional-unit-edit-cancel" class="inner-button">Cancel</span>
                <span id="product-functional-unit-edit-save" class="inner-button">Update default</span>
                <div class="clear"></div>',
				'#suffix' => '</div>'
			);
			$form['close-product-functional-unit-edit'] =array(
				'#markup'=>'</div>',
			);
			
			if(!$product['numConcepts']) {
				$form['func-note'] =array(
					'#markup'=>'<div class="planyear_use"><strong class="text_warning">Note: upon completing the project setup, the functional unit can no longer be changed.</strong></div>',
				);
			}
			$form['func-explanation'] = array(
				'#prefix' => '<div id="product-functional-unit-explanation">',
				'#markup' => '
                <div class="functional_explain">
                <p>
                <strong>The functional unit describes the service <em>the product delivers</em> to the end user.</strong> <br />It is used to normalize assessment results and enables your concepts to be compared. Results are displayed in \'Impacts per [your functional unit]\'.
				</p>
                <p>
                <strong>
                Choose a functional unit relevant to the entire intended service life.
                </strong>
                <br />
                Example: If the product is intended to last 10 years, the functional unit should be years, not days. 
                </p>
                <p>
                <strong>
                When creating concepts, you will specify the <em>total amount of service delivered</em> (TASD) for each concept over its lifetime. 
                </strong> 
                Example for a household appliance: functional unit of 1 year of use and a product concept with a TASD of 5 years. 
				</p>
                </div>
                <div class="clear"> </div>
                ',
				'#suffix' => '</div><br />'  // trying to force a break here. This makes the divider visible again.
			);
			
			
			// $form['product-system'] = array(
			// 	'#markup' => '<div class="form-item group-title"><label class="form-label">Product system</label></div>'
			// 	);
			$form['system'] = array(
				'#type' => 'textarea', 
				'#title' => 'Product system',
				//'#prefix' => '<div class="no-top-border">',
				'#description' => '<strong>Describe the product system and system boundaries.</strong>',
				'#rows' => 4,
				'#default_value' => $default_values['system'],
				'#attributes' => array('class' => array('w-100')),
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
			$imagePath = $default_values['icon']?$default_values['icon']:'/drupal8/web/sites/default/files/2021-07/no_scope.gif';
			$form['defaultImgPath'] = array(
				'#type' => 'hidden',
				'#attributes' => [
					'id' => 'defaultPath',
				  ],
				'#default_value' => '/drupal8/web/sites/default/files/2021-07/no_scope.gif',
				);
			$form['icon'] = array(
				'#type' => 'hidden',
				'#attributes' => [
					'id' => 'Icon'
				  ],
				'#default_value' => $imagePath,
				);
			$form['img_upload'] = array(
				'#type' => 'managed_file',
				// '#upload_validators' => $validators,
				'#upload_validators' => [
					'file_validate_extensions' => ['png,jpeg,jpg,gif'],
				  ],
      			'#upload_location' => 'public://',
				'#name' => 'img_upload',
				'#attributes' => [
					// 'class' => array('d-none'),
					'id' => 'ImgFile'
				  ],
				);
			$form['image_wrapper'] = [
				'#markup' => '<div class="mb-3 form-group noscope_upload">
				<label for="edit-system" class="form-label"><strong>If you have an image that illustrates the product system, upload it here.</strong></label><br>
				<img id="ShowImage" src="'.$imagePath.'" class="mb-2" alt="scope image" title="system upload">
				<ul class="d-flex mb-2">
					<li><a id="Default-img" class="btn">Use default image</a></li>
					<li><a id="Browse-img" class="btn">Browser</a></li>
				</ul>
				<div class="gl-upload-help">Image format: gif, jpg, png<br>Max width: 670px</div>
			</div>'
			];
			// $form['file'] = array(
			// 	'#prefix' => '<div class="form-item no-top-border no-top-pad no-bottom-margin">',
			// 	'#description' => 'If you have an image that illustrates the product system, upload it here.',
			// 	'#type' => 'uploadify_uploader',
			// 	'#default_value'=> $default_values['icon'] ? $default_values['icon'] : DEFAULT_SCOPE_IMAGE,
			// 	'#suffix' => t("</div>")
			// );
			// $form['upload_help'] =array(
			// 	'#type'=>'markup',
			// 	'#markup'=>'<div class="gl-upload-help">Image format: gif, jpg, png<br />Max width: 670px</div>',
			// );
			$form['exclusion'] = array(
				'#type' => 'textarea',
				//'#title' => t(''), 
				'#prefix' => '<div class="form-item no-top-border">',
				'#description' => '<strong>List product system exclusions.</strong> <p class="forms-description"><em>Example: If you\'re designing a lighting fixture and DO NOT intend to include the bulb, indicate that here.</em></p>',
				'#default_value' => $default_values['exclusion'],
				'#attributes' => array('class' => array('form-textarea resizable w-100')),
				'#cols' => 60,
				'#rows' => 3,
				'#required' => FALSE,
				'#suffix' => t("</div>")
			);


			$form[] = setup_project_form_open_div('mb-5  form-button-actions form-group d-flex justify-content-between');
			$form[] = setup_project_form_open_div('general-action actions-right');
			if (!$is_add) {
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE_EXIT,
				);				
			} else {
				$form['back'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_BACK,
				);
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_NEXT,
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
				'#markup' => '<div class="project-complete mt-4 mb-5">
				<h5 class="heading_6">Project setup complete!</h5>
				<h5 class="heading_6">You can now create concepts for assessment.</h5>
				<p>The first concept created will automatically become the <b>reference concept</b> for this project. The reference concept is  the baseline to which subsequent concepts are compared. Once you have created more than one concept for this project, you can designate a different concept as the reference. </p>
				</div>'
			);
			$form[] = setup_project_form_open_div('mb-5  form-button-actions form-group d-flex justify-content-between');
				
			$form[] = setup_project_form_open_div('general-action  actions-right');
			$form['back'] = array(
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_BACK,
			);
			$form['submit'] = array(
				'#type' => 'submit',
				'#value' => 'No thanks, I\'ll create concepts later',
			);
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
