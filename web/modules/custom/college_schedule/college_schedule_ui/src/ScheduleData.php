<?php

namespace Drupal\college_schedule_ui;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Class ScheduleData.
 */
class ScheduleData implements ScheduleDataInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Cache\CacheBackendInterface definition.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheDefault;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new ScheduleData object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CacheBackendInterface $cache_default, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cacheDefault = $cache_default;
    $this->configFactory = $config_factory;
  }

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
   *   Date arrat.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function load(int $group, string $week, $saturday = FALSE) {
    /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
    $storage = $this->entityTypeManager->getStorage('schedule_event');

    $weekDate = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $week);
    $weekDate->modify('+5 day');
    $endDate = $weekDate->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

    $query = $storage->getQuery();
    $query
      // ->condition('type', 'training')
      ->condition('group', $group)
      ->condition('date.value', $endDate, '<=')
      ->condition('date.value', $week, '>=')
      ->sort('date')->sort('subgroup');

    $ids = $query->execute();
    /** @var \Drupal\Core\Entity\ContentEntityInterface[] $items */
    $items = $storage->loadMultiple($ids);

    $data = [];
    foreach ($items as $id => $item) {
      $date = $item->get('date')->value;
      $hour = $item->get('hour')->value;
      $data[$date][$hour][$id] = $item;

    }
    return $data;
  }

  public function loadByDay(int $group, string $day) {
    $storage = $this->entityTypeManager->getStorage('schedule_event');
    $query = $storage->getQuery();
    $query
      // ->condition('type', 'training')
      ->condition('group', $group)
      ->condition('date.value', $day, '=')
      ->sort('date');

    $ids = $query->execute();
    $items = $storage->loadMultiple($ids);
    return $items;
  }

  public function loadWeek(int $group, DrupalDateTime $week, $saturday = FALSE) {}

}
