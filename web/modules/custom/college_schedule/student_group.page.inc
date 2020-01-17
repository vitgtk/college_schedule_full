<?php

/**
 * @file
 * Contains student_group.page.inc.
 *
 * Page callback for Student group entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Student group templates.
 *
 * Default template: student_group.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_student_group(array &$variables) {
  // Fetch StudentGroup Entity Object.
  $student_group = $variables['elements']['#student_group'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
