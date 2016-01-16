<?php


/**
 * @file
 * Contains \Drupal\myeck\Permission\MyeckPermissions.
 */

namespace Drupal\myeck\Permission;

use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines dynamic permissions.
 *
 * @ingroup eck
 */
class MyeckPermissions {
  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * Returns an array of entity type permissions.
   *
   * @return array
   *   The permissions.
   */
  public function entityTypePermissions() {
    $perms = array();
    $list_entity = \Drupal\myeck\MyeckConfig::getConfig();
    foreach ($list_entity as $id_entity => $entity_info) {
      $name_entity = $entity_info['entity_title'];
      $perms = array_merge($perms, $this->buildPermissions($id_entity, $name_entity));
    }
    return $perms;
  }

  /**
   * Builds a standard list of entity permissions for a given type.
   *
   * @param  $myeck_type
   *   The entity type.
   *
   * @return array
   *   An array of permissions.
   */
  public function buildPermissions($myeck_id, $myeck_type) {
    $type_id = $myeck_id;
    $type_params = array('%type_name' => $myeck_type);

    return array(
      "create $type_id entity" => array(
        'title' => $this->t('%type_name: Create new entity', $type_params),
      ),
      "edit own $type_id entity" => array(
        'title' => $this->t('%type_name: Edit own entity', $type_params),
      ),
      "edit any $type_id entity" => array(
        'title' => $this->t('%type_name: Edit any entity', $type_params),
      ),
      "delete own $type_id entity" => array(
        'title' => $this->t('%type_name: Delete own entity', $type_params),
      ),
      "delete any $type_id entity" => array(
        'title' => $this->t('%type_name: Delete any entity', $type_params),
      ),
      "view own $type_id entity" => array(
        'title' => $this->t('%type_name: View own entity', $type_params),
      ),
      "view any $type_id entity" => array(
        'title' => $this->t('%type_name: View any entity', $type_params),
      ),
      "administer $type_id entity" => array(
        'title' => $this->t('%type_name: administer entity', $type_params),
      ),
    );

  }

}