<?php

/**
 * @file
 * Contains \Drupal\mymodule\Entity\MyTestEntity.
 */

namespace Drupal\myeck\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for My test entity entities.
 */
class MyeckEntityViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
//
//    $data['myeck']['table']['base'] = array(
//      'field' => 'id',
//      'title' => $this->t('Myeck entity'),
//      'help' => $this->t('The Myeck entity ID.'),
//    );

    return $data;
  }

}
