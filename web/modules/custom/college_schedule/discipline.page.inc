<?php

/**
 * @file
 * Contains discipline.page.inc.
 *
 * Page callback for Discipline entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Discipline templates.
 *
 * Default template: discipline.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_discipline(array &$variables) {
  // Fetch Discipline Entity Object.
  $discipline = $variables['elements']['#discipline'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
