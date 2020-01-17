<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Student group entities.
 *
 * @ingroup college_schedule
 */
interface StudentGroupInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Student group name.
   *
   * @return string
   *   Name of the Student group.
   */
  public function getName();

  /**
   * Sets the Student group name.
   *
   * @param string $name
   *   The Student group name.
   *
   * @return \Drupal\college_schedule\Entity\StudentGroupInterface
   *   The called Student group entity.
   */
  public function setName($name);

  /**
   * Gets the Student group creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Student group.
   */
  public function getCreatedTime();

  /**
   * Sets the Student group creation timestamp.
   *
   * @param int $timestamp
   *   The Student group creation timestamp.
   *
   * @return \Drupal\college_schedule\Entity\StudentGroupInterface
   *   The called Student group entity.
   */
  public function setCreatedTime($timestamp);

}
