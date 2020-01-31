<?php


namespace Drupal\college_schedule_ui\Element;


use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
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
 *   '#type' => 'schedule_hour',
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
 * @RenderElement("schedule_hour")
 */
class ScheduleHour extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {

    return [
      '#theme' => 'schedule_hour_element',
      // Define a default #pre_render method. We will use this to handle
      // additional processing for the custom attributes we add below.
      '#pre_render' => [
        [self::class, 'preRenderScheduleHour'],
      ],
      '#hour_id' => NULL,
      '#is_empty' => NULL,
      '#times' => NULL,
      '#actions' => NULL,
      '#events' => [],
      '#attributes' => [
        'class' => [
          'cs-board--hour-item',
        ],
        'data-hour-id' => '',
      ],
    ];
  }

  /**
   * Pre-render callback; Process custom attribute options.
   *
   * @param array $element
   *   The renderable array representing the element with '#type' => 'schedule_hour'
   *   property set.
   *
   * @return array
   *   The passed in element with changes made to attributes depending on
   *   context.
   */
  public static function preRenderScheduleHour(array $element) {

    if (count($element['#items']) > 0) {
      $element['#is_empty'] = FALSE;
      $weight = 0;
      $type = 'none';
      foreach ($element['#items'] as $key => $event) {
        /** @var \Drupal\college_schedule\Entity\Event $event */
        $weight += 0.001;
        $type = $event->bundle();
        $element['#events'] += [
          $key => [],
        ];
        $element['#events'][$key] += [
          '#type' => 'schedule_event',
          '#entity' => $event,
          '#weight' => $weight,
        ];
      }
      $element['#attributes']['class'][] = 'cs-board--hour--type-' . $type;
    }
    else {
      $element['#is_empty'] = TRUE;
      $element['#attributes']['class'][] = 'hour-type--empty-hour';
      $element['#attributes']['class'][] = 'cs-board--hour--type-empty-hour';
    }
    $element['#actions']['add'] = Link::createFromRoute('Edit', '<front>');
    $element['#times'] = [
      '#markup' => $element['#times'],
    ];

    $element['#attributes']['hour_id'] = $element['#hour_id'];
    return $element;
  }

}
