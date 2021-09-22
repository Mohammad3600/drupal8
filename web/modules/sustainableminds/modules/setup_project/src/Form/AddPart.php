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
use Drupal\menu_link_content\Entity\MenuLinkContent;
include_once(dirname(__FILE__).'\..\..\init.inc');

class AddPart extends FormBase {  
   /**  
	 * {@inheritdoc}  
	 */  
	protected function getEditableConfigNames() {  
		return [  
		'setup_project.addproduct',  
		];  
	}

	/**  
	 * {@inheritdoc}  
	 */  
	public function getFormId() {  
		return 'Add_part';  
	}  

	/**  
	 * Creates Setup project Form  
	 */  
	public function buildForm(array $form, FormStateInterface $form_state, $mode=null, $conceptid=null, $phaseid=null, $componentid=null) { 
        //If this is an edit, get all material information to populate the fields with.
		// $mid = sustainable_minds_sbom_get_afterspecial_arg(true, $mid); //arg(6) (only for edits)
		$db = \Drupal::service('setup_project.sbom_db');
		// if ($mode == "edit" && $mid > 0) {
		// 	$is_add = false;
		// 	$item = $db->get_item($mid);
		// 	$default_values = array('itemID' => $mid,
		//         'title' => $item['name'], 
		//         'partID' => $item['partID'], 
		//         'quantity' => $item['quantity'], 
		//         'amount' => $item['factor'], 
		//         'description' => $item['description'], 
		//         'measure' =>$item['measurement'],
		//         'unit_symbol' =>$item['unit_symbol'], 
		//         'unitID' =>$item['unitID'], 
		//         'matProcName' =>$item['matprocname'], 
		//         'matProcID' =>$item['materialID']);
		// } else
		if ($mode == 'add' && is_numeric($conceptid)) {
			$is_add = true;
			$parentID = sustainable_minds_arg_set($componentid,true, 0); //arg(5)
			if (!$parentID) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		} else {
			// sustainable_minds_set_error_goto('The part you are looking for does not exist.');
		}
		/**********************************************/
		// DEBUG
		//watchdog('mp', 'Debug BAG 1');
		//watchdog('mp', 'Debug BAG 1 parentID:' . $parentID);
		//Phase
		$phaseid = sustainable_minds_arg_set($phaseid,true, PHASEID_MANUFACTURE);
		$componentid = sustainable_minds_arg_set($componentid); //arg(5)
		// use component to find out version
		$version = $db->get_version_for_component($componentid);
		if ($phaseid == PHASEID_MANUFACTURE) {
			$component = $db->get_component($componentid);
			$parent_name = ' to ' . $component['name'];
		} elseif ($phaseid == PHASEID_USE) {
			// go up the tree, find the component with a componentTypeID != 0 that is the root
			// base the material tree of that root
			$component = $db->get_component($componentid);
			while($component['componentTypeID'] == 0 && $component) {
				$componentid = $component['parentID'];
				$component = $db->get_component($componentid);
			}
		} elseif ($phaseid == PHASEID_TRANSPORT) {
			$component = $db->get_component($componentid);
			if ($component['phaseID'] == PHASEID_TRANSPORT) { //trans for entire product
				$weight = $db->get_trans_weight($componentid);
			} else {										//trans for subassemblies
				$weight = $db->get_weight($componentid);
			}
		}	
		$presets = sustainable_minds_form_presets($phaseid);
		$root = is_array($presets['root']) ? $presets['root'][$component['componentTypeID']] : $presets['root'];
		$term2 = is_array($presets['term_main']) ? $presets['term_main'][$component['componentTypeID']] : $presets['term_main'];
		$term = is_array($presets['term_sub']) ? $presets['term_sub'][$component['componentTypeID']] : $presets['term_sub'];
		$amount_text = $presets['amount'];
		$s = $_POST;
		// if add  
		if ($_POST['newMatProcID']) {
			//watchdog('mp', 'Debug 2 BAG0',$_POST['newMatProcID']);
			//Get component based on the component's phase. use global variables to select which categories to display.
			$materialid = $_POST['newMatProcID'];
			$amount = $_POST['amount'];
		} elseif (!$is_add) {
			$mat_name = $default_values['matProcName']; //' [id:'.$default_values['matProcID'] .']'
			$materialid = $default_values['matProcID'];
			$amount = (float)$default_values['amount'];
		}
		$form['#attributes'] = array(
			'class' => array('mt-3 mx-2'),
		);
		$form['#title'] = $this->t($term);
		$am = $db->list_measurementTypes();
		$defaultMeasure = 1; 
		foreach ($am as $key => $measure) {
			$allmeasurements[$key] = $measure['name'];
			if ($measure['name'] == $default_values['measure']) {
				$defaultMeasure = $key;
			}
		}
		if ($is_add) {
			$header = 'Add a '. strtolower($term2). $parent_name;
		} else {
			$header = 'Edit '. $mat_name;		
		}
		$form['is_add'] =array(
			'#type'=>'value',
			'#value'=>$is_add,
		);
		$form['header'] =array(
			'#markup'=>'<div class="blue-header"><p>'.$header.'</p></div>',
		);
		//  form body contents wrapper
		$form['form-body-open'] =array(
			'#markup'=>'<div class="add-edit-container">',
			'#suffix'=>'<div class="inset">',
		);
		// header wrapper open for text-only contents
		if ($phaseid == PHASEID_MANUFACTURE || $phaseid == PHASEID_TRANSPORT) {
			$form['head_contents_wrapper-open'] = array(
			'#markup'=>'<div class="name-qty-id-wrapper">',
			);
		} 
		$form['title'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'textfield',
			'#title' => ($phaseid == PHASEID_MANUFACTURE) ? t('Part name') : t('Name'),
			'#maxlength' => 255,
			'#size' => 40,
			'#default_value' => $default_values['title'],
			'#required' => FALSE,
		);
		if ($phaseid == PHASEID_TRANSPORT) {
			$form['about_text'] = array(
				'#prefix' => '<div class="form-item about-assembly">',
				'#markup' => t('<label>Weight: </label><p>@weight lb</p>', array('@weight' => $weight)),
				'#attributes' => array(),
				'#suffix' => '</div>'
			);
		}
		$form['partID'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'textfield',
			'#title' => t('Part #'),
			'#maxlength' => 40,
			'#size' => 40,
			'#default_value' => $default_values['partID'],
		);
		$form['quantity'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'textfield',
			'#title' => t('Quantity'),
			'#maxlength' => 10,
			'#size' => 10,
			'#default_value' => $default_values['quantity'] ? $default_values['quantity'] : 1,
			'#required' => TRUE,
		);
		/********************/
		/* datatool version */
		/********************/
		
		//$version = $_REQUEST['datasetversion'] ? $_REQUEST['datasetversion'] : '';
		
		// header wrapper close
		if ($phaseid == PHASEID_MANUFACTURE || $phaseid == PHASEID_TRANSPORT) {
			$form['head_contents_wrapper-close'] = array(
				'#markup'=>'</div>',
			);
		}
		// $form['myitem'] = array(
		//'#twig_suggestion' => 'add-part-fields',
		//	'#type' => 'sustainable_minds_browser',
		// 	'#title' => 'myitem',
		// 	'#root' => $root,
		//     	'#itemtitle' => t('Select !term', array('!term'=>strtolower($term))), 
		//     	'#methodology' => t('<div class="browser-methodolgy">Methodology:&nbsp; !tt </div>', array('!tt'=>sustainable_minds_methodology($version, true))),
		// 	'#matProcID' =>  $materialid,
		// 	'#mat_value'=> $mat_name,
		// 	'#version' => $version,
		// 	'#show_browser' => $is_add,
		// 	'#process' => array('_sustainable_minds_browser'=>array()),
		// );
		
		//  amount area wrapper - contains amt & ELM
		$form['form-amtelm-open'] = array(
			'#markup'=>'<div class="amt-measure-wrapper">',
		);
		$form['amount'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'textfield',
			'#title' => t($amount_text),
			'#maxlength' => 25,
			'#size' =>15,
			'#field_suffix' => t('<span id="edit-amount-suffix" style="display:none"> '.$default_values['unit_symbol'].'</span>'),
			'#default_value' => $amount,
			'#prefix' => t('<div id="amount-wrapper">'),
			'#required' => TRUE,
			//'#id'=>'form-item-amount'
		);

		/*************************************************
		 * ADD DROPDOWN LISTS FOR UNIT OF MEASURE 
		 * - bmagee added for uom conversion 2-15-2010
		 * - requires custom module "units-api"
		 * - build list for each measure type
		 *   (length,volume,weight,area,time,temperature)
		 * MOVE THIS TO THE PROJECT LEVEL
		 *************************************************/
		
		// 	$idx = $default_values['unitID'];
		// //	watchdog('mp-eol', 'Debug 3 unitID:' . $idx);
		// 	$defaultUnitName = $default_values['unit_symbol'];
			
		// 	/* set up default unitID */
		// 	$unitslist1 = unitsapi_getUnitsList('length');
		// 	$form['units-list']['units-1'] = array(
		//'#twig_suggestion' => 'add-part-fields',
		// 	'#type' => 'select',
		// 		'#options' => $unitslist1,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist1[$idx],
		//    	'#prefix' => t('<div id="units-list-1" style="display:none">'),
		//   	'#suffix' => t('</div>'),
		// 	);
		//   $unitslist2 = unitsapi_getUnitsList('volume');
		//   $form['units-list']['units-2'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'select',
		// 		'#options' => $unitslist2,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist2[$idx],
		// 	  '#prefix' => t('<div id="units-list-2" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$unitslist3 = unitsapi_getUnitsList('mass');
		//   $form['units-list']['units-3'] = array(
		//'#twig_suggestion' => 'add-part-fields',		
		//'#type' => 'select',
		// 		'#options' => $unitslist3,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist3[$idx],
		// 	  '#prefix' => t('<div id="units-list-3" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$unitslist4 = unitsapi_getUnitsList('area');
		//   $form['units-list']['units-4'] = array(
		//'#twig_suggestion' => 'add-part-fields',// 		
		//'#type' => 'select',
		// 		'#options' => $unitslist4,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist4[$idx],
		// 	  '#prefix' => t('<div id="units-list-4" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$unitslist5 = unitsapi_getUnitsList('time');
		//   $form['units-list']['units-5'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'select',
		// 		'#options' => $unitslist5,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist5[$idx],
		// 	  '#prefix' => t('<div id="units-list-5" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$unitslist6 = unitsapi_getUnitsList('temp');
		//   $form['units-list']['units-6'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'select',
		// 		'#options' => $unitslist6,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist6[$idx],
		// 	  '#prefix' => t('<div id="units-list-6" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$unitslist7 = unitsapi_getUnitsList('energy');
		//   $form['units-list']['units-7'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'select',
		// 		'#options' => $unitslist7,  
		// 		'#attributes' => array('onchange' => "javascript:selectunit(this);"), 
		// 		'#default_value' => $unitslist7[$idx],
		// 	  '#prefix' => t('<div id="units-list-7" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);
		// 	$form['units-list']['units-0'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'markup',
		// 		'#value' => 'lb',
		// 	  '#prefix' => t('<div id="units-list-0" style="display:none">'),
		// 	  '#suffix' => t('</div>'),
		// 	);  
			
		// 	/* Hidden fields; originalUnitName, newUnitName, displayUnitID, addMode */
		// 	//why aren't these just type = hidden?
		// 	$form['originalUnitName'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'textfield',
		// 		'#default_value' => $default_values['unit_symbol'],
		// 	  '#prefix' => t('<div id="unit-of-measure" style="display:none">'),
		// 	);
		// 	$form['newUnitName'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'textfield',
		// 		'#default_value' => '',
		// 	);
		// 	$form['parentUnitName'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'textfield',
		// 		'#default_value' => $defaultUnitName,
		// 	);
		// 	$form['displayUnitID'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'textfield',
		// 		'#default_value' => $idx,
		// 		'#default_value' => '',
		// 	);
		// 	$form['addMode'] = array(
		//'#twig_suggestion' => 'add-part-fields', 		
		//'#type' => 'textfield',
		// 		'#default_value' => $is_add,
		// 	 	'#suffix' => t('</div>'),
		// 	);
			
		/*************************************************/
		// $form['measure'] = array(
		// 	'#twig_suggestion' => 'add-part-fields',
		// 	'#type' => 'select', 
		// 	'#required' => TRUE,
		// 	'#attributes' => array('class' => array('w-auto p-0 ps-1 pe-3 font-size-11')),
		// 	'#default_value' => $defaultMeasure,
		// 	'#title' => t('Is this selected '.strtolower($term).' and/or '.strtolower($amount_text).' based on'), 
		// 	'#options' => $allmeasurements,
		// 	'#prefix' => t('</div>'),
		// //'#id'=>'form-item-measurement'
		// );
		$form['form-amtwrap-close'] = array(
			'#markup'=>'</div>',
		);
		$form['measure'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'select', 
			'#required' => TRUE,
			'#attributes' => array('class' => array('w-auto p-0 ps-1 pe-3 font-size-11')),
			'#default_value' => $defaultMeasure,
			'#title' => t('Is this selected '.strtolower($term).' and/or '.strtolower($amount_text).' based on'), 
			'#options' => $allmeasurements,
		//'#id'=>'form-item-measurement'
		);

	//  amount area wrapper close - contains amt & ELM
		$form['form-amtelm-close'] = array(
			'#markup'=>'</div><hr>',
		);
		$form['description'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'textarea', 
			'#title' => t('Comments'), 
			'#rows' => 4,
			'#attributes' => array('class' => array('form-textarea resizable w-100')),
			'#default_value' => $default_values['description'],
			'#description' => t("Comments, assumptions, source of idea, etc..."),
			'#required' => FALSE,
		);
		$form['term'] = array(
			'#twig_suggestion' => 'add-part-fields',
			'#type' => 'hidden', 
			'#value' => $term,
		);	
		//  form body wrapper close
		$form['form-body-close'] =array(
			'#markup'=>'</div>',
			'#prefix'=>'</div>',
			);
			$form[] = setup_project_form_open_div('form-button-actions');
				
			if ($is_add) {
				$form[] = setup_project_form_open_div('general-action  actions-right');
				if ($materialid && !count($db->list_process_by_material($materialid, 'noteol'))) {
					$attributes = array('class'=>'disabled');
					$disabled = TRUE;
				} else {
					$attributes = array(); 
					$disabled = FALSE;
				}
				$form['submit_process'] = array(
					'#twig_suggestion' => 'add-part-fields',
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_ADD_TO_SBOM . ' and add a process',
					'#attributes' => $attributes,
					'#disabled' => $disabled
				);
				$form[] = setup_project_form_close_div();
				$form[] = setup_project_form_open_div('general-action  actions-right');
				$form['submit_material'] = array(
					'#twig_suggestion' => 'add-part-fields',
					'#type' => 'submit',
					'#value' =>  BUTTON_LABEL_ADD_TO_SBOM . ' and add another ' .strtolower( $term2),
				);
				$form[] = setup_project_form_close_div();
				$form[] = setup_project_form_open_div('general-action actions-right');
				$form['submit_done'] = array(
					'#twig_suggestion' => 'add-part-fields',
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_ADD_TO_SBOM,
				);
				$form[] = setup_project_form_close_div();
			} else {
				$form[] = setup_project_form_open_div('general-action actions-right');
				$form['submit_done'] = array(
					'#twig_suggestion' => 'add-part-fields',
					'#type' => 'submit',
					'#value' => BUTTON_LABEL_SAVE,
				);
				$form[] = setup_project_form_close_div();
			}
			$form[] = setup_project_form_open_div('general-action');
			$form['submit_cancel'] = array(
				'#twig_suggestion' => 'add-part-fields',
				'#type' => 'submit',
				'#value' => BUTTON_LABEL_CANCEL,
			);
			$form[] = setup_project_form_close_div();
			$form[] = setup_project_form_close_div();
			// $conceptid =sustainable_minds_sbom_get_concept_arg();
			// sustainable_minds_concept_bread($conceptid);
			$form['#multistep'] = TRUE;
			$form['#redirect'] = FALSE;
			if (is_array($presets['removedfields']['add'])) {
				if ($is_add) {
					$remove = $presets['removedfields']['add'];
				} else {
					$remove = $presets['removedfields']['edit'];
				}
			} else {
				$remove = $presets['removedfields'];
		}		
		foreach ($remove as $val) {
			unset($form[$val]); /*remove the fields we don't want*/
		}	
		$form['#validate'] = array('sustainable_minds_material_edit_validate_default'=>array());
		$form['#submit'] = array('sustainable_minds_material_edit_submit_default'=>array());
		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state)
	{
		if ($form_state->getValue('op') != BUTTON_LABEL_CANCEL) {
			if (!$form_state->getValue('amount')) {
				$form_state->setErrorByName('amount', t(TEXT_ERROR_ITEM_SET_AMOUNT));
			} else if (!is_numeric($form_state->getValue('amount'))) {
				$form_state->setErrorByName('amount', t(TEXT_ERROR_ITEM_NUMERIC_AMOUNT));
			}		
			if (arg(2) == 'add' && $form_state->getValue('newMatProcID')) {
				// only get materialID if it's in add mode
				// parse for ID of material 
				$materialid = $form_state->getValue('newMatProcID');
				if (!is_numeric($materialid)) {
					$form_state->setErrorByName('myitem', t( TEXT_ERROR_ITEM_SELECT_MATPROC));
				}						
				$qty = $form_state->getValue('quantity');
				if ($qty){
					if (!is_numeric($qty)) {
						$form_state->setErrorByName('quantity', t('<strong>Quantity must be numeric</strong>'));
					}
				}				
			}
		} else { // cancel, go back to conceptbuild
			$goto = URL_CONCEPT_BUILD.'/'.sustainable_minds_sbom_combine_url_arg(null, null, -1);
				drupal_get_messages();
				drupal_goto($goto);
		}
    }

    public function submitForm(array &$form, FormStateInterface $form_state) { 
		$db = \Drupal::service('setup_project.sbom_db');
		//	watchdog('material_edit','entering edit_submit_default');
		//if not numeric, set to 1 or give error?
		if (empty($form_state->getValue('quantity'))) {
			$form_state->setValue('quantity',1) ;
		}
		if (arg(2) == 'add') {
			// FIXME: for transporation on sub-assemblies, displayUnitID and displayFactor don't get set on add, only on edit
			// FIXME Insert data into database using $Sessions
			$componentid = sustainable_minds_sbom_get_special_arg();
			// set the title to term if none is set
			if (empty($form_state->getValue('title')) && $form_state->getValue('title') !== '0') {
				$term = $form_state->getValue('term');
				if (strtolower($term) != 'material') {
					$form_state->setValue('title',$term);
				} else {
					$form_state->setValue('title', DEFUALT_ITEM_TEXT);
				}
			}
			if ($form_state->getValue('displayUnitID')) {
				$displayUnitName = unitsapi_getUnitName($form_state->getValue('displayUnitID'));
				$displayAmount = (float) $form_state->getValue('amount');
				$info = sm_unit_convert($displayUnitName, $form_state->getValue('amount'), $form_state->getValue('originalUnitName'));
			}
			$convertedAmount = isset($info['result']) ? (float) $info['result'] : (float) $form_state->getValue('amount');
			$form_state->getValue['convertedAmount'] = (float) $convertedAmount;
			$form_state->getValue['displayAmount'] = (float) $displayAmount;
			$lastid = $db->add_item($componentid, $form_state->getValue);
			//Set GoTo based on button pressed
			switch ($form_state->getValue('op')) {
				case BUTTON_LABEL_ADD_TO_SBOM . ' and add a process':
					$goto = URL_ADD_PROCESS. '/'. sustainable_minds_sbom_combine_url_arg() .$lastid;
					break;
				
				case BUTTON_LABEL_ADD_TO_SBOM:
					$goto = URL_CONCEPT_BUILD .'/'. sustainable_minds_sbom_combine_url_arg(null,null,-1);
					break;
				
				default:
					$goto = URL_ADD_MANUFACTURING_MATERIAL . '/'. sustainable_minds_sbom_combine_url_arg() ;
					break;
			}
			$phaseid = sustainable_minds_sbom_get_phase_arg(true, PHASEID_MANUFACTURE);
			switch ($phaseid) {
				case PHASEID_TRANSPORT:
					$cookie = 'gridOpentransport';
					break;
				case PHASEID_EOL:
					$cookie = 'gridOpeneol';
					break;
				case PHASEID_USE:
					$cookie = 'gridOpenuse';
					break;
				default:
					$cookie = 'gridOpensbom';
			}
			setcookie($cookie, $_COOKIE[$cookie].'|c'.$componentid, 0, '/');
		} elseif (arg(2) == 'edit') {
			$mid = sustainable_minds_sbom_get_afterspecial_arg();
			$item = $db->get_item($mid);
				
			if (empty($form_state->getValue('title')) || trim($form_state->getValue('title')) == '') {
				$form_state->setValue('title',$item['name']); 
			}

			if (empty($form_state->getValue('partID')) || trim($form_state->getValue('partID')) == '') {
				$form_state->setValue('partID', $item['partID']);
			}
		
			$db->update_item($mid, $form_state->getValue);
		
			/* bmagee - add check for unit of measure change */
			if ($form_state->getValue('amount') != $item['factor'] || $form_state->getValue('displayUnitID') != '0') {
				$baseUnit = unitsapi_get_matproc_unitname($item['materialID']);  // fix me - use sp to handle this
				$displayUnitName = is_numeric($form_state->getValue('displayUnitID')) ? unitsapi_getUnitName($form_state->getValue('displayUnitID')) : $item['unit'];
				if (!$displayUnitName) {
					$displayUnitName = $item['unit'];
				}
				$db->set_imp_factor($item['itemMatProcID'], $form_state->getValue('amount'), $displayUnitName, $baseUnit);
			}

			$goto = URL_CONCEPT_BUILD .'/'. sustainable_minds_sbom_combine_url_arg(null,null,-1);
		}
		drupal_goto($goto);
    }
}
	