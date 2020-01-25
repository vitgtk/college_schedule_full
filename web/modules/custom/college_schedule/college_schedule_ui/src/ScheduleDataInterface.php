<?php

namespace Drupal\college_schedule_ui;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Interface ScheduleDataInterface.
 */
interface ScheduleDataInterface {

  public function load(int $group, string $week, $saturday = FALSE);

}
