<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\AssessmentGoalsForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

class AssessmentGoalsForm extends FormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'setup_project.goals_form',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'goals_form';  
  }  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.goals_form');  
    $form['policies']=[
      '#type' => 'textarea',
      '#title' => $this->t('Company goals and environmental policies:'),
      '#default_value' => $config->get('setup_project.policies'),
    ]; 
    $form['goals']=[
      '#type' => 'textfield',
      '#title' => $this->t('Project assessment goals:'),
      '#default_value' => $config->get('setup_project.goals'),
      '#description' => '<div id="edit-assessment-description" class="description">Why are you conducting this assessment - what do you hope to learn? <br><br><em>Examples: <ul><li>Understand which ecodesign strategies result in the greatest environmental performance improvement</li><li>How to Increase environmental performance by at least 20% from the 2008 version (the reference)</li><li>Determine how the results support corporate goals to begin to better define product-level goals</li></ul></em></div>'
    ]; 

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
    $form['actions']['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('back'),
      '#submit' => array([$this, 'goToBack'])
    ];

    return $form;  
  }
  public function Cancel($form, &$form_state){
    $form_state->setRedirect('/project');
  }
  public function goToBack($form, &$form_state){
    $form_state->setRedirect('/project/add');
  }
    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.defintion_form');
    // $config->set('setup_project.policies', $form_state->getValue('policies'));  
    // $config->set('setup_project.goals', $form_state->getValue('goals'));   
    // $config->save();
    $form_state->setRedirect('setup_project.scope_form');  
  }  
}  
