<?php
// // App Kinetics Changes
// header('Access-Control-Allow-Origin: http://app-collect.earlystageit.com');
// $path = SM_PATH.'/app-kinetics.js';
// drupal_add_js($path);
// // Decorate once
// drupal_add_js("if ('undefined'==typeof ak_jq_ajax_decorated){jQuery.ajax = appKineticsAjaxDecorator(jQuery.ajax);ak_jq_ajax_decorated=1;}", 'inline');

// //JS argument variables
// drupal_add_js("drupal_arg3 = " . drupal_to_js(arg(3)) . ";", 'inline');
// drupal_add_js("drupal_arg4 = " . drupal_to_js(arg(4)) . ";", 'inline');
// drupal_add_js("drupal_arg5 = " . drupal_to_js(arg(5)) . ";", 'inline');

// //Javascript includes
// $path = SM_PATH.'/general.js';
// drupal_add_js($path);
// $path = SM_PATH.'/dhtmlx/events.js';
// drupal_add_js($path);
// $path = SM_PATH.'/detections.js';
// drupal_add_js($path);

// Default image if none is provided
define('IMAGE_NO_IMAGE_DEFAULT', '/files/default.png');
define('DEFAULT_IMAGE', '/files/default.png');
define('DEFAULT_SCOPE_IMAGE', '/files/no_scope.gif');

//Phases PHP and JS
define('PHASEID_MANUFACTURE', 1);
define('PHASEID_USE', 2);
define('PHASEID_TRANSPORT', 3);
define('PHASEID_EOL', 4);

// drupal_add_js("PHASEID_MANUFACTURE = parseInt(" . drupal_to_js(PHASEID_MANUFACTURE) . ");", 'inline');
// drupal_add_js("PHASEID_USE = parseInt(" . drupal_to_js(PHASEID_USE) . ");", 'inline');
// drupal_add_js("PHASEID_TRANSPORT = parseInt(" . drupal_to_js(PHASEID_TRANSPORT) . ");", 'inline');
// drupal_add_js("PHASEID_EOL = parseInt(" . drupal_to_js(PHASEID_EOL) . ");", 'inline');
// //Flash version info
// drupal_add_js("REQUIRED_MAJOR_VERSION = 8", 'inline');
// drupal_add_js("REQUIRED_MINOR_VERSION = 0", 'inline');
// drupal_add_js("REQUIRED_REVISION = 0", 'inline');
// //browser version info
// drupal_add_js("REQUIRED_FIREFOX_VERSION = 2", 'inline');
// drupal_add_js("REQUIRED_OPERA_VERSION = 9", 'inline');
// drupal_add_js("REQUIRED_SAFARI_VERSION = 3", 'inline');
// drupal_add_js("REQUIRED_CHROME_VERSION = 1", 'inline');

//limits for using scientific notation
define('SN_LOWER', .001); //use s.n. for values <= this
define('SN_UPPER', 1000); //use s.n. for values >= this

//Types of component
define('COMPONENT_ROOT_MAN', 1);
define('COMPONENT_ROOT_TRA', 2);
define('COMPONENT_ROOT_USE', 3);
define('COMPONENT_ROOT_CON', 4);
define('COMPONENT_ROOT_POW', 5);
define('COMPONENT_ROOT_WAT', 6);
// componet, process, or item, used in AJAX
define('COMPONENT_TYPE_CHECK', 1);
define('TOTAL_TYPE_CHECK', 7);
define('EMPTY_TYPE_CHECK', 8);
define('ITEM_TYPE_CHECK', 2);
define('ITEM_NO_PROC_TYPE_CHECK', 3);
define('ITEM_WITH_PROC_TYPE_CHECK', 4);
define('PROCESS_TYPE_CHECK', 5);
define('ONLY_TRANS_SCORES', 1);
// drupal_add_js("COMPONENT_ID = parseInt(" . drupal_to_js(COMPONENT_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("ITEM_ID = parseInt(" . drupal_to_js(ITEM_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("ITEM_NO_PROC_ID = parseInt(" . drupal_to_js(ITEM_NO_PROC_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("ITEM_WITH_PROC_ID = parseInt(" . drupal_to_js(ITEM_WITH_PROC_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("PROCESS_ID = parseInt(" . drupal_to_js(PROCESS_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("TOTAL_ID = parseInt(" . drupal_to_js(TOTAL_TYPE_CHECK) . ");", 'inline');
// drupal_add_js("EMPTY_ID = parseInt(" . drupal_to_js(EMPTY_TYPE_CHECK) . ");", 'inline');

define('BOMLOAD_IMPORT_STATE_MISSING', 1);
define('BOMLOAD_IMPORT_STATE_READY', 2);
define('BOMLOAD_IMPORT_STATE_IMPORTED', 3);
// drupal_add_js("BOMLOAD_IMPORT_STATE_MISSING = parseInt(" . drupal_to_js(BOMLOAD_IMPORT_STATE_MISSING) . ");", 'inline');
// drupal_add_js("BOMLOAD_IMPORT_STATE_READY = parseInt(" . drupal_to_js(BOMLOAD_IMPORT_STATE_READY) . ");", 'inline');
// drupal_add_js("BOMLOAD_IMPORT_STATE_IMPORTED = parseInt(" . drupal_to_js(BOMLOAD_IMPORT_STATE_IMPORTED) . ");", 'inline');
// FIXME
define('LIFETIME_UNITS','years');
//Category ID's
define('SM_PROCESS_CATEGORY_ID', 11);
// drupal_add_js("SM_PROCESS_CATEGORY_ID = parseInt(" . drupal_to_js(SM_PROCESS_CATEGORY_ID) . ");", 'inline');
define('SM_EOL_CATEGORY_ID', 11);
define('SM_CONSUMABLE_CATEGORY_ID', 7);
define('SM_TRANSPORTATION_CATEGORY_ID', 4);
define('SM_MATERIAL_CATEGORY_ID', 1);
define('SM_CONSUMABLE_CATEGORY_ID2', 8);
define('SM_WATER_CATEGORY_ID', 10);
define('SM_POWER_CATEGORY_ID', 9);
define('SM_ROOT_PRIVATE_CAT_ID', 820);
define('SM_PRIVATE_CAT_TYPE', 'private');
define('SM_MATERIALS_CAT_TYPE', 'materials');
define('SM_PRODUCTS_CAT_TYPE', 'products');
define('SM_CATEGORY_TREE_SYSTEM_OWNER', 1000000);
define('SM_PRIVATE_DATA_SET_NODE_ID', 892);
// drupal_add_js("SM_PRIVATE_DATA_SET_NODE_ID = parseInt(" . drupal_to_js(SM_PRIVATE_DATA_SET_NODE_ID) . ");", 'inline');
define('SM_NONWOVENS_NODE_ID', 925);
// drupal_add_js("SM_NONWOVENS_NODE_ID = parseInt(" . drupal_to_js(SM_NONWOVENS_NODE_ID) . ");", 'inline');

define('URL_ADMIN_SETTINGS_PARENT','admin/settings/sm');
define('URL_ADMIN_SETTINGS','admin/settings/sm/general');
define('URL_ADMIN_SETTINGS_DB','admin/settings/sm/db');

define('URL_PROJECT','project');
define('URL_PROJECT_LIST_USER','project/list/user');
define('URL_PROJECT_LIST','project/list');
define('URL_PROJECT_VIEW','project/view');
define('URL_PROJECT_CONCEPTS','project/concepts');
define('URL_PROJECT_SCOPE','project/scope');
define('URL_PROJECT_GOAL','project/goal');


define('URL_PROJECT_ADD','project/add');
// drupal_add_js("URL_PROJECT_ADD = '" . URL_PROJECT_ADD . "';", 'inline');
define('URL_AJAX_PROJECT_NEW','ajax/actions/newproject');
// drupal_add_js("URL_AJAX_PROJECT_NEW = '" . URL_AJAX_PROJECT_NEW . "';", 'inline');
define('URL_PROJECT_EDIT','project/edit');
define('URL_CONCEPT_VIEW','project/concept/view');
define('URL_CONCEPT_BUILD','project/concept/build');
// drupal_add_js("URL_CONCEPT_BUILD = '" . URL_CONCEPT_BUILD . "';", 'inline');
define('URL_CONCEPT_ADD','project/concept/add');
define('URL_CONCEPT_EDIT','project/concept/edit');
define('URL_CONCEPT_RESULT','project/concept/result');
// drupal_add_js("URL_CONCEPT_RESULT = '" . URL_CONCEPT_RESULT . "';", 'inline');
define('SUBURL_CONCEPT_RESULT_SCORECARD','scorecard');
#define('SUBURL_CONCEPT_RESULT_COMPONENT_LIFETIME','input/okala');
define('SUBURL_CONCEPT_RESULT_COMPONENT_LIFETIME','input/total');
define('SUBURL_CONCEPT_RESULT_COMPONENT_CO2','input/co2');
define('SUBURL_CONCEPT_RESULT_COMPONENT_CATEGORIES','input/impacts');
define('SUBURL_CONCEPT_RESULT_LIFECYCLE_PHASE','lifecycle/total');
#define('SUBURL_CONCEPT_RESULT_LIFECYCLE_PHASE','lifecycle/okala');
define('SUBURL_CONCEPT_RESULT_LIFECYCLE_CO2','lifecycle/co2');
define('SUBURL_CONCEPT_RESULT_LIFECYCLE_CATEGORIES','lifecycle/impacts');
define('SUBURL_CONCEPT_RESULT_SBOM','bom');


define('URL_CREATE_NEW_PRODUCT', 'project/add');

define('URL_ADD_COMPONENT', 'project/component/add');
define('URL_EDIT_COMPONENT', 'project/component/edit');
define('URL_ADD_MANUFACTURING_MATERIAL', 'project/material/add');
define('URL_EDIT_MANUFACTURING_MATERIAL', 'project/material/edit');
define('URL_ADD_PROCESS', 'project/process/add');
define('URL_EDIT_PROCESS', 'project/process/edit');

define('URL_BOMLOAD_FILE', 'project/concept/upload');
define('URL_BOMLOAD_APPROVE', 'project/concept/import');
// drupal_add_js("URL_BOMLOAD_APPROVE = '" . URL_BOMLOAD_APPROVE . "';", 'inline');
define('URL_BOMLOAD_APPROVE_MATERIAL', 'project/material/import');
// drupal_add_js("URL_BOMLOAD_APPROVE_MATERIAL = '" . URL_BOMLOAD_APPROVE_MATERIAL . "';", 'inline');
define('URL_BOMLOAD_APPROVE_TRANSPORTATION', 'project/transportation/import');
// drupal_add_js("URL_BOMLOAD_APPROVE_TRANSPORTATION = '" . URL_BOMLOAD_APPROVE_TRANSPORTATION . "';", 'inline');
define('URL_BOMLOAD_APPROVE_PROCESS', 'project/process/import');
// drupal_add_js("URL_BOMLOAD_APPROVE_PROCESS = '" . URL_BOMLOAD_APPROVE_PROCESS . "';", 'inline');
define('URL_BOMLOAD_DELETE', 'sbom/ajax/import/delete');
define('URL_AJAX_BOMLOAD_APPROVE_RECORD', 'ajax/actions/bomload/approve_record');
define('URL_AJAX_BOMLOAD_APPROVE_RECORDS', 'ajax/actions/bomload/approve_records');
define('URL_AJAX_BOMLOAD_SET_MAPPEDMATPROCID', 'ajax/actions/bomload/set_mappedmatprocid');
define('URL_AJAX_BOMLOAD_GET_POSSIBLE_MATCHES', 'ajax/actions/bomload/get_matches');
define('URL_AJAX_BOMLOAD_LIST_APPROVE', 'ajax/actions/bomload/list_approve');
define('URL_BOMLOAD_VERIFICATION', 'ajax/actions/bomload/verification');
// drupal_add_js("URL_BOMLOAD_VERIFICATION = '" . URL_BOMLOAD_VERIFICATION . "';", 'inline');
// drupal_add_js("URL_AJAX_BOMLOAD_APPROVE_RECORD = '" . URL_AJAX_BOMLOAD_APPROVE_RECORD . "';", 'inline');
// drupal_add_js("URL_AJAX_BOMLOAD_APPROVE_RECORDS = '" . URL_AJAX_BOMLOAD_APPROVE_RECORDS . "';", 'inline');
// drupal_add_js("URL_AJAX_BOMLOAD_SET_MAPPEDMATPROCID = '" . URL_AJAX_BOMLOAD_SET_MAPPEDMATPROCID . "';", 'inline');
// drupal_add_js("URL_AJAX_BOMLOAD_GET_POSSIBLE_MATCHES = '" . URL_AJAX_BOMLOAD_GET_POSSIBLE_MATCHES . "';", 'inline');
// drupal_add_js("URL_AJAX_BOMLOAD_LIST_APPROVE = '" . URL_AJAX_BOMLOAD_LIST_APPROVE . "';", 'inline');
define('URL_AJAX_SET_EDIT','ajax/actions/set_edit');
define('URL_AJAX_UNSET_EDIT','ajax/actions/unset_edit');
define('URL_AJAX_FINISH_CONCEPT_ADD','ajax/actions/finish_concept_add');
define('URL_AJAX_COPY_CONCEPT', 'ajax/actions/copy');
define('URL_AJAX_SET_REFERENCE','ajax/actions/set_reference');
define('URL_AJAX_SET_FINAL','ajax/actions/set_final');
define('URL_AJAX_DELETE_CONCEPT','ajax/actions/delete_concept');
define('URL_AJAX_COPY_PROJECT','ajax/actions/copy_project');
define('URL_AJAX_COPY_UPDATE_PROJECT','ajax/actions/copy_update_project');
define('URL_AJAX_DELETE_PROJECT','ajax/actions/delete_project');
define('URL_AJAX_SET_RESULT_LABEL_OPTION','ajax/actions/set_result_label_option');
define('URL_AJAX_SET_UNIT_NAME','ajax/actions/set_unit_name');
define('URL_AJAX_COPY_SBOM_ITEM', 'ajax/actions/copy_sbom_item');  // KJH - copy feature
define('URL_AJAX_COPY_SBOM_COMPONENT', 'ajax/actions/copy_sbom_component'); // KJH - copy feature

define('URL_AJAX_GET_ITEM','sbom/ajax/getitem');
define('URL_AJAX_GET_CATEGORY','sbom/ajax/getcategory');
define('URL_AJAX_GET_MATERIAL_SINGLE','sbom/ajax/getmaterialsingle');
define('URL_AJAX_CONCEPT_GET_COMPONENTS','sbom/ajax/concept/getcomponents');
define('URL_AJAX_CONCEPT_SETUP_BOM','sbom/ajax/concept/setup/bom');

define('URL_AJAX_CONCEPT_GET_PROCESSES','sbom/ajax/concept/getprocesses');
// drupal_add_js("URL_AJAX_CONCEPT_GET_PROCESSES = '" . URL_AJAX_CONCEPT_GET_PROCESSES . "';", 'inline');

define('URL_AJAX_CONCEPT_EDIT_COMPONENTS','sbom/ajax/concept/editcomponents');
define('URL_AJAX_CONCEPT_GET_COMPONENTS_DESC','sbom/ajax/concept/getcomponentsdesc');


define('URL_PROJECT_FINAL_ADD','project/add/final');
define('URL_PROJECT_FINAL_EDIT','project/edit/final');

define('URL_LEARN','learning-center');
define('URL_LEARN_SECTIONS','learning-center');
define('URL_HOMEPAGE','homepage');

define('URL_LOGIN','login');
define('URL_HELP_VIEW','helpview');
define('URL_NODE_VIEW','nodeview');
//define('URL_','');
/* Add Process:
 * arg3: componentID
 * arg4: itemID
 * arg5: phaseID
 */


//Includes
// include_once('./'.SM_PATH.'/actions.inc');
// include_once('./'.SM_PATH.'/products.inc');
// include_once('./'.SM_PATH.'/mp.inc');
// include_once('./'.SM_PATH.'/cc.inc');
// include_once('./'.SM_PATH.'/security.inc');
// include_once('./'.SM_PATH.'/themes.inc');
// include_once('./'.SM_PATH.'/result.inc');
// include_once('./'.SM_PATH.'/dhtmlx/ajax.inc');
// include_once('./'.SM_PATH.'/dataload.inc');
// include_once('./'.SM_PATH.'/xml_ajax.inc');
// include_once('./'.SM_PATH.'/phases/phases_tg_ajax_edit.inc');
// include_once('./'.SM_PATH.'/phases/phases_tg_ajax_get.inc');
// include_once('./'.SM_PATH.'/phases/phases_tg.inc');
// include_once('./'.SM_PATH.'/popuphint/popuphint.inc');
//include_once('./'.SM_PATH.'/bom_load.inc');


//Other Defined variabels

define('IMPACT_TO_IGNORE', 10);

define('SESSION_CONCEPT_ADD','concept_create');






//Defaults and Alerts
define('TEXT_ERROR_PROJECT_NAME', '<strong>A unique project name is required. </strong><br/><br/>Consider a name based on the company, brand, product model or an internal identifier. Names may contain special characters and be any length.');
define('TEXT_ERROR_PROJECT_FUNAME', '<strong>A functional unit is required for each project. </strong>');

define('TEXT_DEFAULT_PROJECT_DESCRIPTION', 'The project description has not been provided.'); //The description is meant to summarize project background info, design brief and ecodesign strategies.<br/><a href="#">More about the project description ></a>
define('TEXT_DEFAULT_PROJECT_CONCEPTS', 'A reference concept has not been designated.<br/><a href="#">More about the reference concept and comparing product concepts ></a>');
define('TEXT_DEFAULT_PROJECT_CONCEPTS_DESCRIPTION', 'The concept description has not been provided.');
define('TEXT_DEFAULT_PROJECT_GOALS_COMPANY', 'Company sustainability goals have not yet been described. To add information, edit the assessment goals.');//<br/>Describe sustainability policies and goals as they relate to products and product development.<br/><a href="#">More about goal-setting ></a>
define('TEXT_DEFAULT_PROJECT_GOALS_PROJECT', 'Project sustainability goals have not yet been described. To add information, edit the assessment goals.');//<br/><a href="#">More about goal-setting ></a>

define('TEXT_DEFAULT_SBOM_EMPTY_MAN', 'You have not created any manufacturing parts or sub-assemblies yet.');
define('TEXT_DEFAULT_SBOM_EMPTY_TRAN', 'You have not added any transport modes yet.');
define('TEXT_DEFAULT_SBOM_EMPTY_EOL', TEXT_DEFAULT_SBOM_EMPTY_MAN . ' You cannot add end of life to nothing.');

define('TEXT_ERROR_ITEM_SELECT_MATPROC', '<strong>A material or process selection is required.</strong>');
define('TEXT_ERROR_ITEM_NUMERIC_AMOUNT', '<strong>Amounts must be numeric.</strong>');
define('TEXT_ERROR_ITEM_NOT_NUMERIC_QUANTITY', '<strong>Quantity must be numeric.</strong>');
define('TEXT_ERROR_ITEM_SET_AMOUNT', '<strong>SBOM inputs require a numeric value.</strong>');
define('TEXT_ERROR_CONCEPT_NAME','<strong>A name for your concept is required.</strong> Alphanumeric characters are acceptable.');
//define('TEXT_ERROR_CONCEPT_LIFETIME', '<strong>Specifying concept lifetime is required.</strong><br/>Concept lifetime is the number of total years product will be used in its lifetime.');
define('TEXT_ERROR_CONCEPT_NUMERIC_LIFETIME', 'Service amounts must be numeric.<br/>');
define('TEXT_ERROR_CONCEPT_FUNCUNIT','<strong>The amount of service delivered is required for each concept</strong>.');
define('TEXT_ERROR_CONCEPT_FUNCUNIT_ZERO','<strong>The amount of service delivered must be greater than 0</strong>.');
define('TEXT_ERROR_CONCEPT_NUMERIC_FUNCUNIT','<strong>The service amount must be numeric.</strong></a>');
//define('TEXT_DEFAULT_PROJECT_CONCEPT_LIST', 'Concepts have not been created for this project.');
define('TEXT_DEFAULT_PROJECT_CONCEPT_LIST_INTRO','Product concepts are "what-if" scenarios of the product being designed.');
define('TEXT_DEFAULT_PROJECT_CONCEPT_LIST_REF','<strong>The reference concept</strong><br />The first product concept you create automatically becomes the reference concept, which serves as the baseline for comparison. The reference is often an existing product that is being improved or redesigned. You can copy and edit the reference to create your subsequent product concepts. After creating other concepts, you may designate any other as the reference.');
define('TEXT_DEFAULT_PROJECT_CONCEPT_LIST_FINAL','<strong>The final concept</strong><br /> The concept with superior overall environmental performance, or lowest score, will continuously be updated as the best concept, as you create and compare concepts. When you are done, you can designate one concept as the final concept for the project, which depending on the trade-off decisions you and your team make, may not always be the one with the lowest score.');
define('TEXT_DEFAULT_CONCEPT_DESCRIPTION', 'A description has not been provided for this concept.');
define('TEXT_DEFAULT_PROJECT_CLIENT', '(not entered)');
define('TEXT_DEFAULT_SBOM_INPUT', 'No inputs have been added to this SBOM.<br/><a href="#">More about building a System Bill of Materials (SBOM) ></a>');
define('TEXT_ERROR_COMPONENT_NAME', '<strong>A sub-assembly name is required.</strong>');
//define('TEXT_DEFAULT_RESULT_REFERENCE', 'This concept is the reference for this project. The reference is a baseline to which other concepts in this project will be compared.<br/><br/><a href="JavaScript:void(0);" onclick=\'return node_popup("/helpview/32","")\'>Learn more about references ></a>');
define('TEXT_DEFAULT_RESULT_REFERENCE', 'The first concept created in a project is, by default, the <strong>reference concept</strong>. The reference is the baseline that other concepts in this project will be compared to.');
define('TEXT_DEFAULT_RESULT_REFERENCE_NOLINK', 'This concept is the reference for this project. The reference is a baseline to which other concepts in this project will be compared.');
//define('TEXT_DEFAULT_RESULT_REFERENCE_ADD', 'This concept is the reference for this project. The reference is a baseline to which other concepts in this project will be compared.<br/><br/>The first concept created in a project is the reference by default. You may later designate a different concept as the reference.<br/><br/><a href="JavaScript:void(0);" onclick=\'return node_popup("/helpview/32","")\'>Learn more about references ><a/>');
define('TEXT_DEFAULT_RESULT_REFERENCE_ADD', 'The first concept you create is by default your reference concept, which you use as the baseline for comparison and benchmarking analysis. You may also designate any other concept you create as the reference. After you create other concepts you can designate any of those concepts as the reference. A reference is often an existing product that is being improved or redesigned.');
define('TEXT_DEFAULT_RESULT_MISSING', 'This results view will be available in a future release.');

// defualt titles for sbom pages
define('TITLE_RESULT_SCORECARD','Scorecard');
define('TITLE_RESULT_COMPONENT_LIFETIME','Total');
define('TITLE_RESULT_COMPONENT_CO2','Carbon footprint');
define('TITLE_RESULT_COMPONENT_CATEGORIES','Impact category indicator');
define('TITLE_RESULT_LIFECYCLE_PHASE','Total');
define('TITLE_RESULT_LIFECYCLE_CO2','Carbon footprint');
define('TITLE_RESULT_LIFECYCLE_CATEGORIES','Impact categories');
define('TITLE_RESULT_SBOM','System BOM');


// display messages for missing items on results pages
define('SCORE_NO_FUNCUNITSTOTAL','Incomplete data');
define('SCORE_NO_LIFETIMETOTAL','Incomplete data');
define('SCORE_NO_PHASE','Incomplete data');
define('SCORE_NO_MEASURETYPE','Incomplete data');
define('SCORE_NO_HIGHCAT','Incomplete data');
define('SCORE_NO_HIGHINPUT','Incomplete data');

//Pop up nodes//


// variable_set('sm_popup_impact_1', 59);//        |        1 | Fossil Fuel          |             |
// variable_set('sm_popup_impact_2', 63);//        |        2 | Smog                 |             |
// variable_set('sm_popup_impact_3', 57);//        |        3 | Ozone Depletion      |             |
// variable_set('sm_popup_impact_4', 55);//        |        4 | Ecotoxicity          |             |
// variable_set('sm_popup_impact_5', 58);//        |        5 | Water Eutrophication |             |
// variable_set('sm_popup_impact_6', 61);//        |        6 | Human respiratory    |             |
// variable_set('sm_popup_impact_7', 60);//        |        7 | Human Carcinogens    |             |
// variable_set('sm_popup_impact_8', 54);//        |        8 | Acidification        |             |
// variable_set('sm_popup_impact_9', 62);//        |        9 | Human Toxicity       |             |
// variable_set('sm_popup_impact_10', 53);//       |       10 | Global Warming       |             |
// variable_set('sm_popup_impact_11', 64);//       |       11 | CO2 Equivalent       |

// variable_set('sm_popup_impact_pre2013_1', 59);//        |        1 | Fossil Fuel Depletion|             |
// variable_set('sm_popup_impact_pre2013_2', 63);//        |        2 | Smog                 |             |
// variable_set('sm_popup_impact_pre2013_3', 57);//        |        3 | Ozone Depletion      |             |
// variable_set('sm_popup_impact_pre2013_4', 55);//        |        4 | Ecotoxicity          |             |
// variable_set('sm_popup_impact_pre2013_5', 58);//        |        5 | Eutrophication       |             |
// variable_set('sm_popup_impact_pre2013_6', 61);//        |        6 | Respiratory effects  |             |
// variable_set('sm_popup_impact_pre2013_7', 60);//        |        7 | Carcinogenics        |             |
// variable_set('sm_popup_impact_pre2013_8', 54);//        |        8 | Acidification        |             |
// variable_set('sm_popup_impact_pre2013_9', 62);//        |        9 | Non carcinogenics    |             |
// variable_set('sm_popup_impact_pre2013_10', 53);//       |       10 | Global Warming       |             |
// variable_set('sm_popup_impact_pre2013_11', 64);//       |       11 | CO2 Equivalent       |

/*
Button Labels for forms
*/
define('BUTTON_LABEL_SYSTEM_BOM','Build System BOM');
define('BUTTON_LABEL_SAVE','Save');
define('BUTTON_LABEL_SAVE_EXIT','Save and exit');
define('BUTTON_LABEL_CANCEL','Cancel');
define('BUTTON_LABEL_NEXT','Next');
define('BUTTON_LABEL_BACK','Back');
define('BUTTON_LABEL_ADD_TO_SBOM','Add to SBOM');
define('BUTTON_LABEL_COPY_PROJECT','Copy Project');

define('CHART_PACHAGE_TO_USE',2); // 0 for open_flash, 1 for netcharts, 2 for fusion

define('SM_CURRENT_RELEASE','Alpha');
define('MPT_UNIT_LABEL','mPt.');
define('MPTS_UNIT_LABEL','mPts');
//This one needs to go away because okala/hr is no longer the only option
define('MPTS_HOURS_USE_LABEL',MPTS_UNIT_LABEL.'/func unit');
define('PER_FU_LABEL', '/func unit');

define('CO2_EQ_LABEL','CO<sub>2</sub> eq. kg');


define('ROUND_AMOUNT',3);

// defualt texts for add component/item buttons, use
define('DEFUALT_COMPONENT_TEXT','Sub-assembly');
define('DEFUALT_ITEM_TEXT','Part');
define('DEFUALT_PROCESS_TEXT','Process');
define('DEFUALT_PROCESS_EOL_TEXT','End of life method');
define('DEFAULT_SBOM_ICON_DELETE_URL','/sites/all/themes/sm_theme_01/images/sbom/sm_delete.png');
define('DEFAULT_SBOM_ICON_DELETE','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_delete.png" />');
define('DEFAULT_SBOM_ICON_EDIT','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_edit.png" />');
define('DEFAULT_SBOM_ICON_COPY','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_copy.png" />'); // new - KJH
define('DEFAULT_SBOM_ICON_COPY_URL','/sites/all/themes/sm_theme_01/images/sbom/sm_copy.png'); // new - KJH
define('DEFAULT_SBOM_ICON_ADDPART','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_part.png" />');
define('DEFAULT_SBOM_ICON_ADDSA','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_sasmbly.png" />');
define('DEFAULT_SBOM_ICON_ADDPROC','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_proc.png" />');
define('DEFAULT_SBOM_ICON_ADDPROC_D','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_proc_disabled.png" />');
define('DEFAULT_SBOM_ICON_ADDMETHOD','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_method.png" />');
define('DEFAULT_SBOM_ICON_ADDMETHOD_D','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_method_disabled.png" />');
define('DEFAULT_SBOM_ICON_ADDMODE','not using yet look in init and phases_get');
define('DEFAULT_SBOM_ICON_ADDTRANSPORTATION','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_transportation.png" />');
define('DEFAULT_SBOM_ICON_ADDMODE_D','not using yet look in init and phases_get');
define('DEFAULT_SBOM_ICON_ADDCONSUME','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_consumables.png" />');
define('DEFAULT_SBOM_ICON_ADDWATER','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_water_use.png" />');
define('DEFAULT_SBOM_ICON_ADDPOWER','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_power_use.png" />');

//default images for tree-actions
define('DEFAULT_TREEACTION_ADDPART','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_a_part.png" alt="Add a Part" />');
define('DEFAULT_TREEACTION_IMPORTBOM','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_import_bom.png" alt="Import Bom" />');
define('DEFAULT_TREEACTION_IMPORTBOMPREV','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_previous_imports.png" alt="Previous Imports" />');
define('DEFAULT_TREEACTION_ADDSA','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_addasubassembly.png" alt="Add Sub-Assembly" />');
define('DEFAULT_TREEACTION_ADDTRANSPORTATION','<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_transportationforpro.png" alt="Add Transportation for the entire product" />');

// defualt for project edits
define('DEFAULT_PROJECT_ICON_EDIT_VIEW','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_overview.png" />');
define('DEFAULT_PROJECT_ICON_EDIT_GOALS','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_assess_goals.png" />');
define('DEFAULT_PROJECT_ICON_EDIT_SCOPE','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_assess_scope.png" />');
define('DEFAULT_PROJECT_ICON_DELETE','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_delete_project.png" alt="Delete"/>');

define('DEFAULT_PROJECT_CONCEPT_ICON_CREATE','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_create.png"/>');
// defualt for project concept list actions
define('DEFAULT_PROJECT_CONCEPT_ICON_COPY','/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_copy.png');
define('DEFAULT_PROJECT_CONCEPT_ICON_COPYANDUPDATE','/sites/all/themes/sm_theme_01/images/buttons/project_copy_update_methodo.png');
define('DEFAULT_PROJECT_CONCEPT_ICON_DELETE','/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_delete.png');
define('DEFAULT_PROJECT_CONCEPT_ICON_REFERENCE','/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_ref.png');
define('DEFAULT_PROJECT_CONCEPT_ICON_FINAL','/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_fin.png');
define('DEFAULT_PROJECT_CONCEPT_ICON_EDIT_OVERVIEW','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_edit_overview.png"/>');


define('DEFAULT_PROJECT_ICON_SETUP','<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_setup_project.png"/>');
// project page names

define('PROJECT_PAGE_NAME_DEFINITION','definition'); // edit name of view
// drupal_add_js("PROJECT_PAGE_NAME_DEFINITION = '" . PROJECT_PAGE_NAME_DEFINITION . "';", 'inline');
define('PROJECT_PAGE_NAME_VIEW','view');
define('PROJECT_PAGE_NAME_SCOPE','scope');
define('PROJECT_PAGE_NAME_GOALS','goals');
define('PROJECT_PAGE_NAME_CONCEPTS','concepts');
define('PROJECT_PAGE_NAME','Part');

