<?php  
/**  
 * @file  
 * Contains Drupal\email_notification\Form\emailSettings.  
 */  
namespace Drupal\email_notification\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  

class emailSettings extends ConfigFormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'email_notification.adminsettings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'email_notification_form';  
  }  
  /**  
   * returns admin form for Email Settings
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('email_notification.adminsettings'); 
    $reg_token = '<br><a href="/admin/settings/sm_registration/email/help/tokens">Uses registration tokens.</a>'; 
    $form['notification_settings'] = [  
      '#type' => 'fieldset',  
      '#title' => $this->t('Email Notification Settings'),
    ]; 
    $form['notification_settings']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Define email notification settings.'),
    ];
    $form['notification_settings']['welcome_subject']=[
      '#type' => 'textfield',
      '#title' => $this->t('Trial Welcome Email Subject:'),
      '#default_value' => $config->get('email_notification.welcome_subject'),
    ]; 
    $form['notification_settings']['welcome_message']=[
      '#type' => 'textarea',
      '#title' => $this->t('Trial Welcome Email Message:' . $reg_token),
      '#default_value' => $config->get('email_notification.welcome_message'),
    ]; 
    $form['notification_settings']['renews_subject']=[
      '#type' => 'textfield',
      '#title' => $this->t('15 Days Before Subscription Renews Subject:'),
      '#default_value' => $config->get('email_notification.renews_subject'),
    ]; 
    $form['notification_settings']['renews_message']=[
      '#type' => 'textarea',
      '#title' => $this->t('15 Days Before Subscription Renews Message:'. $reg_token),
      '#default_value' => $config->get('email_notification.renews_message'),
    ];
    $form['notification_settings']['expire_subject_28_days']=[
      '#type' => 'textfield',
      '#title' => $this->t('28 Days Before Plan Expires Email Subject:'),
      '#default_value' => $config->get('email_notification.expire_subject_28_days'),
    ];  
    $form['notification_settings']['expire_message_28_days']=[
      '#type' => 'textarea',
      '#title' => $this->t('28 Days Before Plan Expires Email Message:'. $reg_token),
      '#default_value' => $config->get('email_notification.expire_message_28_days'),
    ]; 
    $form['notification_settings']['expire_subject_7_days']=[
      '#type' => 'textfield',
      '#title' => $this->t('7 Days Before Trial Expires Email Subject:'),
      '#default_value' => $config->get('email_notification.expire_subject_7_days'),
    ];
    $form['notification_settings']['expire_message_7_days']=[
      '#type' => 'textarea',
      '#title' => $this->t('7 Days Before Trial Expires Email Message:'. $reg_token),
      '#default_value' => $config->get('email_notification.expire_message_7_days'),
    ]; 
    $form['notification_settings']['expire_subject_2_days']=[
      '#type' => 'textfield',
      '#title' => $this->t('2 Days Before Trial Expires Email Subject:'),
      '#default_value' => $config->get('email_notification.expire_subject_2_days'),
    ]; 
    $form['notification_settings']['expire_message_2_days']=[
      '#type' => 'textarea',
      '#title' => $this->t('2 Days Before Trial Expires Email Message:'. $reg_token),
      '#default_value' => $config->get('email_notification.expire_message_2_days'),
    ]; 
    $form['notification_settings']['post_expire_subject']=[
      '#type' => 'textfield',
      '#title' => $this->t('30 Days Post Expiration Email Subject:'),
      '#default_value' => $config->get('email_notification.post_expire_subject'),
    ]; 
    $form['notification_settings']['post_expire_message']=[
      '#type' => 'textarea',
      '#title' => $this->t('30 Days Post Expiration Email Message:'. $reg_token),
      '#default_value' => $config->get('email_notification.post_expire_message'),
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
    $config = $this->config('email_notification.adminsettings');
    $config->set('email_notification.welcome_subject', '');  
    $config->set('email_notification.welcome_message', '');  
    $config->set('email_notification.renews_subject', '');  
    $config->set('email_notification.renews_message', '');  
    $config->set('email_notification.expire_subject_28_days','');  
    $config->set('email_notification.expire_message_28_days','');  
    $config->set('email_notification.expire_subject_7_days', '');  
    $config->set('email_notification.expire_message_7_days', '');  
    $config->set('email_notification.expire_subject_2_days', '');  
    $config->set('email_notification.expire_message_2_days', '');  
    $config->set('email_notification.post_expire_subject', '');  
    $config->set('email_notification.post_expire_message', '');  
    $config->save();  
    }   

    /**  
   * { To save the values of submitted form }  
   */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    $config = $this->config('email_notification.adminsettings');
    $config->set('email_notification.welcome_subject', $form_state->getValue('welcome_subject'));  
    $config->set('email_notification.welcome_message', $form_state->getValue('welcome_message'));  
    $config->set('email_notification.renews_subject', $form_state->getValue('renews_subject'));  
    $config->set('email_notification.renews_message', $form_state->getValue('renews_message'));  
    $config->set('email_notification.expire_subject_28_days', $form_state->getValue('expire_subject_28_days'));  
    $config->set('email_notification.expire_message_28_days', $form_state->getValue('expire_message_28_days'));  
    $config->set('email_notification.expire_subject_7_days', $form_state->getValue('expire_subject_7_days'));  
    $config->set('email_notification.expire_message_7_days', $form_state->getValue('expire_message_7_days'));  
    $config->set('email_notification.expire_subject_2_days', $form_state->getValue('expire_subject_2_days'));  
    $config->set('email_notification.expire_message_2_days', $form_state->getValue('expire_message_2_days'));  
    $config->set('email_notification.post_expire_subject', $form_state->getValue('post_expire_subject'));  
    $config->set('email_notification.post_expire_message', $form_state->getValue('post_expire_message'));  
    $config->save();  
  }  
}

 