<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Location entities.
 *
 * @ingroup college_schedule
 */
interface EventLocationInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Location name.
   *
   * @return string
   *   Name of the Location.
   */
  public function getName();

  /**
   * Sets the Location name.
   *
   * @param string $name
   *   The Location name.
   *
   * @return \Drupal\college_schedule\Entity\EventLocationInterface
   *   The called Location entity.
   */
  public function setName($name);

  /**
   * Gets the Location creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Location.
   */
  public function getCreatedTime();

  /**
   * Sets the Location creation timestamp.
   *
   * @param int $timestamp
   *   The Location creation timestamp.
   *
   * @return \Drupal\college_schedule\Entity\EventLocationInterface
   *   The called Location entity.
   */
  public function setCreatedTime($timestamp);

}
