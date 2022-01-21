<?php
/**
 * @file
 * Contains \Drupal\bb_custom\Form\SubscribeForm.
 */
namespace Drupal\bb_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SubscribeForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'subscribe_bb_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email ID'),
      '#required' => TRUE,
      /*'#prefix' => '<div class="input_field"><span class="prefix">+91</span>',
      '#suffix' => '</div><p class="note">We we need: PAN NO., Aadhar Card, Bank Account No.</p>',*/
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Subscribe'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * Validate the select field of the form
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */

  public function validateForm(array &$form, FormStateInterface $form_state){
    //parent::validateForm($form, $form_state);
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    /*drupal_set_message($this->t('@employee_name ,Your application is being submitted!', array('@emp_name' => $form_state->getValue('employee_name'))));*/
    
  }
}