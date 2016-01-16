<?php

/**
 * @file
 * Contains \Drupal\myeck\Routing\MyeckRoutes.
 */

namespace Drupal\myeck\Routing;

use Drupal\myeck\Entity\MyeckEntityType;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Validator\Constraints\Null;

/**
 * Defines dynamic routes.
 *
 * @ingroup eck
 */
class MyeckRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $list_entity = \Drupal::config('myeck.list_entity')
      ->get('myeck_type_entity');
    $route_collection = new RouteCollection();
    foreach ($list_entity as $id_entity => $entity_info) {
      $name_entity = $entity_info['entity_title'];
      //---------------
      $route_list = new Route(
        '/content-' . $id_entity . '/{' . $id_entity . '}',
        array(
          '_entity_view' => $id_entity,
          '_title' => $name_entity . ' content',
        ),
        array(
          '_permission' => "view any $id_entity entity",
        )
      );
      $route_collection->add('entity.' . $id_entity . '.canonical', $route_list);
      //---------------
      $route_list = new Route(
        '/content-' . $id_entity . '/list',
        array(
          '_entity_list' => $id_entity,
          '_title' => $name_entity . ' list',
        ),
        array(
          '_permission' => "view any $id_entity entity",
        )
      );
      $route_collection->add('entity.' . $id_entity . '.collection', $route_list);
      //---------------
      $route_list = new Route(
        '/content-' . $id_entity . '/add',
        array(
          '_entity_form' => $id_entity . '.add',
          '_title' => $name_entity . ' Add',
        ),
        array(
          '_permission' => "create $id_entity entity",
        )
      );
      $route_collection->add('myeck.' . $id_entity . '.add', $route_list);
      //---------------
      $route_list = new Route(
        '/content-' . $id_entity . '/{' . $id_entity . '}/edit',
        array(
          '_entity_form' => $id_entity . '.edit',
          '_title' => $name_entity . ' edit',
        ),
        array(
          '_permission' => "edit any $id_entity entity",
        )
      );
      $route_collection->add('entity.' . $id_entity . '.edit_form', $route_list);
      //---------------
      $route_list = new Route(
        '/content-' . $id_entity . '/{' . $id_entity . '}/delete',
        array(
          '_entity_form' => $id_entity . '.delete',
          '_title' => $name_entity . ' delete',
        ),
        array(
          '_permission' => "delete any $id_entity entity",
        )
      );
      $route_collection->add('entity.' . $id_entity . '.delete_form', $route_list);
      //---------------
      $route_list = new Route(
        'admin/structure/' . $id_entity . '_settings',
        array(
          '_form' => '\Drupal\\' . $entity_info['module_name'] . '\Form\\' . $entity_info['entity_class'] . 'SettingsForm',
          '_title' => $name_entity . ' Settings',
        ),
        array(
          '_permission' => "administer $id_entity entity",
        )
      );
      $route_collection->add('myeck.' . $id_entity . '.settings', $route_list);
      //---------------
    }
    return $route_collection;
  }

}
