<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\ProjectForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

class ProjectDefinitionForm extends FormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'setup_project.definition_form',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'definition_form';  
  }  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.definition_form'); 
    // $categories = array('(not entered)','Air & Environment','Aircraft,Bath','Bedding', 'Beverage Packaging','Children Vehicles','Clothing',
    // 'Computer Equipment','Construction', 'Mining & Materials Handling','Consumer equipment','Dining') ;
    $categories = \Drupal::service('setup_project.project_service')->getCategories();

    $form['project_name']=[
      '#type' => 'textfield',
      '#title' => $this->t('Project name: *'),
      '#default_value' => $config->get('setup_project.project_name'),
    ]; 
    $form['client_or_group']=[
      '#type' => 'textfield',
      '#title' => $this->t('Client or group:'),
      '#default_value' => $config->get('setup_project.client_or_group'),
    ]; 
    $form['product_category']=[
      '#type' => 'textfield',
      '#title' => $this->t('Product category:'),
      '#default_value' => $config->get('setup_project.product_category'),
    ];
    $form['product_category'] = array(
      '#type' => 'select',
      '#title' => t('Product category:'),
      '#options' => $categories,
      '#default_value' => $config->get('setup_project.product_category'),
  ); 
    $form['project_description']=[
      '#type' => 'textarea',
      '#title' => $this->t('Project Description:'),
      '#default_value' => $config->get('setup_project.project_description'),
      '#description' => '<div id="edit-description-description" class="description">Description information can include:<ul><li>Project background info</li><li>Product backround info</li><li>Summarized design brief</li><li>Ecodesign strategies to be explored.</li></ul>There is no character limit on this or subsequent free text fields.</div>'
    ];
    $form['dataset_version'] = array(
      '#type' => 'select',
      '#title' => t('LCA Dataset Version:'),
      '#options' => $categories,
      '#default_value' => $config->get('setup_project.dataset_version'),
  );   
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => array([$this, 'Cancel'])
    ];
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('next'),
      '#submit' => array([$this, 'submitForm'])
    ];

    return $form;  
  }
  public function Cancel($form, &$form_state){
    $form_state->setRedirect('/project');
  }
    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.defintion_form');
    // $config->set('setup_project.project_name', $form_state->getValue('project_name'));  
    // $config->set('setup_project.client_or_group', $form_state->getValue('client_or_group'));   
    // $config->set('setup_project.product_category', $form_state->getValue('product_category'));  
    // $config->set('setup_project.project_description', $form_state->getValue('project_description'));  
    // $config->set('setup_project.dataset_version', $form_state->getValue('dataset_version'));  
    // $config->save();
    $form_state->setRedirect('setup_project.goals_form');  
  }  
}  
