<?php

/**
 * @file
 * Contains schedule_event.page.inc.
 *
 * Page callback for Event entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event templates.
 *
 * Default template: schedule_event.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_schedule_event(array &$variables) {
  // Fetch Event Entity Object.
  $schedule_event = $variables['elements']['#schedule_event'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
