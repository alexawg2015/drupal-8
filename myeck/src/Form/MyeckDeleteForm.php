<?php

/**
 * @file
 * Contains \Drupal\content_entity_example\Form\ContactDeleteForm.
 */

namespace Drupal\myeck\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a content_entity_example entity.
 *
 * @ingroup content_entity_example
 */
class MyeckDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $title = empty($this->entity->label()) ? $this->entity->id() : $this->entity->label();
    $type = $this->entity->getEntityType()->getLabel();
    return $this->t('Are you sure you want to delete %entity entity %name?', array(
      '%entity' => $type,
      '%name' => $title
    ));
  }

  /**
   * {@inheritdoc}
   *
   * If the delete command is canceled, return to the contact list.
   */
  public function getCancelURL() {
    $type = $this->entity->getEntityType()->id();
    return new Url('entity.' . $type . '.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. log() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();
    $type = $entity->getEntityType()->id();
    $title = empty($this->entity->label()) ? $this->entity->id() : $this->entity->label();

    \Drupal::logger($type)->notice('@type: deleted %title.',
      array(
        '@type' => $this->entity->bundle(),
        '%title' => $title,
      ));
    $form_state->setRedirect('entity.' . $type . '.collection');
  }

}
