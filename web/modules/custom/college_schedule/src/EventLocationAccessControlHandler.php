<?php

namespace Drupal\college_schedule;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Location entity.
 *
 * @see \Drupal\college_schedule\Entity\EventLocation.
 */
class EventLocationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\college_schedule\Entity\EventLocationInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished location entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published location entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit location entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete location entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add location entities');
  }


}
