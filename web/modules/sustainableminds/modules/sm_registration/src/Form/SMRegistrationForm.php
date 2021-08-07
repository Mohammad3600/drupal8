<?php

namespace Drupal\sm_registration\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SMRegistrationForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'ex81_hello_form';
  }

   /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
 echo $html = "<div class='test_div'>";
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please confirm the following.'),
    ];
echo $html = "</div>";
    
    $form['FirstName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['LastName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['Email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['Phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['role'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title or Role'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['about'] = [
      '#type' => 'select',
      '#title' => $this
        ->t('How did you hear about us'),
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    
    $form['business'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Business or school name'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['state'] = [
      '#type' => 'textfield',
      '#title' => $this->t('State/Province'),
      '#description' => $this->t(''),
      '#required' => TRUE,
    ];

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['business_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Business Type'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['organization_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Product development organization size'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['username'] = [
      '#type' => 'select',
      '#title' => $this->t('User name'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['password'] = [
      '#type' => 'select',
      '#title' => $this->t('Create Password'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['confirm_password'] = [
      '#type' => 'select',
      '#title' => $this->t('Retype password'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    $form['time_zone'] = [
      '#type' => 'select',
      '#title' => $this->t('Time Zone'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        '1' => $this
          ->t('One')
      ],
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Display the results.
    
    // Call the Static Service Container wrapper
    // We should inject the messenger service, but its beyond the scope of this example.
    $messenger = \Drupal::messenger();
    $messenger->addMessage('Title: '.$form_state->getValue('title'));
    $messenger->addMessage('Accept: '.$form_state->getValue('accept'));

    // Redirect to home
    $form_state->setRedirect('<front>');

  } 

}