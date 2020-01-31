<?php

namespace Drupal\college_schedule_api;

use Drupal\college_schedule\Entity\EventInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Editor.
 */
class Editor implements EditorInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\Core\TempStore\PrivateTempStoreFactory definition.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempstorePrivate;

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
   * Constructs a new Editor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $tempstore_private
   *   Tempstore.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ConfigFactoryInterface $config_factory,
    PrivateTempStoreFactory $tempstore_private,
    LoggerInterface $logger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->tempstorePrivate = $tempstore_private;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function save(EventInterface $event) {
    // TODO: Implement save() method.
  }


  public function addLunch(int $group_program, string $week) {
    $start = \DateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $week);
    $start->modify('Monday this week');
    $interval = new \DateInterval('P1D');
    $days = new \DatePeriod($start, $interval, 4);
    $baseEvent = [
      'type' => 'lunch',
      'group_id' => $group_program,
    ];
    foreach ($days as $day) {
      $event = $baseEvent;
      $event['date'] = $day->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

      if (!$events = $this->storage()->loadByProperties($event)) {
        /** @var EventInterface $lunch */
        $lunch = $this->storage->create($event);
        $lunch->set('hour', 4);
        $lunch->setName('Lunch');
        $lunch->save();
      }
    }

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
