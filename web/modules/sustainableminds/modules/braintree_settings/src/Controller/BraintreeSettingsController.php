<?php

namespace Drupal\braintree_settings\Controller;
use Drupal\Core\Form;
class BraintreeSettingsController {
  public function showValues() {
    $config = \Drupal::config('braintree_settings.adminsettings');
    drupal_set_message($config->get('braintree_settings.email_address'));
    return array(
      '#markup' => $config->get('braintree_settings.email_address')
    );
  }
}