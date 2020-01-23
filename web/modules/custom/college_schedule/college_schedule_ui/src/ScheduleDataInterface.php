<?php

namespace Drupal\college_schedule_ui;

/**
 * Interface ScheduleDataInterface.
 */
interface ScheduleDataInterface {

  public function load(int $group, int $week, $saturday = FALSE);

}
