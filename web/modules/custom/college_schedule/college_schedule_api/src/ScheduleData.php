<?php

namespace Drupal\college_schedule_api;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ScheduleData.
 */
class ScheduleData implements ScheduleDataInterface {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\Core\Cache\CacheBackendInterface definition.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheDefault;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Event storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a new ScheduleData object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_default
   *   Cache.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    CacheBackendInterface $cache_default,
    EntityTypeManagerInterface $entity_type_manager,
    LoggerInterface $logger
  ) {
    $this->configFactory = $config_factory;
    $this->cacheDefault = $cache_default;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function load(int $group, string $week, $saturday = FALSE) {
    $data = [];

    $weekDate = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $week);
    $weekDate->modify('+5 day');
    $endDate = $weekDate->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

    $query = $this->storage()->getQuery();
    $query
      ->condition('group_id', $group)
      ->condition('date.value', $endDate, '<=')
      ->condition('date.value', $week, '>=')
      ->sort('date')->sort('subgroup');

    $ids = $query->execute();
    /** @var \Drupal\Core\Entity\ContentEntityInterface[] $items */
    try {
      $items = $this->storage()->loadMultiple($ids);
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
      return $data;
    }

    foreach ($items as $id => $item) {
      $date = $item->get('date')->value;
      $hour = $item->get('hour')->value;
      $data[$date][$hour][$id] = $item;

    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getNotFreeHours(int $group, string $day) {
    $hours = [];
    $query = $this->storage()->getQuery();
    $query->condition('status', 1)
      ->condition('group_id', $group)
      ->condition('date', $day)
      ->notExists('subgroup');

    $eids = $query->execute();


    $items = $this->storage()->loadMultiple($eids);
    dpm($items, 'eids');
  }

  /**
   * Event storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   Event storage.
   */
  private function storage() {
    if (empty($this->storage)) {
      try {
        /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
        $this->storage = $this->entityTypeManager->getStorage('schedule_event');
      }
      catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
        $this->logger->error($e->getMessage());
      }
    }
    return $this->storage;
  }

}
