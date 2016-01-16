<?php
/**
 * @file
 * Contains \Drupal\content_entity_example\ContactInterface.
 */

namespace Drupal\myeck;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Myeck entity.
 *
 * We have this interface so we can join the other interfaces it extends.
 *
 * @ingroup Myeck
 */
interface MyeckInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
