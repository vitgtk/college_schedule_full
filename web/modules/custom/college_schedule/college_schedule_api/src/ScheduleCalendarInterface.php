<?php

namespace Drupal\college_schedule_api;

/**
 * Interface ScheduleCalendarInterface.
 */
interface ScheduleCalendarInterface {

  /**
   * @param null $day
   *
   * @return \Drupal\college_schedule\Entity\TimetableInterface
   */
  public function getTimetable($day = NULL);

}
