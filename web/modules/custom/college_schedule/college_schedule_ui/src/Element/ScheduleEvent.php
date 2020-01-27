<?php


namespace Drupal\college_schedule_ui\Element;


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
 *   '#content' => 'Whoa cools, a marquee!',
 * ];
 * @endcode
 *
 * View an example of this custom element in use in
 * \Drupal\render_example\Controller\RenderExampleController::arrays().
 *
 * @see plugin_api
 * @see render_example_theme()
 *
 * @RenderElement("schedule_event")
 */
class ScheduleEvent extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {

    return [
      '#theme' => 'schedule_event_element',
      // Define a default #pre_render method. We will use this to handle
      // additional processing for the custom attributes we add below.
      '#pre_render' => [
        [self::class, 'preRenderEvent'],
      ],
      '#entity' => NULL,
      '#attributes' => [
        'class' => [
          'cs-board--event-item',
        ],
        'data-event-id' => '',
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
  public static function preRenderEvent(array $element) {
    /** @var \Drupal\college_schedule\Entity\Event $event */
    $event = $element['#entity'];
    $element['#event_id'] = $event->id();
    $element['#type'] = $event->bundle();

    if ($event->bundle() == 'training') {
      $element['#label'] = $event->get('discipline')->entity->label();
      $element['#location'] = $event->get('location')->entity->label();
      $element['#subgroup'] = $event->get('subgroup')->value;
    }
    elseif ($event->bundle() == 'lunch') {
      $element['#label'] = $event->label();
    }

    $element['#attributes']['data-event-id'] = $event->id();
    $element['#attributes']['data-event-type'] = $event->bundle();

    return $element;
  }

}
