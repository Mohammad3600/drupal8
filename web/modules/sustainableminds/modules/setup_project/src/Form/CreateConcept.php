<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\CreateConcept.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

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
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.create_concept');  
    $form['concept_name']=[
      '#type' => 'textfield',
      '#title' => $this->t('Concept name: *'),
      '#default_value' => $config->get('setup_project.concept_name'),
    ]; 
    $form['image_render']=[
        '#markup' => '<div class="mb-3 form-group noscope_upload">
        <label for="edit-system" class="form-label">Image or rendering:</label><br>
        <img src="/drupal8/web/sites/default/files/2021-07/no_scope.gif" class="mb-2" alt="scope image" title="system upload">
        <ul class="d-flex mb-2">
          <li><a href="#" class="btn">Use default image</a></li>
          <li><a href="#" class="btn">Browser</a></li>
        </ul>
        <div class="gl-upload-help">
          Image format: gif, jpg, png<br>Max width: 670px
        </div>
      </div>'
    ];
    $form['concept_desc'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Concept description:'),
        '#default_value' => '',
        '#description' => '<div class="description">
        <p class="d-block">
          Include what is distinctive about this concept along
          with ecodesign strategies being explored.
        </p>
        <a class="mb-2 d-block" href="JavaScript:void(0);">View ecodesign strategy wheel &gt;</a>
        </div>',
    ];
    $form['upload_file']=[
        '#type' => 'file',
        '#attributes' => array(
            'id' => 'UploadFile',
            'class' => array(
                'd-none'
            )
        )
    ];
    $form['total_amount'] = [
        '#type' => 'textfield',
        '#title' => 'Total amount of service delivered: *',
        '#default_value' => '',
        '#description' => 'Example: the product concept is designed for 10 years of use, and the functional unit is 1 year of use (1x10 = 10), enter 10 as the amount of service delivered.'
    ];
    $form['year_use'] = [
        '#markup' => '<span class="field-suffix">
        <strong> X 1 year of use (functional unit)</strong>
        <p> Year of use is a standard unit of measure when service delivered is measured by time.</p> 
        </span>'
    ];
    $form['estimate_amount'] = [
        '#type'=> 'textarea',
        '#title'=>'Describe your rationale or calculations used to estimate the amount of service delivered.',
        '#default_value' => '',
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => array([$this, 'Cancel'])
    ];
    $form['actions']['save'] = [
        '#type' => 'submit',
        '#value' => $this->t("Save"),
        '#submit' => array([$this, 'submitForm'])
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
