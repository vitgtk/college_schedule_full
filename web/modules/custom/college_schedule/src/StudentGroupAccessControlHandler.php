<?php

namespace Drupal\college_schedule;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Student group entity.
 *
 * @see \Drupal\college_schedule\Entity\StudentGroup.
 */
class StudentGroupAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\college_schedule\Entity\StudentGroupInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished student group entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published student group entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit student group entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete student group entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add student group entities');
  }


}
