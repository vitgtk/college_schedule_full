<?php

namespace Drupal\college_schedule_ui;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\Routing\Route;

/**
 * Converts parameters for upcasting database record IDs to full std objects.
 *
 * @DCG
 * To use this converter specify parameter type in a relevant route as follows:
 * @code
 * college_schedule_ui.schedule_day_parameter_converter:
 *   path: example/{record}
 *   defaults:
 *     _controller: '\Drupal\college_schedule_ui\Controller\CollegeScheduleUiController::build'
 *   requirements:
 *     _access: 'TRUE'
 *   options:
 *     parameters:
 *       record:
 *        type: schedule_day
 * @endcode
 *
 * Note that for entities you can make use of existing parameter converter
 * provided by Drupal core.
 * @see \Drupal\Core\ParamConverter\EntityConverter
 */
class ScheduleDayParamConverter implements ParamConverterInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new ScheduleDayParamConverter.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The default database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    try {
      $date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $value);
    }
    catch (\Exception $e) {
      \Drupal::logger('college_schedule_ui')->error($e->getMessage());
      return NULL;
    }
    // Return NULL if record not found to trigger 404 HTTP error.
    return $date;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return !empty($definition['type']) && $definition['type'] == 'schedule_day';
  }

}
