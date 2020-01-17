<?php

namespace Drupal\college_schedule;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Timetable entities.
 */
class TimetableListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Timetable');
    $header['id'] = $this->t('Machine name');
    $header['length'] = $this->t('Day length');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\college_schedule\Entity\TimetableInterface $entity */
    dpm($entity);
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['length'] = $entity->duration();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

}
