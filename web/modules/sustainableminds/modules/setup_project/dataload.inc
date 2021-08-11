<?php
// use Drupal\setup_project\Services\sbom_db;
include_once('./'.SM_PATH.'/dataload/load_cats.php');
include_once('./'.SM_PATH.'/dataload/load_matproc_alias.php');
include_once('./'.SM_PATH.'/dataload/load_matproc.php');
include_once('./'.SM_PATH.'/dataload/load_matproc_desc.php');
include_once('./'.SM_PATH.'/dataload/load_materials.php');
include_once('./'.SM_PATH.'/dataload/load_proc.php');

function sustainable_minds_dataload_view() {
  return drupal_get_form('sustainable_minds_dataload_view_form');
}
function sustainable_minds_dataload_files() {
  return drupal_get_form('sustainable_minds_dataload_files_form');
}
function sustainable_minds_dataload_load() {
  return drupal_get_form('sustainable_minds_dataload_load_form');
}
function sustainable_minds_dataload_del() {
  return drupal_get_form('sustainable_minds_dataload_del_form');
}
function sustainable_minds_dataload_edit() {
  return '';
}
function sustainable_minds_dataload_results() {
  return drupal_get_form('sustainable_minds_dataload_results_form');
}
function sustainable_minds_datasets() {
  return drupal_get_form('sustainable_minds_datasets_form');
}

function sustainable_minds_dataload_view_form() {
  //variable_set('sustainable_minds_dataload_files', array('data/parcatmat.txt'=>array('use'=>array('cats','matproc')) ));
  $files = variable_get('sustainable_minds_dataload_files', array());
  $form['#tree'] = TRUE;
  $types = sustainable_minds_dataload_types();
  
  // add display field here to show the data_set version
  
  $form[] = array(
    '#type' => 'markup',
    '#value'=> 'Run a full data load or individually run some files<br/>If a file is missing, upload it via file upload then assign it to a section via Manage file.<br/>');
    
  $form['full'] = array('#type' => 'submit', '#value' => t('Full Run'));
  
  foreach ($files as $key => $file) {
    $ops[$key] = $key;
  }
    
  foreach ($types as $type) {
    $form[$type['type']] = array(
      '#type' => 'fieldset', 
      '#collapsible' => TRUE, 
      '#title' => t( $type['title']),
      '#collapsed' => TRUE,
      'run' => array (
        '#title' => t('Run ' . $type['title'] ),
        '#type' => 'checkbox',
      )
    );

    $defop = array();
    $ops = array();
    foreach ($files as $key=>$file) {
      if (in_array($type['type'], $file['use'])) {
        $defop[] = $key;
        $ops[$key] = $key;
      }
    }
    
    $form[$type['type']]['files'] = array (
        '#title' => t('Files included in run '),
        '#type' => 'checkboxes',
        '#default_value' => $defop,
        '#options' => $ops
    );
    //$form[$type['type']]['submit'] = array('#type' => 'submit', '#value' => t('Run ' . $type['title']));
  }
  
  $form['submit'] = array('#type' => 'submit', '#value' => t('Run Selected'));
  $form[] = array(
    '#type' => 'markup',
    '#value'=>'<br/><br/><br/><br/><br/>Default Run should only be run on recreation of the site. It will clear all file information from these pages and run the default data files.<br/>'
  ); 
  $form['default'] = array('#type' => 'submit', '#value' => t('Default Run'));
  return $form;
}


function sustainable_minds_dataload_view_form_submit($form_id, $form) {
  $files = variable_get('sustainable_minds_dataload_files',array());
  $types = sustainable_minds_dataload_types(TRUE);  // bmagee - added reset=true
  $dbname = $_GET['db_name'] ? $_GET['db_name'] : DB_NAME_SBOM;
  if ($form['op'] == 'Full Run') {
    $ra = true;
  }
  
  if ($form['op'] != 'Default Run') {
    foreach($types as $type) {
      $t = $type['type'];
      if ($form[$t]['run'] || $ra) { //files/dataload_data/
        drupal_set_message(t('Doing '.$t));
        $files_use = array();
        foreach ($form[$t]['files'] as $f) {
          if ($f) { 
            $files_use[] = 'files/dataload_data/'. $f;
          }
        }
        
        $func = 'sustainable_minds_dataload_load_'.$t;
        
//        watchdog('dataload', 'Debug- function: ' . $func);

        if (!empty($files_use)) {
          $output = $func($files_use,$dbname);
          drupal_set_message(t($output));
        } else {
          drupal_set_message(t($t .' was skipped due to lack of files.'));
        }
      }
    }
  } else {
    // default run
    $default_files = 
      array(
        array('type'=>'cats','title'=>'Category', 'files'=>array(
          'parcatmat.txt'
          )
        ),
        array('type'=>'matproc','title'=>'MatProc', 'files'=>array(
          'energy A.txt',
          'metals.txt',
          'landfill.txt',
          'energy B.txt',
          'incinceration.txt',
          'last_bits.txt',
          'plastics.txt',
          'metal processing B Okala data.txt',
          'polymer forming.txt',
          'mp.txt',
          'sundry 1.txt',
          'sundry 2.txt',
          'sundry 3.txt',
          'sundry 4.txt',
          'sundry 9.txt',
          'sundry 10.txt',
          'sundry 11.txt',
          'sundry 12.txt',
          'sundry 13.txt',
          'sundry 6.txt',
          'sundry 7b.txt',
          'transportation.txt',
          'recycling.txt'
        )),
        array('type'=>'matproc_alias','title'=>'MatProc Alias', 'files'=>array(
          'matproc_alias.txt'
        )), 
        array('type'=>'categorization','title'=>'Categorization of material', 'files'=>array(
          'parcatmat.txt'
        )),
        array('type'=>'process','title'=>'Process to material', 'files'=>array(
          'proc.txt', 
          'Recycling_Process_Map.txt' 
        ))
      );
      
    foreach ($default_files as $type) {  
      $t = $type['type'];
      drupal_set_message(t('Doing '.$t));
      $files_use = array();
      foreach ($type['files'] as $f) {
        if ($f) {
          $files_use[] = 'dataload_data/'. $f;
        }
      }
      
      $func = 'sustainable_minds_dataload_load_'.$t;
      
      if (!empty($files_use)) {
        $output = $func($files_use,$dbname);
        drupal_set_message(t($output));
      } else {
        drupal_set_message(t($t .' was skipped due to lack of files.'));
      }
    }
    
    sustainable_minds_dataload_types(true);
    variable_set('sustainable_minds_dataload_types', $types);
    variable_set('sustainable_minds_dataload_files', array());
  }
  
  drupal_set_message(t('The run has completed. '));
  drupal_goto('admin/settings/sustainable_minds/dataload/results');
}


function sustainable_minds_dataload_files_form() {
  //variable_set('sustainable_minds_dataload_files', array('data/parcatmat.txt'=>array('use'=>array('cats','matproc')) ));
  $files = variable_get('sustainable_minds_dataload_files', array());
  $form['#tree'] = TRUE;
  /*$types = array( array('type'=>'cats','title'=>'Category'),
            array('type'=>'matproc','title'=>'MatProc'),
            array('type'=>'matproc_alias','title'=>'MatProc Alias'), 
            array('type'=>'categorization','title'=>'Categorization of material'),
            array('type'=>'process','title'=>'Process to material')
               );
  variable_set('sustainable_minds_dataload_types', $types);*/
  $types = sustainable_minds_dataload_types();
  foreach ($files as $key => $file) $ops[$key] = $key; 
  foreach ($types as $type) {
    $form[$type['type']] = array(
      '#type' => 'fieldset', 
      '#collapsible' => TRUE, 
      '#title' => t($type['title'].' Files'),
      '#collapsed' => TRUE
    );
    
    $defop = array();
    foreach ($files as $key=>$file) {
      if (in_array($type['type'], $file['use'])) {
        $defop[] = $key;
      }
    }
        
    $form[$type['type']]['files'] = array (
        '#title' => t('Files to use'),
        '#type' => 'checkboxes',
        '#default_value' => $defop,
        '#options' => $ops
    );
  }
  
  $form['submit'] = array('#type' => 'submit', '#value' => t('Save'),);
  return $form;
}


function sustainable_minds_dataload_files_form_submit($form_id, $edit) {
  $files = variable_get('sustainable_minds_dataload_files',array());
  $types = sustainable_minds_dataload_types();
  foreach ($files as $key => $file) {
    $use = array();
    foreach ($types as $type) {
      $t = $type['type'];
      if ($edit[$t]['files'][$key]) {
        $use[] = $t; 
      }
    }
    $files[$key]['use'] = $use; 
  }
  variable_set('sustainable_minds_dataload_files', $files);
  drupal_set_message(t('The changes have been saved.'));
}


function sustainable_minds_dataload_load_form() {
  $form['#tree'] = TRUE;
  $form['#attributes'] = array('enctype' => "multipart/form-data"); 
  $form['path'] = array('#type' => 'fieldset', '#collapsible' => TRUE, '#title' => t('Use an existing file'),'#collapsed' => TRUE);
  $form['path']['loc'] = array(
      '#type' => 'textfield', 
      '#title' => t('Path'),
      '#description'=>'Type in an name of a file that is under files/dataload_data'
    );
       
  //$form['file'] = array('#type' => 'fieldset', '#collapsible' => TRUE, '#title' => t('Upload a new file'));
  $form['file_up'] = array(
      '#type' => 'file', 
      '#title' => t('Or upload a file: '),
      '#description' => t('If file exists, it will replace the old file and retain section information.')
     );
  $form['hid'] = array(
      '#type' => 'hidden',
      '#value' => 1
     );
  $form['submit'] = array('#type' => 'submit', '#value' => t('Upload'),);
  return $form;
}


function sustainable_minds_dataload_load_form_submit($form_id, $form) {
  $files = variable_get('sustainable_minds_dataload_files', array());
  if ($form['path']['loc']) {
    if (!$files[$form['path']['loc']]) {
      $files[$form['path']['loc']] = array('use'=>array());
    }
  }
  
  if ($file = file_check_upload('file_up')) {
    $loc = $form['file']['loc'];
    if (file_save_upload('file_up', 'dataload_data/'. $file->filename, FILE_EXISTS_REPLACE)) {
      //$save = file_save_upload('font', $dir, true);
      if (empty($files[$file->filename] )) {
        $files[$file->filename] = array('use'=>array());
      }
    }
  }
  
  variable_set('sustainable_minds_dataload_files', $files);
  drupal_set_message(t('The changes have been saved.'));
}


function sustainable_minds_dataload_del_form() { 
  $files = variable_get('sustainable_minds_dataload_files',array());
  //$form['#tree'] = TRUE;
  foreach ($files as $key => $file) {
    $ops[$key] = $key;
  }
  
  $form['remove'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Remove'),
    '#options' => $ops
  );
  $form['submit'] = array('#type' => 'submit', '#value' => t('Remove Selected'),);
  return $form;
}


function sustainable_minds_dataload_del_form_submit($form_id, $edit) { 
  $files = variable_get('sustainable_minds_dataload_files',array());
  foreach ($files as $key => $file) {
    if (!$edit['remove'][$key] && $key !== 0 && !empty($key)) {
      $nfiles[$key] = $file;
    }
  }
   variable_set('sustainable_minds_dataload_files', $nfiles);
   drupal_set_message(t('The files have been removed.'));
}


function sustainable_minds_dataload_types($reset = false) {
  $types_all = array( 
        array('type'=>'cats',           'title'=>'Category'),
        array('type'=>'categorization', 'title'=>'Categorization of material'),
        array('type'=>'matproc',        'title'=>'MatProc'),
        array('type'=>'matproc_alias',  'title'=>'MatProc Alias'), 
        array('type'=>'process',        'title'=>'Process to material'),
        array('type'=>'matproc_desc',   'title'=>'MatProc Descriptions'),
  );
        
  if (!$reset) {
    $types = variable_get('sustainable_minds_dataload_types', array());
    foreach ($types as $type) {
      $current[] = $type['type'];
    }
      
    foreach ($types_all as $key=>$type) {
      if (!in_array($types_all['type'], $current)) {
        array_splice($types, $key, 0, array($type));
      }
    }
    
    return $types;
  } else {
    return $types_all;
  }
}


/*
 * Display dataload results
 * - bmagee 07/03/2010 
 */
function sustainable_minds_dataload_results_form($form_values = NULL) { 
  global $user;

  /* get the messages set in the last Dataload run and store in array, clear message queue */
  $error_messages = drupal_get_messages('error'); 
  $messages = drupal_get_messages(NULL, TRUE);  
  if (count($messages) < 1) {
    //if (isset($form['save_messages'])) {
    //  $messages = $form['save_messages'];
    //} else {
      $messages[] = 'There are no results from the last dataload run.';
    //}
  }
  
  /* Add fieldset for section. */
  $form['dataload-results'] = array(
    '#type' => 'fieldset',
    '#title' => t(''),
  );
  $form['dataload-results']['open'] = array(
    '#type' => 'markup',
    '#value' => t('<div class="registration_fields" style="font-size:10px;">')
  );
  
  /* build output from the error message array */
  $i = 0;
  $output = '<table width="100%">';
  while($i < count($error_messages['error'])) {
    $text = trim($error_messages['error'][$i]);
    if (strlen($text) > 0) {
      $output .= '<tr class="error"><td>' . $text . '</td></tr>';
    }
    $i++;
  }
  
  /* build output from the status message array */
  while($i < count($messages['status'])) {
    $stripe = $i % 2 ? 'evenzz' : 'odd';
    $text = trim($messages['status'][$i]);
    if (strlen($text) > 0) {
      $output .= '<tr class="' . $stripe . '"><td>' . $text . '</td></tr>';
    }
    $i++;
  }
  $output .= '</table>';

  $form['dataload-results']['message'] = array(
    '#type' => 'markup',
    '#value' => t('<div class="dataload_results" style="background-color:#CFC;">
                  ' . $output)
  );
  $form['dataload-results']['close'] = array(
      '#type' => 'markup',
      '#value' => t('</div>')
  );

  /* save messages to hidden form field */
  $form['save_messages'] = array('#type' => 'hidden', '#value' => $output);

 /* Add fieldset for section. */
  $form['dataload-custom'] = array(
    '#type' => 'fieldset',
    '#title' => t(''),
  );
  $form['dataload-custom']['open'] = array(
    '#type' => 'markup',
    '#value' => t('<div class="registration_fields" style="font-size:10px;">')
  );

  $files = variable_get('sustainable_minds_dataload_files',array());
  foreach ($files as $key => $file) {
    $ops[$key] = $key;
  }
  
  $form['dataload-custom']['select'] = array(
//  $form['select'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Select'),
    '#options' => $ops
  );
  
  $form['dataload-custom']['submit'] = array('#type' => 'submit', '#value' => t('Process Selected'),);
//  $form['submit'] = array('#type' => 'submit', '#value' => t('Process Selected'),);

  $form['dataload-custom']['close'] = array(
      '#type' => 'markup',
      '#value' => t('</div>')
  );

  return $form;
}


function sustainable_minds_dataload_results_form_submit($form_id, $edit) { 
  $files = variable_get('sustainable_minds_dataload_files',array());
  foreach ($files as $key => $filea) {
    if ($edit['select'][$key] && $key !== 0 && !empty($key)) {
      $nfile = $key;
	  break;
    }
  }
  $filesel = 'files/dataload_data/'. $nfile;
  watchdog ('admin','This is dataload results nfile: '.$filesel);
  $file = fopen($filesel, "r");
	while(!feof($file)) {
		$data_array[] = fgetcsv($file, 0, "	");
	}
  fclose($file);
  /* process the data */
  /* get the fields to be updated */
  $colNames = $data_array[0];
  array_walk($colNames,'trim');
  $colNames = array_flip($colNames);
  
  if (count($colNames) > count(array_unique($colNames))) {
	sendError('File with same named columns\t');
  }
  // load data from file into array() - start on row[1]
  $select_array = array();
  for ($i = 1; $i < count($data_array); $i++) {
	$select_array[$i] = $data_array[$i];
	if (!empty($select_array[$i])) {
		array_walk($select_array[$i], 'trim');
	}
  }

  foreach ($select_array as $select_row) {
    if (!empty($select_row)) {
		$vid = $select_row[$colNames['vid']];
		$planKey = $select_row[$colNames['field_plan_key_value']];
		$trialStart = $select_row[$colNames['field_trial_start_value']];
		$trialExpire = $select_row[$colNames['field_trial_expire_value']];
		$purchaseDate = $select_row[$colNames['field_purchase_date_value']];
		$planDesc = $select_row[$colNames['field_plan_description_value']];
		$planBasePrice = $select_row[$colNames['field_plan_base_price_value']];
		$planExpireDate = $select_row[$colNames['field_plan_expire_date_value']];
		$planRenewDate = $select_row[$colNames['field_plan_renew_date_value']];
		db_query('UPDATE {content_type_user_profile} 
			SET field_plan_key_value="%s", field_trial_start_value="%s", field_trial_expire_date_value="%s",
			field_purchase_date_value="%s", field_plan_description_value="%s", field_plan_base_price_value="%s", 
			field_plan_expire_date_value="%s", field_plan_renew_date_value="%s"
			WHERE vid="%s"',$planKey, $trialStart, $trialExpire, $purchaseDate, $planDesc, $planBasePrice, $planExpireDate, $planRenewDate, $vid);
	}
  }
}


/*
 * Build list of dataset versions
 **/
function sustainable_minds_list_versions() {
  global $user;

  $is_admin = false;
  if (in_array("superadmin", $user->roles) || in_array("lca data manager", $user->roles)) {
    $is_admin = true;
  }
   
  $db = \Drupal::service('setup_project.sbom_db');

  $versions = $db->list_dataset_version();
  $list = array();
  $list['1.0'] = 'Dataset 1.0';
  
  /* build dropdown list */
  for ($i = 0; $i < count($versions); $i++) {
    if ($versions[$i]) {
      if ($versions[$i]['status'] == 1 || $is_admin) {
        $option = trim($versions[$i]['version']);
        $description = 'Dataset: ' . trim($versions[$i]['version']);
        $list[$option] = $description;
      }
    }
  }

  return $list;
}


/*
 * Display dataset versions information page
 * - bmagee 07/03/2010 
 */
function sustainable_minds_datasets_form($form_values = NULL) { 
  global $user;

  /*************************************************/
  /* Display table of dataset versions             */
  /*************************************************/
    
  /* get dataset information */
  $db = \Drupal::service('setup_project.sbom_db');

  $info = $db->list_datasetInfo();  

  /* setup column headings */
  $output = '<div class="registration-content"> 
             <br /><h2>Dataset Version Maintenance</h2>
             <p style="font-size:11px;">This page is updated automatically whenever a process is run to load LCA data from 
             one or more uploaded files. <br />
             A dataset version will not be available for general use until it has been published.
             </p><br />
             <table style="width:720px; padding-left:5px;"><tbody>
             <tr style="background-color:#036;color:#fff;line-height:22px;">
               <th style="color:#fff; padding-left:5px;"">Version</th><th style="color:#fff;">Revision</th>
               <th style="color:#fff;">Description</th><th style="color:#fff;"></th>
               <th style="color:#fff;">Status</th><th style="color:#fff;"></th>
             </tr>
             ';
    
  /* hard-code first row for old data (2009) */
  $output .= '<tr class="odd">
                <td>1.0</td><td>1.0</td><td colspan="2">Original dataset prior to implementation of versioning (2009)</td>
                <td>published</td><td></td>
              </tr>';
  
  /* dataset data */
  foreach ($info as $row) {
    if ($row['status'] > 0) {
      $publish = 'published'; 
    } else {
      $publish = 'draft';
    }
    
    $class = 'even' ? 'odd' : 'even';
    $output .= '<tr class="' . $class . '">
                  <td>' . $row['version'] . '</td><td>' . $row['revision'] . '</td>
                  <td colspan="2">' . $row['description'] . '</td><td>' . $publish . '</td>
                  <td><a href="/edit_dataset/'. $row['version'] . '">edit</a>&nbsp;&nbsp;<a href="/delete_dataset/'. $row['version'] . '">delete</a></td>
                </tr>';
    $count++;
  }
  
  $output .= '</tbody>
              </table>
              </fieldset> 
              <p><a href="/add_dataset">Add a dataset</a></p>
              </div>';

  //why use a form to display the table?
  $form['dataset_info'] = array(
    '#type' => 'markup',
    '#value' => t($output),
  );

  $form['map_datasets'] = array(
    '#type' => 'fieldset',
    '#title' => t('Create a dataset mapping'),
    '#description' => t('Create a mapping to allow updating a project from one version to another.')
  );
  $form['map_datasets']['source_dataset'] = array(
		'#type' => 'select',
		'#title' => 'Source dataset version',
		'#options' => sustainable_minds_list_versions(),
  );
  $form['map_datasets']['dest_dataset'] = array(
		'#type' => 'select',
		'#title' => 'Destination dataset version',
		'#options' => sustainable_minds_list_versions(),
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit'
  );

  return $form;
}


function sustainable_minds_datasets_form_validate($form_id, $form_values) {
  if ($form_values['source_dataset'] == $form_values['dest_dataset']) {
    form_set_error('dest_dataset', 'Source and destination dataset versions should be different.');
  }
}

function sustainable_minds_datasets_form_submit($form_id, $form_values) {
  $db = \Drupal::service('setup_project.sbom_db');
  $db->map_dataset($form_values['source_dataset'], $form_values['dest_dataset']);
  drupal_set_message('Mapping has been created: '.$form_values['source_dataset'].' '. $form_values['dest_dataset']);
}

/*
 * Add/edit dataset form
 * - bmagee 07/03/2010 
 */
function sustainable_minds_edit_dataset_form($form_values = NULL) {
  global $user;
    
  if (arg(0) == 'add_dataset') {
    $add = true;
    $info = array();
  } else {
    /* get dataset information */
    $version = arg(1);
    $db = \Drupal::service('setup_project.sbom_db');
    $info = $db->get_datasetInfo($version);
  }

  /* Display dataset fields  */
  $form['heading'] = array(
    '#type' => 'markup',
    '#value' => $add ? t('<div class="dataset-content"><h2>Add Dataset</h2>') : t('<div class="dataset-content"><h2>Edit Dataset</h2>')
  );
  /* Add fieldset for section. */
  $form['dataset-section-1'] = array(
    '#type' => 'fieldset',
    '#title' => t(''),
  );
  $form['dataset-section-1']['open'] = array(
    '#type' => 'markup',
    '#value' => t('<div class="dataset_fields">')
  );
  if ($add) {
    $form['dataset-section-1']['version'] = array(
      '#type' => 'textfield',
      '#title' => t('Version'),
      '#required' => TRUE,
    );
  } else {
    $form['dataset-section-1']['version'] = array(
      '#type' => 'textfield',
      '#title' => t('Version'),
      '#default_value' => $info['version'],
      '#attributes' => array('readonly' => 'readonly'),
    );
  }
  $form['dataset-section-1']['revision'] = array(
    '#type' => 'textfield',
    '#title' => t('Revision'),
    '#required' => TRUE,
    '#default_value' => $info['revision'],
  );
  $form['dataset-section-1']['description'] = array(
    '#type' => 'textfield',
    '#title' => t('Description'),
    '#required' => TRUE,
    '#default_value' => $info['description'],
  );
  $form['dataset-section-1']['status'] = array(
    '#type' => 'checkbox',
    '#title' => t('Publish'),
    '#default_value' => $info['status'],
  );
  $form['dataset-section-1']['close'] = array(
    '#type' => 'markup',
    '#value' => t('</div></div>')
  );


  if (!$add) {
    /* get dataset file upload information */
    $db = \Drupal::service('setup_project.sbom_db');
    $rows = $db->list_datasetFiles($version); 
  
    /* setup column headings */
    $output = '<br /><h2>Files Processed</h2>
               <table style="width:660px; padding-left:5px;"><tbody>
               <tr style="background-color:#036;color:#fff;line-height:22px;">
                 <th style="color:#fff; padding-left:5px;"">Filename</th><th style="color:#fff;">Type</th>
                 <th style="color:#fff;">Date</th>
               </tr>
               ';
    
    $arr = array();
    
    foreach ($rows as $row) {
      $workdate = strtotime($row['last_updated']);
      $updated = date('F d, Y - g:i a', $workdate); 
  
      $arr [] = strtolower($row['fileType']);
      $class = 'even' ? 'odd' : 'even';
      $output .= '<tr class="' . $class . '"><td>' . $row['filename'] . '</td><td>' . $row['fileType'] . '</td>
                    <td>' . $updated . '</td>
                  </tr>';
    }
  
    if (!in_array('categories', $arr)) 
      $output .= '<tr><td><i>Missing Category</i></td><td></td><td></td></tr>';
    if (!in_array('materials', $arr)) 
      $output .= '<tr><td><i>Missing Categorization of Materials</i></td><td></td><td></td></tr>';
    if (!in_array('matproc', $arr)) 
      $output .= '<tr><td><i>Missing MatProc</i></td><td></td><td></td></tr>';
    if (!in_array('matprocalias', $arr)) 
      $output .= '<tr><td><i>Missing MatProc Alias</i></td><td></td><td></td></tr>';
    if (!in_array('procmat', $arr)) 
      $output .= '<tr><td><i>Missing Process to Material</i></td><td></td><td></td></tr>';
    if (!in_array('matprocdesc', $arr)) 
      $output .= '<tr><td><i>Missing MatProc Descriptions</i></td><td></td><td></td></tr>';
  
    $output .= '</tbody>
                </table><br /><br />';
  
    $form['dataset_info'] = array(
      '#type' => 'markup',
      '#value' => t($output),
    );
  }
  
  /* submit */
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => $add ? 'Add' : 'Update',
  );
 
 $form['profile-section-3']['cancel'] = array(
    '#type' => 'markup',
    '#value' => t('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin/settings/sustainable_minds/dataload/datasets">Cancel</a>')
  );

  return $form;
}

/**
 * Validate handler for form ID 'sustainable_minds_edit_dataset_form'.
 */
function sustainable_minds_edit_dataset_form_validate($form_id, $form_values) {
  
  /* add validation here */

}


function sustainable_minds_edit_dataset_form_submit($form_id, $form_values) {
  switch ($form_values['submit']) {
    case 'Add':
      sustainable_minds_add_dataset_info($form_values);
      return '/admin/settings/sustainable_minds/dataload/datasets';
      break;
    case 'Update':
      /* update the dataset */
      sustainable_minds_update_dataset_info($form_values);
      drupal_set_message('Dataset: ' . $version . 'has been updated!');
      return '/admin/settings/sustainable_minds/dataload/datasets';
      break;
  }
}


/*
 * Add new dataset info 
 **/
function sustainable_minds_add_dataset_info($form_values) {
  $version = $form_values['version'];
  $revision = $form_values['revision'];
  $description = $form_values['description'];
  $status = $form_values['status'];
  
  $db = \Drupal::service('setup_project.sbom_db');
  $db->add_dataset($version, $revision, $description, $status);  
}


/*
 * Update dataset info 
 **/
function sustainable_minds_update_dataset_info($form_values) {
  $version = $form_values['version'];
  $revision = $form_values['revision'];
  $description = $form_values['description'];
  $status = $form_values['status'];
  
  $db = \Drupal::service('setup_project.sbom_db');
  $db->update_dataset($version, $revision, $description, $status);  
}


function sustainable_minds_delete_dataset() {
  if (arg(1)) {
    $db = \Drupal::service('setup_project.sbom_db');
    $db->delete_dataset(arg(1)); //no revision?
  }
  drupal_goto('admin/settings/sustainable_minds/dataload/datasets');
}

?>