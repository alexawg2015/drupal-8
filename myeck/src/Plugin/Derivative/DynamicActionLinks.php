<?php

/**
 * @file
 * Contains \Drupal\myeck\Plugin\Derivative\DynamicActionLinks.
 */

namespace Drupal\myeck\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Defines dynamic local tasks.
 */
class DynamicActionLinks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $list_entity = \Drupal\myeck\MyeckConfig::getConfig();
    foreach ($list_entity as $id_entity => $entity_info) {
      $name_entity = $entity_info['entity_title'];
      //-------------------------
//      $this->derivatives['myeck.content_'.$id_entity.'.add'] = $base_plugin_definition;
      $this->derivatives['myeck.entity.' . $id_entity . '_add'] = $base_plugin_definition;
      # Which route will be called by the link
      $this->derivatives['myeck.entity.' . $id_entity . '_add']['title'] = "Add $name_entity";
      $this->derivatives['myeck.entity.' . $id_entity . '_add']['route_name'] = 'myeck.' . $id_entity . '.add';
      # Where will the link appear, defined by route name.
      $this->derivatives['myeck.entity.' . $id_entity . '_add']['appears_on'] = array(
        'entity.' . $id_entity . '.collection',
        'entity.' . $id_entity . '.canonical',
        'myeck.' . $id_entity . '.settings'
      );

      $this->derivatives['myeck.entity.' . $id_entity . '_list'] = $base_plugin_definition;
      # Which route will be called by the link
      $this->derivatives['myeck.entity.' . $id_entity . '_list']['title'] = "List of $name_entity";
      $this->derivatives['myeck.entity.' . $id_entity . '_list']['route_name'] = 'entity.' . $id_entity . '.collection';
      # Where will the link appear, defined by route name.
      $this->derivatives['myeck.entity.' . $id_entity . '_list']['appears_on'] = array(
        'myeck.' . $id_entity . '.settings'
      );
    }

    return $this->derivatives;
  }

}