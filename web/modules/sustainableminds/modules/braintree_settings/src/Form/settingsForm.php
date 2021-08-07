<?php  
/**  
 * @file  
 * Contains Drupal\braintree_settings\Form\settingsForm.  
 */  
namespace Drupal\braintree_settings\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  

class settingsForm extends ConfigFormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'braintree_settings.adminsettings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'braintree_settings_form';  
  }  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('braintree_settings.adminsettings');  
    $form['braintree_integration'] = [  
      '#type' => 'fieldset',  
      '#title' => $this->t('Braintree Integration Settings'),
    ]; 
    $form['braintree_integration']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Define information needed for the integration to Braintree.<br>
      Enable debug mode for the registration cron job. This is designed to allow testing of the registration cron email notifications by using minutes instead of days as a measurement of when an email should be sent to trial users (example: after 15 minutes a trial user will receive the 15 day notfication). Note: cron must be set to run evey minute for this to work properly.'),
    ];
    $form['braintree_integration']['enable_debug'] = [  
      '#type' => 'checkbox',  
      '#title' => $this->t('Enable debug'),
      '#default_value' => $config->get('braintree_settings.enable_debug'),
    ]; 
    $form['braintree_integration']['environment']=[
      '#type' => 'textfield',
      '#title' => $this->t('Environment:'),
      '#default_value' => $config->get('braintree_settings.environment'),
    ]; 
    $form['braintree_integration']['public_key']=[
      '#type' => 'textfield',
      '#title' => $this->t('Public Key:'),
      '#default_value' => $config->get('braintree_settings.public_key'),
    ]; 
    $form['braintree_integration']['private_key']=[
      '#type' => 'textfield',
      '#title' => $this->t('Private Key:'),
      '#default_value' => $config->get('braintree_settings.private_key'),
    ]; 
    $form['braintree_integration']['merchant_id']=[
      '#type' => 'textfield',
      '#title' => $this->t('Merchant ID:'),
      '#default_value' => $config->get('braintree_settings.merchant_id'),
    ];
    $form['braintree_integration']['email_address']=[
      '#type' => 'textfield',
      '#title' => $this->t('Sustainableminds sales email address:'),
      '#default_value' => $config->get('braintree_settings.email_address'),
    ]; 
    $form['braintree_integration']['enable_braintree'] = [  
      '#type' => 'fieldset',  
      '#title' => $this->t('Enable Braintree Subscription Plans'),
      '#description' => 'List the plans you want to enable for on-line registration separated by commas.<br>
      Available Braintree plans: (Regular, Monthly_Plan, Instructor, Semester, )',  
    ];
    $form['braintree_integration']['online_registration']=[
      '#type' => 'textfield',
      '#title' => $this->t('Subscription Plans allowed in on-line registration:'),
      '#default_value' => $config->get('braintree_settings.online_registration'),
    ]; 
    $form['braintree_integration']['plans_allowed']=[
      '#type' => 'textfield',
      '#title' => $this->t('Subscription Plans allowed for Resellers:'),
      '#default_value' => $config->get('braintree_settings.plans_allowed'),
    ]; 
    $form['braintree_integration']['time_period']=[
      '#type' => 'textfield',
      '#title' => $this->t('Time period (days) for Trial subscription:'),
      '#default_value' => $config->get('braintree_settings.time_period'),
    ]; 

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a reset button
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset to defaults'),
      '#submit' => array([$this, 'resetValues'])
    ];

    return parent::buildForm($form, $form_state);  
  }
  public function resetValues($form, &$form_state){
    $config = $this->config('braintree_settings.adminsettings');
    $config->set('braintree_settings.enable_debug', false);  
    $config->set('braintree_settings.environment', '');  
    $config->set('braintree_settings.public_key', '' );  
    $config->set('braintree_settings.private_key', '' );  
    $config->set('braintree_settings.merchant_id', '' );  
    $config->set('braintree_settings.opsource_username', '' );  
    $config->set('braintree_settings.opsource_password', '' );  
    $config->set('braintree_settings.email_address', '' );  
    $config->set('braintree_settings.online_registration', '' );  
    $config->set('braintree_settings.plans_allowed', '' );  
    $config->set('braintree_settings.time_period', 15 );  
    $config->save();
    drupal_set_message('The configuration options have been reset to their default values.');
    }     
    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    $config = $this->config('braintree_settings.adminsettings');
    $config->set('braintree_settings.enable_debug', $form_state->getValue('enable_debug'));  
    $config->set('braintree_settings.environment', $form_state->getValue('environment'));  
    $config->set('braintree_settings.public_key', $form_state->getValue('public_key'));  
    $config->set('braintree_settings.private_key', $form_state->getValue('private_key'));  
    $config->set('braintree_settings.merchant_id', $form_state->getValue('merchant_id'));  
    $config->set('braintree_settings.opsource_username', $form_state->getValue('opsource_username'));  
    $config->set('braintree_settings.opsource_password', $form_state->getValue('opsource_password'));  
    $config->set('braintree_settings.email_address', $form_state->getValue('email_address'));  
    $config->set('braintree_settings.online_registration', $form_state->getValue('online_registration'));  
    $config->set('braintree_settings.plans_allowed', $form_state->getValue('plans_allowed'));  
    $config->set('braintree_settings.time_period', $form_state->getValue('time_period'));  
    $config->save();  
  }  
}  