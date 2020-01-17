<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Discipline entities.
 *
 * @ingroup college_schedule
 */
interface DisciplineInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Discipline name.
   *
   * @return string
   *   Name of the Discipline.
   */
  public function getName();

  /**
   * Sets the Discipline name.
   *
   * @param string $name
   *   The Discipline name.
   *
   * @return \Drupal\college_schedule\Entity\DisciplineInterface
   *   The called Discipline entity.
   */
  public function setName($name);

  /**
   * Gets the Discipline creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Discipline.
   */
  public function getCreatedTime();

  /**
   * Sets the Discipline creation timestamp.
   *
   * @param int $timestamp
   *   The Discipline creation timestamp.
   *
   * @return \Drupal\college_schedule\Entity\DisciplineInterface
   *   The called Discipline entity.
   */
  public function setCreatedTime($timestamp);

}
