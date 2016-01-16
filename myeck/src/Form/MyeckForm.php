<?php
/**
 * @file
 * Contains Drupal\content_entity_example\Form\ContactForm.
 */

namespace Drupal\myeck\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the content_entity_example entity edit forms.
 *
 * @ingroup myeck
 */
class MyeckForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\myeck\Entity\Myeck */
    $form = parent::buildForm($form, $form_state);
    //сделать универсальное для всех !!!!!!!!!!!!!!!
//    $entity = $this->entity;

//    $form['langcode'] = array(
//      '#title' => $this->t('Language'),
//      '#type' => 'language_select',
//      '#default_value' => $entity->getUntranslated()->language()->getId(),
//      '#languages' => Language::STATE_ALL,
//    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $redirect = $entity->getEntityType()->get('id');
    $form_state->setRedirect('entity.' . $redirect . '.collection');
    $entity->save();
  }
}
