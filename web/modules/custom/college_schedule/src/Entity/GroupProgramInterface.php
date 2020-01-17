<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Group program entities.
 *
 * @ingroup college_schedule
 */
interface GroupProgramInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Group program name.
   *
   * @return string
   *   Name of the Group program.
   */
  public function getName();

  /**
   * Sets the Group program name.
   *
   * @param string $name
   *   The Group program name.
   *
   * @return \Drupal\college_schedule\Entity\GroupProgramInterface
   *   The called Group program entity.
   */
  public function setName($name);

  /**
   * Gets the Group program creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Group program.
   */
  public function getCreatedTime();

  /**
   * Sets the Group program creation timestamp.
   *
   * @param int $timestamp
   *   The Group program creation timestamp.
   *
   * @return \Drupal\college_schedule\Entity\GroupProgramInterface
   *   The called Group program entity.
   */
  public function setCreatedTime($timestamp);

}
