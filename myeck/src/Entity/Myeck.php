<?php
/**
 * @file
 * Contains \Drupal\myeck\Entity\myeck.
 */

namespace Drupal\myeck\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\myeck\MyeckInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the myeck entity.
 *
 * @ingroup myeck
 *
 * This is the main definition of the entity type. From it, an entityType is
 * derived. The most important properties in this example are listed below.
 *
 * id: The unique identifier of this entityType. It follows the pattern
 * 'moduleName_xyz' to avoid naming conflicts.
 *
 * label: Human readable name of the entity type.
 *
 * handlers: Handler classes are used for different tasks. You can use
 * standard handlers provided by D8 or build your own, most probably derived
 * from the standard class. In detail:
 *
 * - view_builder: we use the standard controller to view an instance. It is
 *   called when a route lists an '_entity_view' default for the entityType
 *   (see routing.yml for details. The view can be manipulated by using the
 *   standard drupal tools in the settings.
 *
 * - list_builder: We derive our own list builder class from the
 *   entityListBuilder to control the presentation.
 *   If there is a view available for this entity from the views module, it
 *   overrides the list builder. @todo: any view? naming convention?
 *
 * - form: We derive our own forms to add functionality like additional fields,
 *   redirects etc. These forms are called when the routing list an
 *   '_entity_form' default for the entityType. Depending on the suffix
 *   (.add/.edit/.delete) in the route, the correct form is called.
 *
 * - access: Our own accessController where we determine access rights based on
 *   permissions.
 *
 * More properties:
 *
 *  - base_table: Define the name of the table used to store the data. Make sure
 *    it is unique. The schema is automatically determined from the
 *    BaseFieldDefinitions below. The table is automatically created during
 *    installation.
 *
 *  - fieldable: Can additional fields be added to the entity via the GUI?
 *    Analog to content types.
 *
 *  - entity_keys: How to access the fields. Analog to 'nid' or 'uid'.
 *
 *  - links: Provide links to do standard tasks. The 'edit-form' and
 *    'delete-form' links are added to the list built by the
 *    entityListController. They will show up as action buttons in an additional
 *    column.
 *
 * There are many more properties to be used in an entity type definition. For
 * a complete overview, please refer to the '\Drupal\Core\Entity\EntityType'
 * class definition.
 *
 * The following construct is the actual definition of the entity type which
 * is read and cached. Don't forget to clear cache after changes.
 *
 * @ContentEntityType(
 *   id = "myeck",
 *   label = @Translation("myeck entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\myeck\Entity\MyeckListBuilder",
 *     "views_data" = "Drupal\myeck\Entity\MyeckEntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\myeck\Form\MyeckForm",
 *       "edit" = "Drupal\myeck\Form\MyeckForm",
 *       "delete" = "Drupal\myeck\Form\MyeckDeleteForm",
 *     },
 *     "access" = "Drupal\myeck\MyeckAccessControlHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "myeck",
 *   admin_permission = "administer myeck entity",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/content-myeck/{myeck}",
 *     "edit-form" = "/content-myeck/{myeck}/edit",
 *     "delete-form" = "/content-myeck/{myeck}/delete",
 *     "collection" = "/content-myeck/list"
 *   },
 *   field_ui_base_route = "myeck.myeck.settings",
 * )
 */
class Myeck extends ContentEntityBase implements MyeckInterface {

  use EntityChangedTrait;

  /**
   * use standard fields in entity
   */
  protected $use_uid = FALSE;
  protected $use_uuid = TRUE;
  protected $use_created = FALSE;
  protected $use_changed = FALSE;

  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = array()) {
    parent::__construct($values, $entity_type, $bundle, $translations);
    $this->setDefaultVariable();
  }

  public function setDefaultVariable() {
//     $this->use_uid = false;
//     $this->use_uuid = true;
//     $this->use_created = false;
//     $this->use_changed = false;
  }

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    if ($this->use_created) {
      return $this->get('created')->value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    if ($this->use_changed) {
      return $this->get('changed')->value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    if ($this->use_changed) {
      $this->set('changed', $timestamp);
      return $this;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
//    $changed = $this->getUntranslated()->getChangedTime();
//    foreach ($this->getTranslationLanguages(FALSE) as $language)    {
//      $translation_changed = $this->getTranslation($language->getId())->getChangedTime();
//      $changed = max($translation_changed, $changed);
//    }
//    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    if ($this->use_uid) {
      return $this->get('user_id')->entity;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    if ($this->use_uid) {
      return $this->get('user_id')->target_id;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    if ($this->use_uid) {
      $this->set('user_id', $uid);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    if ($this->use_uid) {
      $this->set('user_id', $account->id());
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the myeck entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the myeck entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Contact entity.'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
//    $fields['uid'] = BaseFieldDefinition::create('integer')
//      ->setLabel(t('Owner'))
//      ->setDescription(t('Owner of the ticket.'))
//      ->setSettings(array(
//        'default_value' => 0,
//      ));

//    $fields['created'] = BaseFieldDefinition::create('created')
//      ->setLabel(t('Created'))
//      ->setDescription(t('The time that the myesk was created.'))
//      ->setDisplayOptions('view', array(
//        'label' => 'Ticket was created',
//        'type' => 'string',
//        'weight' => -5,
//      ))
//      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
