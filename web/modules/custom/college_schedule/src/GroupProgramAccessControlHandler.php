<?php

namespace Drupal\college_schedule;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Group program entity.
 *
 * @see \Drupal\college_schedule\Entity\GroupProgram.
 */
class GroupProgramAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\college_schedule\Entity\GroupProgramInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished group program entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published group program entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit group program entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete group program entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add group program entities');
  }


}
