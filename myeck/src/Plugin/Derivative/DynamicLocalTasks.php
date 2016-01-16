<?php

/**
 * @file
 * Contains \Drupal\myeck\Plugin\Derivative\DynamicLocalTasks.
 */

namespace Drupal\myeck\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Defines dynamic local tasks.
 */
class DynamicLocalTasks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $list_entity = \Drupal\myeck\MyeckConfig::getConfig();
    foreach ($list_entity as $id_entity => $entity_info) {
      //-------------------------
      $this->derivatives[$id_entity . '.settings_tab'] = $base_plugin_definition;
      $this->derivatives[$id_entity . '.settings_tab']['title'] = "Settings";
      $this->derivatives[$id_entity . '.settings_tab']['route_name'] = 'myeck.' . $id_entity . '.settings';
      $this->derivatives[$id_entity . '.settings_tab']['base_route'] = 'myeck.' . $id_entity . '.settings';
      //-------------------------
      $this->derivatives[$id_entity . '.view'] = $base_plugin_definition;
      $this->derivatives[$id_entity . '.view']['title'] = "View";
      $this->derivatives[$id_entity . '.view']['route_name'] = 'entity.' . $id_entity . '.canonical';
      $this->derivatives[$id_entity . '.view']['base_route'] = 'entity.' . $id_entity . '.canonical';
      //-------------------------
      $this->derivatives[$id_entity . '.page_edit'] = $base_plugin_definition;
      $this->derivatives[$id_entity . '.page_edit']['title'] = "Edit";
      $this->derivatives[$id_entity . '.page_edit']['route_name'] = 'entity.' . $id_entity . '.edit_form';
      $this->derivatives[$id_entity . '.page_edit']['base_route'] = 'entity.' . $id_entity . '.canonical';
      //-------------------------
      $this->derivatives[$id_entity . '.delete_confirm'] = $base_plugin_definition;
      $this->derivatives[$id_entity . '.delete_confirm']['title'] = "Delete";
      $this->derivatives[$id_entity . '.delete_confirm']['route_name'] = 'entity.' . $id_entity . '.delete_form';
      $this->derivatives[$id_entity . '.delete_confirm']['base_route'] = 'entity.' . $id_entity . '.canonical';
      $this->derivatives[$id_entity . '.delete_confirm']['weight'] = 10;
    }

    return $this->derivatives;
  }

}