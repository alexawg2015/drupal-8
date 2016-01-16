<?php

/**
 * @file
 * Contains \Drupal\content_entity_example\Entity\Controller\ContentEntityExampleController.
 */

namespace Drupal\myeck\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;


/**
 * Provides a list controller for content_entity_example entity.
 *
 * @ingroup myeck
 */
class MyeckListBuilder extends EntityListBuilder {
  // use LinkGeneratorTrait;

  /**
   * lists of fields, which show in lists of entity
   */
  protected $show_fields = array();
  protected $entity_label = '';
  protected $entity_id = '';


  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    parent::__construct($entity_type, $storage);
    $type = $entity_type->id();
    $entity_info = \Drupal\myeck\MyeckConfig::getForListBuilder($type);

    $this->entity_label = empty($entity_info['entity_label']) ? '' : $entity_info['entity_label'];
    $this->entity_id = empty($entity_info['entity_id']) ? 'id' : $entity_info['entity_id'];
    $this->show_fields = array($this->entity_id => $type . ' ID');
    $this->show_fields += empty($entity_info['show_fields']) ? array() : $entity_info['show_fields'];
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
//  public function render() {
//    $build['description'] = array(
//      '#markup' => $this->t('Myeck are fieldable entities. You can manage the fields on the <a href="@adminlink">Myeck admin page</a>.', array(
//        '@adminlink' => \Drupal::urlGenerator()->generateFromRoute('myeck.myeck_entity.settings'),
//      )),
//    );
//    $build['table'] = parent::render();
//    return $build;
//  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    foreach ($this->show_fields as $name => $label) {
      $header[$name] = $this->t($label);
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\myeck\Entity\Myeck */
    foreach ($this->show_fields as $name => $label) {
      switch ($name) {
        case $this->entity_id:
          $row[$name] = $entity->link($entity->id());
          break;
        case $this->entity_label:
          $row[$name] = $entity->link($entity->$name->value);
          break;
        case 'user_id':
          $user_id = $entity->$name->target_id;
          $user = \Drupal\user\Entity\User::load($user_id);
          $row[$name] = $user->getDisplayName();
          break;
        case 'created':
        case 'changed':
          $date = date('Y-m-d H:i:s', $entity->$name->value);
          $row[$name] = $date;
          break;
        case 'language':
        case 'langcode':
          $row[$name] = $entity->langcode->value;
          break;
        default:
          $row[$name] = $entity->$name->value;

      }
    }


//    $row['name'] = $entity->link();
//    $row['first_name'] = $entity->first_name->value;
//    $row['gender'] = $entity->gender->value;

//    $row['name'] = $this->l(
//      $this->getLabel($entity),
//      new Url(
//        'entity.my_test_entity.edit_form', array(
//          'my_test_entity' => $entity->id(),
//        )
//      )
//    );

    return $row + parent::buildRow($entity);
  }

}
