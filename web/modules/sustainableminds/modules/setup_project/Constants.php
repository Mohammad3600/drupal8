<?php

namespace Drupal\setup_project;
class Constants{
    // Default image if none is provided
    const SITE_PATH = '/drupal8/web';
    const IMAGE_NO_IMAGE_DEFAULT =  self::SITE_PATH.'/sites/default/files/2021-07/no_scope.gif';
    const DEFAULT_IMAGE =   self::SITE_PATH.'/sites/default/files/2021-07/no_scope.gif';
    const DEFAULT_SCOPE_IMAGE =  self::SITE_PATH.'/sites/default/files/2021-07/no_scope.gif';

    //Phases PHP
    const PHASEID_MANUFACTURE =  1;
    const PHASEID_USE =  2;
    const PHASEID_TRANSPORT =  3;
    const PHASEID_EOL =  4;

    //limits for using scientific notation
    const SN_LOWER =  .001; //use s.n. for values <= this
    const SN_UPPER =  1000; //use s.n. for values >= this

    //Types of component
    const COMPONENT_ROOT_MAN =  1;
    const COMPONENT_ROOT_TRA =  2;
    const COMPONENT_ROOT_USE =  3;
    const COMPONENT_ROOT_CON =  4;
    const COMPONENT_ROOT_POW =  5;
    const COMPONENT_ROOT_WAT =  6;
    const COMPONENT_TYPE_CHECK =  1;
    const TOTAL_TYPE_CHECK =  7;
    const EMPTY_TYPE_CHECK =  8;
    const ITEM_TYPE_CHECK =  2;
    const ITEM_NO_PROC_TYPE_CHECK =  3;
    const ITEM_WITH_PROC_TYPE_CHECK =  4;
    const PROCESS_TYPE_CHECK =  5;
    const ONLY_TRANS_SCORES =  1;
    const BOMLOAD_IMPORT_STATE_MISSING =  1;
    const BOMLOAD_IMPORT_STATE_READY =  2;
    const BOMLOAD_IMPORT_STATE_IMPORTED =  3;
    const LIFETIME_UNITS = 'years';
    const SM_PROCESS_CATEGORY_ID =  11;
    const SM_EOL_CATEGORY_ID =  11;
    const SM_CONSUMABLE_CATEGORY_ID =  7;
    const SM_TRANSPORTATION_CATEGORY_ID =  4;
    const SM_MATERIAL_CATEGORY_ID =  1;
    const SM_CONSUMABLE_CATEGORY_ID2 =  8;
    const SM_WATER_CATEGORY_ID =  10;
    const SM_POWER_CATEGORY_ID =  9;
    const SM_ROOT_PRIVATE_CAT_ID =  820;
    const SM_PRIVATE_CAT_TYPE =  'private';
    const SM_MATERIALS_CAT_TYPE =  'materials';
    const SM_PRODUCTS_CAT_TYPE =  'products';
    const SM_CATEGORY_TREE_SYSTEM_OWNER =  1000000;
    const SM_PRIVATE_DATA_SET_NODE_ID =  892;
    const SM_NONWOVENS_NODE_ID =  925;
    const URL_ADMIN_SETTINGS_PARENT = 'admin/settings/sm';
    const URL_ADMIN_SETTINGS = 'admin/settings/sm/general';
    const URL_ADMIN_SETTINGS_DB = 'admin/settings/sm/db';
    const URL_PROJECT = 'project';
    const URL_PROJECT_LIST_USER = 'project/list/user';
    const URL_PROJECT_LIST = 'project/list';
    const URL_PROJECT_VIEW = 'project/view';
    const URL_PROJECT_CONCEPTS = 'project/concepts';
    const URL_PROJECT_SCOPE = 'project/scope';
    const URL_PROJECT_GOAL = 'project/goal';
    const URL_PROJECT_ADD = 'project/add';
    const URL_AJAX_PROJECT_NEW = 'ajax/actions/newproject';
    const URL_PROJECT_EDIT = 'project/edit';
    const URL_CONCEPT_VIEW = 'project/concept/view';
    const URL_CONCEPT_BUILD = 'project/concept/build';
    const URL_CONCEPT_ADD = 'project/concept/add';
    const URL_CONCEPT_EDIT = 'project/concept/edit';
    const URL_CONCEPT_RESULT = 'project/concept/result';
    const SUBURL_CONCEPT_RESULT_SCORECARD = 'scorecard';
    const SUBURL_CONCEPT_RESULT_COMPONENT_LIFETIME = 'input/total';
    const SUBURL_CONCEPT_RESULT_COMPONENT_CO2 = 'input/co2';
    const SUBURL_CONCEPT_RESULT_COMPONENT_CATEGORIES = 'input/impacts';
    const SUBURL_CONCEPT_RESULT_LIFECYCLE_PHASE = 'lifecycle/total';
    const SUBURL_CONCEPT_RESULT_LIFECYCLE_CO2 = 'lifecycle/co2';
    const SUBURL_CONCEPT_RESULT_LIFECYCLE_CATEGORIES = 'lifecycle/impacts';
    const SUBURL_CONCEPT_RESULT_SBOM = 'bom';
    const URL_CREATE_NEW_PRODUCT =  'project/add';
    const URL_ADD_COMPONENT =  'project/component/add';
    const URL_EDIT_COMPONENT =  'project/component/edit';
    const URL_ADD_MANUFACTURING_MATERIAL =  'project/material/add';
    const URL_EDIT_MANUFACTURING_MATERIAL =  'project/material/edit';
    const URL_ADD_PROCESS =  'project/process/add';
    const URL_EDIT_PROCESS =  'project/process/edit';
    const URL_BOMLOAD_FILE =  'project/concept/upload';
    const URL_BOMLOAD_APPROVE =  'project/concept/import';
    const URL_BOMLOAD_APPROVE_MATERIAL =  'project/material/import';
    const URL_BOMLOAD_APPROVE_TRANSPORTATION =  'project/transportation/import';
    const URL_BOMLOAD_APPROVE_PROCESS =  'project/process/import';
    const URL_BOMLOAD_DELETE =  'sbom/ajax/import/delete';
    const URL_AJAX_BOMLOAD_APPROVE_RECORD =  'ajax/actions/bomload/approve_record';
    const URL_AJAX_BOMLOAD_APPROVE_RECORDS =  'ajax/actions/bomload/approve_records';
    const URL_AJAX_BOMLOAD_SET_MAPPEDMATPROCID =  'ajax/actions/bomload/set_mappedmatprocid';
    const URL_AJAX_BOMLOAD_GET_POSSIBLE_MATCHES =  'ajax/actions/bomload/get_matches';
    const URL_AJAX_BOMLOAD_LIST_APPROVE =  'ajax/actions/bomload/list_approve';
    const URL_BOMLOAD_VERIFICATION =  'ajax/actions/bomload/verification';
    const URL_AJAX_SET_EDIT = 'ajax/actions/set_edit';
    const URL_AJAX_UNSET_EDIT = 'ajax/actions/unset_edit';
    const URL_AJAX_FINISH_CONCEPT_ADD = 'ajax/actions/finish_concept_add';
    const URL_AJAX_COPY_CONCEPT =  'ajax/actions/copy';
    const URL_AJAX_SET_REFERENCE = 'ajax/actions/set_reference';
    const URL_AJAX_SET_FINAL = 'ajax/actions/set_final';
    const URL_AJAX_DELETE_CONCEPT = 'ajax/actions/delete_concept';
    const URL_AJAX_COPY_PROJECT = 'ajax/actions/copy_project';
    const URL_AJAX_COPY_UPDATE_PROJECT = 'ajax/actions/copy_update_project';
    const URL_AJAX_DELETE_PROJECT = 'ajax/actions/delete_project';
    const URL_AJAX_SET_RESULT_LABEL_OPTION = 'ajax/actions/set_result_label_option';
    const URL_AJAX_SET_UNIT_NAME = 'ajax/actions/set_unit_name';
    const URL_AJAX_COPY_SBOM_ITEM =  'ajax/actions/copy_sbom_item';  // KJH - copy feature
    const URL_AJAX_COPY_SBOM_COMPONENT =  'ajax/actions/copy_sbom_component'; // KJH - copy feature
    const URL_AJAX_GET_ITEM = 'sbom/ajax/getitem';
    const URL_AJAX_GET_CATEGORY = 'sbom/ajax/getcategory';
    const URL_AJAX_GET_MATERIAL_SINGLE = 'sbom/ajax/getmaterialsingle';
    const URL_AJAX_CONCEPT_GET_COMPONENTS = 'sbom/ajax/concept/getcomponents';
    const URL_AJAX_CONCEPT_SETUP_BOM = 'sbom/ajax/concept/setup/bom';
    const URL_AJAX_CONCEPT_GET_PROCESSES = 'sbom/ajax/concept/getprocesses';
    const URL_AJAX_CONCEPT_EDIT_COMPONENTS = 'sbom/ajax/concept/editcomponents';
    const URL_AJAX_CONCEPT_GET_COMPONENTS_DESC = 'sbom/ajax/concept/getcomponentsdesc';
    const URL_PROJECT_FINAL_ADD = 'project/add/final';
    const URL_PROJECT_FINAL_EDIT = 'project/edit/final';
    const URL_LEARN = 'learning-center';
    const URL_LEARN_SECTIONS = 'learning-center';
    const URL_HOMEPAGE = 'homepage';
    const URL_LOGIN = 'login';
    const URL_HELP_VIEW = 'helpview';
    const URL_NODE_VIEW = 'nodeview';
    const IMPACT_TO_IGNORE =  10;
    const SESSION_CONCEPT_ADD = 'concept_create';
    //Defaults and Alerts
    const TEXT_ERROR_PROJECT_NAME =  '<strong>A unique project name is required. </strong><br/><br/>Consider a name based on the company, brand, product model or an internal identifier. Names may contain special characters and be any length.';
    const TEXT_ERROR_PROJECT_FUNAME =  '<strong>A functional unit is required for each project. </strong>';
    const TEXT_DEFAULT_PROJECT_DESCRIPTION =  'The project description has not been provided.'; //The description is meant to summarize project background info, design brief and ecodesign strategies.<br/><a href="#">More about the project description ></a>
    const TEXT_DEFAULT_PROJECT_CONCEPTS =  'A reference concept has not been designated.<br/><a href="#">More about the reference concept and comparing product concepts ></a>';
    const TEXT_DEFAULT_PROJECT_CONCEPTS_DESCRIPTION =  'The concept description has not been provided.';
    const TEXT_DEFAULT_PROJECT_GOALS_COMPANY =  'Company sustainability goals have not yet been described. To add information, edit the assessment goals.';//<br/>Describe sustainability policies and goals as they relate to products and product development.<br/><a href="#">More about goal-setting ></a>
    const TEXT_DEFAULT_PROJECT_GOALS_PROJECT =  'Project sustainability goals have not yet been described. To add information, edit the assessment goals.';//<br/><a href="#">More about goal-setting ></a>
    const TEXT_DEFAULT_SBOM_EMPTY_MAN =  'You have not created any manufacturing parts or sub-assemblies yet.';
    const TEXT_DEFAULT_SBOM_EMPTY_TRAN =  'You have not added any transport modes yet.';
    const TEXT_DEFAULT_SBOM_EMPTY_EOL =  self::TEXT_DEFAULT_SBOM_EMPTY_MAN . ' You cannot add end of life to nothing.';
    const TEXT_ERROR_ITEM_SELECT_MATPROC =  '<strong>A material or process selection is required.</strong>';
    const TEXT_ERROR_ITEM_NUMERIC_AMOUNT =  '<strong>Amounts must be numeric.</strong>';
    const TEXT_ERROR_ITEM_NOT_NUMERIC_QUANTITY =  '<strong>Quantity must be numeric.</strong>';
    const TEXT_ERROR_ITEM_SET_AMOUNT =  '<strong>SBOM inputs require a numeric value.</strong>';
    const TEXT_ERROR_CONCEPT_NAME = '<strong>A name for your concept is required.</strong> Alphanumeric characters are acceptable.';
    const TEXT_ERROR_CONCEPT_NUMERIC_LIFETIME =  'Service amounts must be numeric.<br/>';
    const TEXT_ERROR_CONCEPT_FUNCUNIT = '<strong>The amount of service delivered is required for each concept</strong>.';
    const TEXT_ERROR_CONCEPT_FUNCUNIT_ZERO = '<strong>The amount of service delivered must be greater than 0</strong>.';
    const TEXT_ERROR_CONCEPT_NUMERIC_FUNCUNIT = '<strong>The service amount must be numeric.</strong></a>';
    const TEXT_DEFAULT_PROJECT_CONCEPT_LIST_INTRO = 'Product concepts are "what-if" scenarios of the product being designed.';
    const TEXT_DEFAULT_PROJECT_CONCEPT_LIST_REF = '<strong>The reference concept</strong><br />The first product concept you create automatically becomes the reference concept, which serves as the baseline for comparison. The reference is often an existing product that is being improved or redesigned. You can copy and edit the reference to create your subsequent product concepts. After creating other concepts, you may designate any other as the reference.';
    const TEXT_DEFAULT_PROJECT_CONCEPT_LIST_FINAL = '<strong>The final concept</strong><br /> The concept with superior overall environmental performance, or lowest score, will continuously be updated as the best concept, as you create and compare concepts. When you are done, you can designate one concept as the final concept for the project, which depending on the trade-off decisions you and your team make, may not always be the one with the lowest score.';
    const TEXT_DEFAULT_CONCEPT_DESCRIPTION =  'A description has not been provided for this concept.';
    const TEXT_DEFAULT_PROJECT_CLIENT =  '(not entered)';
    const TEXT_DEFAULT_SBOM_INPUT =  'No inputs have been added to this SBOM.<br/><a href="#">More about building a System Bill of Materials (SBOM) ></a>';
    const TEXT_ERROR_COMPONENT_NAME =  '<strong>A sub-assembly name is required.</strong>';
    const TEXT_DEFAULT_RESULT_REFERENCE =  'The first concept created in a project is, by default, the <strong>reference concept</strong>. The reference is the baseline that other concepts in this project will be compared to.';
    const TEXT_DEFAULT_RESULT_REFERENCE_NOLINK =  'This concept is the reference for this project. The reference is a baseline to which other concepts in this project will be compared.';
    const TEXT_DEFAULT_RESULT_REFERENCE_ADD =  'The first concept you create is by default your reference concept, which you use as the baseline for comparison and benchmarking analysis. You may also designate any other concept you create as the reference. After you create other concepts you can designate any of those concepts as the reference. A reference is often an existing product that is being improved or redesigned.';
    const TEXT_DEFAULT_RESULT_MISSING =  'This results view will be available in a future release.';
    const TITLE_RESULT_SCORECARD = 'Scorecard';
    const TITLE_RESULT_COMPONENT_LIFETIME = 'Total';
    const TITLE_RESULT_COMPONENT_CO2 = 'Carbon footprint';
    const TITLE_RESULT_COMPONENT_CATEGORIES = 'Impact category indicator';
    const TITLE_RESULT_LIFECYCLE_PHASE = 'Total';
    const TITLE_RESULT_LIFECYCLE_CO2 = 'Carbon footprint';
    const TITLE_RESULT_LIFECYCLE_CATEGORIES = 'Impact categories';
    const TITLE_RESULT_SBOM = 'System BOM';


    // display messages for missing items on results pages
    const SCORE_NO_FUNCUNITSTOTAL = 'Incomplete data';
    const SCORE_NO_LIFETIMETOTAL = 'Incomplete data';
    const SCORE_NO_PHASE = 'Incomplete data';
    const SCORE_NO_MEASURETYPE = 'Incomplete data';
    const SCORE_NO_HIGHCAT = 'Incomplete data';
    const SCORE_NO_HIGHINPUT = 'Incomplete data';

    /*
    Button Labels for forms
    */
    const BUTTON_LABEL_SYSTEM_BOM = 'Build System BOM';
    const BUTTON_LABEL_SAVE = 'Save';
    const BUTTON_LABEL_SAVE_EXIT = 'Save and exit';
    const BUTTON_LABEL_CANCEL = 'Cancel';
    const BUTTON_LABEL_NEXT = 'Next';
    const BUTTON_LABEL_BACK = 'Back';
    const BUTTON_LABEL_ADD_TO_SBOM = 'Add to SBOM';
    const BUTTON_LABEL_COPY_PROJECT = 'Copy Project';
    const CHART_PACHAGE_TO_USE = 2; // 0 for open_flash, 1 for netcharts, 2 for fusion
    const SM_CURRENT_RELEASE = 'Alpha';
    const MPT_UNIT_LABEL = 'mPt.';
    const MPTS_UNIT_LABEL = 'mPts';

    //This one needs to go away because okala/hr is no longer the only option
    const MPTS_HOURS_USE_LABEL = MPTS_UNIT_LABEL.'/func unit';
    const PER_FU_LABEL =  '/func unit';
    const CO2_EQ_LABEL = 'CO<sub>2</sub> eq. kg';
    const ROUND_AMOUNT = 3;

    // defualt texts for add component/item buttons, use
    const DEFUALT_COMPONENT_TEXT = 'Sub-assembly';
    const DEFUALT_ITEM_TEXT = 'Part';
    const DEFUALT_PROCESS_TEXT = 'Process';
    const DEFUALT_PROCESS_EOL_TEXT = 'End of life method';
    const DEFAULT_SBOM_ICON_DELETE_URL = '/sites/all/themes/sm_theme_01/images/sbom/sm_delete.png';
    const DEFAULT_SBOM_ICON_DELETE = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_delete.png" />';
    const DEFAULT_SBOM_ICON_EDIT = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_edit.png" />';
    const DEFAULT_SBOM_ICON_COPY = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_copy.png" />'; // new - KJH
    const DEFAULT_SBOM_ICON_COPY_URL = '/sites/all/themes/sm_theme_01/images/sbom/sm_copy.png'; // new - KJH
    const DEFAULT_SBOM_ICON_ADDPART = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_part.png" />';
    const DEFAULT_SBOM_ICON_ADDSA = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_sasmbly.png" />';
    const DEFAULT_SBOM_ICON_ADDPROC = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_proc.png" />';
    const DEFAULT_SBOM_ICON_ADDPROC_D = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_proc_disabled.png" />';
    const DEFAULT_SBOM_ICON_ADDMETHOD = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_method.png" />';
    const DEFAULT_SBOM_ICON_ADDMETHOD_D = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_plus_method_disabled.png" />';
    const DEFAULT_SBOM_ICON_ADDMODE = 'not using yet look in init and phases_get';
    const DEFAULT_SBOM_ICON_ADDTRANSPORTATION = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_transportation.png" />';
    const DEFAULT_SBOM_ICON_ADDMODE_D = 'not using yet look in init and phases_get';
    const DEFAULT_SBOM_ICON_ADDCONSUME = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_consumables.png" />';
    const DEFAULT_SBOM_ICON_ADDWATER = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_water_use.png" />';
    const DEFAULT_SBOM_ICON_ADDPOWER = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_power_use.png" />';

    //default images for tree-actions
    const DEFAULT_TREEACTION_ADDPART = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_a_part.png" alt="Add a Part" />';
    const DEFAULT_TREEACTION_IMPORTBOM = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_import_bom.png" alt="Import Bom" />';
    const DEFAULT_TREEACTION_IMPORTBOMPREV = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_previous_imports.png" alt="Previous Imports" />';
    const DEFAULT_TREEACTION_ADDSA = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_addasubassembly.png" alt="Add Sub-Assembly" />';
    const DEFAULT_TREEACTION_ADDTRANSPORTATION = '<img src="/sites/all/themes/sm_theme_01/images/sbom/sm_add_transportationforpro.png" alt="Add Transportation for the entire product" />';

    // defualt for project edits
    const DEFAULT_PROJECT_ICON_EDIT_VIEW = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_overview.png" />';
    const DEFAULT_PROJECT_ICON_EDIT_GOALS = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_assess_goals.png" />';
    const DEFAULT_PROJECT_ICON_EDIT_SCOPE = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_edit_project_assess_scope.png" />';
    const DEFAULT_PROJECT_ICON_DELETE = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_delete_project.png" alt="Delete"/>';
    const DEFAULT_PROJECT_CONCEPT_ICON_CREATE = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_create.png"/>';
    
    // defualt for project concept list actions
    const DEFAULT_PROJECT_CONCEPT_ICON_COPY = '/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_copy.png';
    const DEFAULT_PROJECT_CONCEPT_ICON_COPYANDUPDATE = '/sites/all/themes/sm_theme_01/images/buttons/project_copy_update_methodo.png';
    const DEFAULT_PROJECT_CONCEPT_ICON_DELETE = '/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_delete.png';
    const DEFAULT_PROJECT_CONCEPT_ICON_REFERENCE = '/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_ref.png';
    const DEFAULT_PROJECT_CONCEPT_ICON_FINAL = '/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_fin.png';
    const DEFAULT_PROJECT_CONCEPT_ICON_EDIT_OVERVIEW = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_project_concept_edit_overview.png"/>';
    const DEFAULT_PROJECT_ICON_SETUP = '<img src="/sites/all/themes/sm_theme_01/images/buttons/sm_setup_project.png"/>';

    // project page names
    const PROJECT_PAGE_NAME_DEFINITION = 'definition'; // edit name of view
    const PROJECT_PAGE_NAME_VIEW = 'view';
    const PROJECT_PAGE_NAME_SCOPE = 'scope';
    const PROJECT_PAGE_NAME_GOALS = 'goals';
    const PROJECT_PAGE_NAME_CONCEPTS = 'concepts';
    const PROJECT_PAGE_NAME = 'Part';
}