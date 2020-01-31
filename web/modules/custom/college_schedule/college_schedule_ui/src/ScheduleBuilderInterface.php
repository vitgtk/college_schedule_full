<?php

namespace Drupal\college_schedule_ui;

/**
 * Interface ScheduleBuilderInterface.
 */
interface ScheduleBuilderInterface {

  /**
   * @param int $group_program
   * @param string $week
   * @param bool $saturday
   *
   * @return mixed
   */
  public function build(int $group_program, string $week, bool $saturday = FALSE);

}
