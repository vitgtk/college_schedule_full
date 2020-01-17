<?php

/**
 * @file
 * Contains event_location.page.inc.
 *
 * Page callback for Location entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Location templates.
 *
 * Default template: event_location.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_event_location(array &$variables) {
  // Fetch EventLocation Entity Object.
  $event_location = $variables['elements']['#event_location'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
