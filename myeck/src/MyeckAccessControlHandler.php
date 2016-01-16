<?php

/**
 * @file
 * Contains \Drupal\myeck\MyeckAccessControlHandler
 */

namespace Drupal\myeck;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the comment entity.
 *
 * @see \Drupal\myeck\Entity\Myeck.
 */
class MyeckAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $entity_id = $entity->id();
    switch ($operation) {
      //сделать универсальную для всех myeck entity !!!!!!!!
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view ' . $entity_id . ' entity');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit ' . $entity_id . ' entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete ' . $entity_id . ' entity');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist, it
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $entity_id = $this->entityType->id();
    return AccessResult::allowedIfHasPermission($account, 'add ' . $entity_id . ' entity');
  }

}
