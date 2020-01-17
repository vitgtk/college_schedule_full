<?php

namespace Drupal\college_schedule\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Discipline entities.
 */
class DisciplineViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
