<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Timetable entities.
 */
interface TimetableInterface extends ConfigEntityInterface {

  /**
   * Return timing list.
   *
   * @return array
   *   Timing list
   */
  public function timing();

  /**
   * Day length.
   *
   * @param int $last
   *   Last lesson number.
   *
   * @return string
   *   Duration
   */
  public function duration($last = 15);

  /**
   * Hour length.
   *
   * @param int $hourId
   *   Hour ID.
   *
   * @return string|null
   */
  public function hourDuration($hourId);

}
