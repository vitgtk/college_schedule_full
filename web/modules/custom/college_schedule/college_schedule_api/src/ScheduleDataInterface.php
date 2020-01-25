<?php

namespace Drupal\college_schedule_api;

/**
 * Interface ScheduleDataInterface.
 */
interface ScheduleDataInterface {

  /**
   * Return schedule data.
   *
   * @param int $group
   *   Group ID.
   * @param string $week
   *   Week DATE_STORAGE_FORMAT string.
   * @param bool $saturday
   *   Bool flag.
   *
   * @return array
   *   Date array.
   */
  public function load(int $group, string $week, $saturday = FALSE);

}
