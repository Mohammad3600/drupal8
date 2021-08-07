<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\ProjectForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

class ConceptsForm extends FormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'setup_project.concepts_form',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'concepts_form';  
  }  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.concepts_form');  
    $form['complete']=[
      '#type' => 'item',
      '#markup' =>'<div class="project-complete mt-4 mb-5">
      <h5 class="heading_6">Project setup complete!</h5>
      <h5 class="heading_6">You can now create concepts for assessment.</h5>
      <p>The first concept created will automatically become the <b>reference concept</b> for this project. The reference concept is  the baseline to which subsequent concepts are compared. Once you have created more than one concept for this project, you can designate a different concept as the reference. </p>
  </div>'
    ]; 

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => array([$this, 'Cancel'])
    ];
    $form['actions']['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('back'),
      '#submit' => array([$this, 'goToBack'])
    ];
    $form['actions']['no_thanks'] = [
        '#type' => 'submit',
        '#value' => $this->t("No thanks, I'll create concepts later"),
        '#submit' => array([$this, 'submitForm'])
      ];
    $form['actions']['add_first_concept'] = [
        '#type' => 'submit',
        '#value' => $this->t("Add First Concept"),
        '#submit' => array([$this, 'addFirstConcept'])
      ];

    return $form;  
  }
  public function Cancel($form, &$form_state){
    $form_state->setRedirect('/project');
  }
  public function goToBack($form, &$form_state){
    $form_state->setRedirect('setup_project.scope_form');
  }
  public function addFirstConcept($form, &$form_state){
    $form_state->setRedirect('setup_project.add_concept');
  }
    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.defintion_form');
    // $config->set('setup_project.policies', $form_state->getValue('policies'));  
    // $config->set('setup_project.goals', $form_state->getValue('goals'));   
    // $config->save();
    $form_state->setRedirect('/project');  
  }  
}  
