<?php

namespace Drupal\college_schedule_api;

use Drupal\college_schedule\Entity\EventInterface;

/**
 * Interface EditorInterface.
 */
interface EditorInterface {

  /**
   * Save Event entity.
   *
   * @param \Drupal\college_schedule\Entity\EventInterface $event
   *   Event entity.
   *
   * @return mixed
   *   Result
   */
  public function save(EventInterface $event);

}
