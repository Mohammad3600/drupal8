<?php  
/**  
 * @file  
 * Contains Drupal\indblog\Form\settingForm.  
 */  
namespace Drupal\indblog\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  

class settingForm extends ConfigFormBase {  
    /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'indblog.adminsettings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'indblog_form';  
  }  

  /**  
   * returns admin form for Braintree Settings
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('indblog.adminsettings');  
    $roleObjects = \Drupal\user\Entity\Role::loadMultiple();
    $roles = [];
    foreach($roleObjects as $roleObj){
      $roles[] = $roleObj->label();
    }
    $showRoles = [];
    foreach($config->get('indblog.show_roles') as $roleObj => $value){
      if($value!= 0){
        $showRoles[] = $value;
      }
    }
    $form['block_class'] = array(
      '#type' => 'fieldset',
      '#title' => t('Block Class settings'),
      '#collapsible' => TRUE,
      '#weight' => -1,
    );
    $form['block_class']['css_class'] = array(
      '#type' => 'textfield',
      '#title' => t('CSS class(es)'),
      '#default_value' => $config->get('indblog.css_class'),
      '#description' => t('Separate classes with a space. IMPORTANT: You must add &lt;?php print block_class($block); ?&gt; to your theme\'s block.tpl.php file to make the classes appear.'),
    );
    $form['block_specific'] = array(
      '#type' => 'fieldset',
      '#title' => t('Block specific settings'),
      '#collapsible' => TRUE,
      '#weight' => -1,
    );
    $form['block_specific']['block_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Block title:'),
      '#default_value' => $config->get('indblog.block_title'),
      '#description' => t('Override the default title for the block. Use <none> to display no title, or leave blank to use the default block title.'),
    );
    $form['block_specific']['block'] = array(
    '#type' => 'select', 
    '#title' => t('Number of news items in block'), 
    '#default_value' => $config->get('indblog.block'), 
    '#options' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
    );
  
    $form['specific_visibility'] = array(
      '#type' => 'fieldset',
      '#title' => t('User specific visibility settings'),
      '#collapsible' => TRUE,
      '#weight' => -1,
    );
    $form['specific_visibility']['custom_visibility'] = array(
      '#type' => 'radios',
      '#title' => t('Custom visibility settings:'),
      '#default_value' => $config->get('indblog.custom_visibility'),
      '#options' => ['Users cannot control whether or not they see this block.','Show this block by default, but let individual users hide it.','Hide this block by default but let individual users show it.'],
      '#description' => t('Allow individual users to customize the visibility of this block in their account settings.'),
    );
  
    $form['role_visibility'] = array(
      '#type' => 'fieldset',
      '#title' => t('Role specific visibility settings'),
      '#collapsible' => TRUE,
      '#weight' => -1,
    );
    $form['role_visibility']['show_roles'] = array(
      '#type' => 'checkboxes',
      '#options' => $roles,
      '#default_value' => $showRoles,
      '#title' => t('Show block for specific roles:'),
      '#description' => t('Show this block only for the selected role(s). If you select no roles, the block will be visible to all users.')
    );
    
    $form['page_visibility'] = array(
      '#type' => 'fieldset',
      '#title' => t('Page specific visibility settings'),
      '#collapsible' => TRUE,
      '#weight' => -1,
    );

    $form['page_visibility']['show_blocks'] = array(
      '#type' => 'radios',
      '#title' => t('Show block on specific pages:'),
      '#default_value' => $config->get('indblog.show_blocks'),
      '#options' => ['Show on every page except the listed pages.','Show on only the listed pages.','Hide this block by default but let individual users show it.'],
      '#description' => t('Allow individual users to customize the visibility of this block in their account settings.'),
    );
    $form['page_visibility']['pages']=[
      '#type' => 'textarea',
      '#title' => $this->t('Pages:'),
      '#default_value' => $config->get('indblog.pages'),
      '#description' => t("Enter one page per line as Drupal paths. The '*' character is a wildcard. Example paths are blog for the blog page and blog/* for every personal blog. <front> is the front page. If the PHP-mode is chosen, enter PHP code between <?php ?>. Note that executing incorrect PHP-code can break your Drupal site."),
    ]; 
    return parent::buildForm($form, $form_state);  
  }

  /**  
  * { To save the values of submitted form }  
  */  
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    $config = $this->config('indblog.adminsettings');
    $config->set('indblog.css_class', $form_state->getValue('css_class'));  
    $config->set('indblog.block_title', $form_state->getValue('block_title'));  
    $config->set('indblog.custom_visibility', $form_state->getValue('custom_visibility'));  
    $config->set('indblog.show_roles', $form_state->getValue('show_roles'));  
    $config->set('indblog.block', $form_state->getValue('block'));  
    $config->set('indblog.show_blocks', $form_state->getValue('show_blocks'));  
    $config->set('indblog.pages', $form_state->getValue('pages'));   
    $config->save();  
  }  
}  