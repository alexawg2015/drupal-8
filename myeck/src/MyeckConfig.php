<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 02.01.2016
 * Time: 13:50
 */

namespace Drupal\myeck;


class MyeckConfig {

  public static function getConfig($type_entity = NULL) {
    $list_entity = \Drupal::config('myeck.list_entity')
      ->get('myeck_type_entity');
    if ($type_entity) {
      if (isset($list_entity[$type_entity])) {
        return $list_entity[$type_entity];
      }
      else {
        return array();
      }
    }
    else {
      return $list_entity;
    }

//    // Проверяем существование класса перед его использованием
//    if (class_exists('MyClass')) {
//      $myclass = new MyClass();
//    }
//    $a = '\namespacename\classname';
//    $obj = new $a; //

  }

  public static function getSettings($type_entity) {
    $entity_info = self::getConfig($type_entity);
    if (isset($entity_info['settings'])) {
      return $entity_info['settings'];
    }
    else {
      return array();
    }
  }

  public static function getForListBuilder($type_entity) {
    $entity_info = self::getConfig($type_entity);
    if (isset($entity_info['settings']['entity_label'])) {
      $show_fields = array();
      if (isset($entity_info['settings']['show_fields'])) {
//        foreach ($entity_info['settings']['show_fields'] as $show_field) {
//          $show_fields[$show_field] = $entity_info['settings']['fields'][$show_field]['label'];
//        }
        $show_fields = $entity_info['settings']['show_fields'];
      }
      $return_info = array(
        'entitty_id' => $entity_info['settings']['entitty_id'],
        'entity_label' => $entity_info['settings']['entity_label'],
        'show_fields' => $show_fields,
      );
      return $return_info;
    }
    else {
      return array();
    }
  }

  public static function getNameEntity($type_entity) {
    $entity_info = self::getConfig($type_entity);
    if (isset($entity_info['entity_title'])) {
      return $entity_info['entity_title'];
    }
    else {
      return '';
    }
  }

  public static function addEntityType(array $entity_info) {
    $list_entity = \Drupal::config('myeck.list_entity')
      ->get('myeck_type_entity');
    $list_entity[$entity_info['entity_id']] = $entity_info;
    $config = \Drupal::service('config.factory')
      ->getEditable('myeck.list_entity');
    $config->set('myeck_type_entity', $list_entity);
    $config->save();
    \Drupal::service("router.builder")->rebuild();
  }

  public static function deleteEntityType($entity_type) {
    $list_entity = \Drupal::config('myeck.list_entity')
      ->get('myeck_type_entity');
    if (isset($list_entity[$entity_type])) {
      unset($list_entity[$entity_type]);
      $config = \Drupal::service('config.factory')
        ->getEditable('myeck.list_entity');
      $config->set('myeck_type_entity', $list_entity);
      $config->save();
      \Drupal::service("router.builder")->rebuild();
    }
  }
}
