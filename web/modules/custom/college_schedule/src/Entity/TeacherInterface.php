<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Teacher entities.
 *
 * @ingroup college_schedule
 */
interface TeacherInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Teacher name.
   *
   * @return string
   *   Name of the Teacher.
   */
  public function getName();

  /**
   * Sets the Teacher name.
   *
   * @param string $name
   *   The Teacher name.
   *
   * @return \Drupal\college_schedule\Entity\TeacherInterface
   *   The called Teacher entity.
   */
  public function setName($name);

  /**
   * Gets the Teacher creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Teacher.
   */
  public function getCreatedTime();

  /**
   * Sets the Teacher creation timestamp.
   *
   * @param int $timestamp
   *   The Teacher creation timestamp.
   *
   * @return \Drupal\college_schedule\Entity\TeacherInterface
   *   The called Teacher entity.
   */
  public function setCreatedTime($timestamp);

}
