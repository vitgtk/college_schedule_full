<?php

namespace Drupal\college_schedule_ui\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Provides a marquee render element.
 *
 * New render element types are defined as plugins. They live in the
 * Drupal\{module_name}\Element namespace and implement
 * \Drupal\Core\Render\Element\ElementInterface. They are annotated with either
 * \Drupal\Core\Render\Annotation\RenderElement or
 * \Drupal\Core\Render\Annotation\FormElement. And extend either the
 * \Drupal\Core\Render\Element\RenderElement, or
 * \Drupal\Core\Render\Element\FormElement base classes.
 *
 * In the annotation below we define the string "marquee" as the ID for this
 * plugin. That will also be the value used for the '#type' property in a render
 * array. For example:
 *
 * @code
 * $build['awesome'] = [
 *   '#type' => 'schedule_event',
 *   '#items' => [],
 * ];
 * @endcode
 *
 * View an example of this custom element in use in
 * \Drupal\render_example\Controller\RenderExampleController::arrays().
 *
 * @see plugin_api
 * @see render_example_theme()
 *
 * @RenderElement("schedule_day")
 */
class ScheduleDay extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {

    return [
      // See render_example_theme() where this new theme hook is declared.
      '#theme' => 'schedule_day_element',
      // Define a default #pre_render method. We will use this to handle
      // additional processing for the custom attributes we add below.
      '#pre_render' => [
        [self::class, 'preRenderScheduleDay'],
      ],
      '#items' => '',
      '#max_row' => 8,
      '#timetable' => NULL,
      '#empty' => '',
      '#date' => NULL,
      '#hours' => NULL,
      '#attributes' => [
        'class' => [
          'schedule-day-item',
        ],
        'data-schedule-day' => '',
      ],
    ];
  }

  /**
   * Pre-render callback; Process custom attribute options.
   *
   * @param array $element
   *   The renderable array representing the element with '#type' => 'marquee'
   *   property set.
   *
   * @return array
   *   The passed in element with changes made to attributes depending on
   *   context.
   */
  public static function preRenderScheduleDay(array $element) {
    /** @var \Drupal\college_schedule\Entity\TimetableInterface $timetable */
    $timetable = $element['#timetable'];
    if (!empty($element['#items'])) {
      $hours = array_keys($element['#items']);
      $lastHour = max($hours);

      $lastLine = ($element['#max_row'] > $lastHour) ? $element['#max_row'] : $lastHour;
      for ($i = 1; $i <= $lastLine; $i++) {
        if (!isset($element['#items'][$i])) {
          $element['#items'][$i] = [];
        }
      }
      ksort($element['#items']);
      $weight = 0;
      $element['#hours'] = [];
      foreach ($element['#items'] as $hour => $events) {
        $weight += 0.001;
        $element['#hours'] += [
          $hour => [],
        ];
        $element['#hours'][$hour] += [
          '#type' => 'schedule_hour',
          '#items' => $events,
          '#times' => $timetable->hourDuration($hour),
          '#hour_id' => $hour,
          '#weight' => $weight,
        ];
      }
    }

    $element['#attributes']['data-schedule-day'] = $element['#date'];

    return $element;
  }

}
