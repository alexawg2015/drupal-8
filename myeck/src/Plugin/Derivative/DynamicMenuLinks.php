<?php

/**
 * @file
 * Contains \Drupal\myeck\Plugin\Derivative\DynamicMenuLinks.
 */

namespace Drupal\myeck\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Defines dynamic local tasks.
 */
class DynamicMenuLinks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $list_entity = \Drupal::config('myeck.list_entity')
      ->get('myeck_type_entity');
    foreach ($list_entity as $id_entity => $entity_info) {
      $name_entity = $entity_info['entity_title'];
      //-------------------------
      $this->derivatives['entity.' . $id_entity . '.collection'] = $base_plugin_definition;
      $this->derivatives['entity.' . $id_entity . '.collection']['title'] = "Listing $name_entity";
      $this->derivatives['entity.' . $id_entity . '.collection']['description'] = "Listing $name_entity";
      $this->derivatives['entity.' . $id_entity . '.collection']['route_name'] = 'entity.' . $id_entity . '.collection';
      $this->derivatives['entity.' . $id_entity . '.collection']['weight'] = 10;
      //-------------------------
      $this->derivatives['content_' . $id_entity . '.admin.structure.settings'] = $base_plugin_definition;
      $this->derivatives['content_' . $id_entity . '.admin.structure.settings']['title'] = "Configure $name_entity";
      $this->derivatives['content_' . $id_entity . '.admin.structure.settings']['description'] = "Configure $name_entity";
      $this->derivatives['content_' . $id_entity . '.admin.structure.settings']['route_name'] = 'myeck.' . $id_entity . '.settings';
      $this->derivatives['content_' . $id_entity . '.admin.structure.settings']['parent'] = 'system.admin_structure';
    }

    return $this->derivatives;
  }

}