<?php  
/**  
 * @file  
 * Contains Drupal\setup_project\Form\ProjectForm.  
 */  
namespace Drupal\setup_project\Form;  
use Drupal\Core\Form\FormBase; 
use Drupal\Core\Form\FormStateInterface;

class AssessmentScopeForm extends FormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'setup_project.scope_form',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'scope_form';  
  }  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.scope_form');
    
    $form['functional_unit']=[
        '#markup' => '<h3>Functional unit</h3>'
    ];
    $form['change_unit']=[
        '#type' => 'container',
        '#attributes' => array(
            'class' => array(
                'functional_explain'
            )
        )
    ];
    $form['change_unit']['label'] = [
        '#type' => 'item',
        '#markup' => 'Change the functional unit for this product.'
    ];
    $form['change_unit']['impact']=[
      '#type' => 'textfield',
      '#title' => $this->t('Impacts per : *'),
      '#default_value' => 'Change the functional unit for this product.1 year of use',
    ]; 
    $form['change_unit']['note']=[
      '#type' => 'textarea',
      '#title' => $this->t('Add a note describing why this functional unit was selected. This description will be displayed in each concept as a reminder.
      Example: Year of use is a standard unit of measure when service delivered is measured by time.'),
      '#default_value' => 'Year of use is a standard unit of measure when service delivered is measured by time.',
    ]; 

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['change_unit']['cancel_change'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      'id' => 'CancelChange'
    ];
    $form['change_unit']['update_default'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Default'),
      'id' => 'UpdateDefault',
      '#submit' => array([$this, 'updateDefault'])
    ];
    $form['note_text'] = [
        '#markup' => '<strong class="text_warning">Note: upon completing the project setup, the functional unit can no longer be changed.</strong>'
    ];
    $form['explanation'] = [
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
    ];
    $form['product_system'] = [
        '#markup' => '<h4 class="heading_6">Product system</h4>'
    ];
    $form['describe_product']=[
        '#type' => 'textarea',
        '#title' => $this->t('Describe the product system and system boundaries.'),
        '#default_value' => '',
      ]; 
      $form['image_wrapper'] = [
        '#markup' => '<div class="mb-3 form-group noscope_upload">
        <label for="edit-system" class="form-label">If you have an image that illustrates the product system, upload it here.</label>
        <img src="/drupal8/web/sites/default/files/2021-07/no_scope.gif" class="mb-2" alt="scope image" title="system upload">
        <ul class="d-flex mb-2">
            <li><a href="#" class="btn">Use default image</a></li>
            <li><a href="#" class="btn">Browser</a></li>
        </ul>
        <div class="gl-upload-help">Image format: gif, jpg, png<br>Max width: 670px</div>
    </div>'
    ];
    $form['list_products']=[
        '#type' => 'textarea',
        '#title' => $this->t('<label for="edit-exclusion" class="form-label mb-0">List product system exclusions.  </label>
        <p class="mb-2"><em>Example: If you&#39;re designing a lighting fixture and DO NOT intend to include the bulb, indicate that here.</em></p>'),
        '#default_value' => '',
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
    $form_state->setRedirect('setup_project.goals_form');
  }
    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    $config = $this->config('setup_project.defintion_form');
    // $config->set('setup_project.policies', $form_state->getValue('policies'));  
    // $config->set('setup_project.goals', $form_state->getValue('goals'));   
    // $config->save();
    $form_state->setRedirect('setup_project.concepts_form');  
  }  
}  