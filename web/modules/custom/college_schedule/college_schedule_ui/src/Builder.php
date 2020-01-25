<?php

namespace Drupal\college_schedule_ui;

use Drupal\college_schedule_api\ScheduleDataInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Class Builder.
 */
class Builder implements ScheduleBuilderInterface {

  use StringTranslationTrait;
  /**
   * Drupal\college_schedule_ui\ScheduleDataInterface definition.
   *
   * @var \Drupal\college_schedule_ui\ScheduleDataInterface
   */
  protected $scheduleData;

  /**
   * Constructs a new Builder object.
   *
   * @param \Drupal\college_schedule_api\ScheduleDataInterface $schedule_data
   *   Schedule data.
   */
  public function __construct(ScheduleDataInterface $schedule_data) {
    $this->scheduleData = $schedule_data;
  }

  /**
   * @param $group_program
   * @param $week
   * @param bool $saturday
   *
   * @return array
   *   Render array.
   */
  public function build(int $group_program, string $week, bool $saturday = FALSE) {
    $build = [];
    $data = $this->scheduleData->load($group_program, $week, $saturday);

    $days = $this->getDaysList($week, $saturday);

    $build['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'schedule-area-' . $week,
        'data-week' => $week,
        'data-group' => $group_program,
        'data-saturday' => $saturday,
        'class' => ['schedule-area-days', 'cs-board--days-area'],
      ],
    ];
    if ($saturday) {
      $build['content']['#attributes']['class'][] = 'schedule-area-days--saturday-on';
    }

    foreach ($days as $date_storage) {
      $container = [
        '#type' => 'container',
        '#attributes' => [
          'id' => 's_dashboard--day-container--' . $date_storage,
          'data-container-day' => $date_storage,
          'class' => ['cs-board--day-area'],
        ],
      ];
      $container['actions'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['cs-board--day-actions'],
          'data-container-day' => $date_storage,
        ],
      ];
      $container['schedule_day'] = [
        '#type' => 'schedule_day',
        '#content' => $date_storage,
        '#date' => $date_storage,
        '#items' => isset($data[$date_storage]) ? $data[$date_storage] : [], /* events group by hour */
        '#attributes' => [
          'class' => ['cs-board--day-grid'],
        ],
      ];

      $container['actions']['add'] = [
        '#type' => 'link',
        '#title' => $this->t('Add'),
        '#url' => Url::fromRoute('college_schedule_ui.events_form', [
          'group' => $group_program,
          'day' => $date_storage,
        ]),
        '#options' => [
          'attributes' => [
            'class' => ['use-ajax', 'button', 'button--add', 'button--extrasmall'],
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode([
              'width' => 900,
            ]),
          ],
        ],
      ];
      $build['content'][] = $container;
    }
    return $build;
  }

  /**
   * Return days array for week.
   *
   * @param string $week
   *   Monday DateTime.
   * @param bool $saturday
   *   Flag is add Saturday day.
   *
   * @return array
   *   Days array.
   */
  private function getDaysList(string $week, $saturday = FALSE) {
    $days = [];
    $count = $saturday ? 6 : 5;
    $date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $week);
    for ($i = 0; $i < $count; $i++) {
      $day = $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);
      $days[] = $day;
      $date->modify('+ 24 hours');
    }
    return $days;
  }

}
