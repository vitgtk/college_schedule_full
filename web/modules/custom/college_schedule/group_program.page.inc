<?php

/**
 * @file
 * Contains group_program.page.inc.
 *
 * Page callback for Group program entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Group program templates.
 *
 * Default template: group_program.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_group_program(array &$variables) {
  // Fetch GroupProgram Entity Object.
  $group_program = $variables['elements']['#group_program'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
