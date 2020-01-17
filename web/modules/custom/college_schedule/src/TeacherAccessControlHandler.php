<?php

namespace Drupal\college_schedule;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Teacher entity.
 *
 * @see \Drupal\college_schedule\Entity\Teacher.
 */
class TeacherAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\college_schedule\Entity\TeacherInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished teacher entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published teacher entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit teacher entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete teacher entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add teacher entities');
  }


}
