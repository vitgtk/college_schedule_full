<?php

namespace Drupal\college_schedule;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Event entities.
 *
 * @ingroup college_schedule
 */
class EventListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Event ID');
    $header['name'] = $this->t('Name');
    $header['date'] = $this->t('Date');
    $header['hour'] = $this->t('Hour');
    $header['group'] = $this->t('Group');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\college_schedule\Entity\Event $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.schedule_event.edit_form',
      ['schedule_event' => $entity->id()]
    );
    $row['date'] = $entity->get('date')->value;
    $row['hour'] = $entity->get('hour')->value;
    $row['group'] = !$entity->get('group_id')->isEmpty() ? $entity->get('group_id')->entity->label() : '';
    return $row + parent::buildRow($entity);
  }

}
