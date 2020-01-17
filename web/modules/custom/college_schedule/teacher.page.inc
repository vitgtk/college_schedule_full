<?php

/**
 * @file
 * Contains teacher.page.inc.
 *
 * Page callback for Teacher entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Teacher templates.
 *
 * Default template: teacher.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_teacher(array &$variables) {
  // Fetch Teacher Entity Object.
  $teacher = $variables['elements']['#teacher'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
