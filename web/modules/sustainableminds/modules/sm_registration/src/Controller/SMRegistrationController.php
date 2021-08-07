<?php

namespace Drupal\sm_registration\Controller;
class SMRegistrationController {
  public function registration() {

    // $service = \Drupal::service('sm_registration.login');
    // print var_export($service, true);
  //  dsm($service->sayHello('rakesh'));
    return array(
      '#markup' => 'SM Registration 123'
    );
  }
}