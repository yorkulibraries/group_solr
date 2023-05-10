<?php

namespace Drupal\group_solr\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\StringFilter;
use Drupal\views\Plugin\views\filter\BooleanOperator;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\Plugin\views\filter\Standard;
use Drupal\views\ViewExecutable;

/**
 * Filter by start and end date.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("access_control_with_group_filter")
 */
class AccessControlWithGroupFilter extends Standard{

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
  }

  /**
   * Override the query so that no filtering takes place if the user doesn't
   * select any options.
   */
  public function query() {
    $query = $this->query;

    // Get current user
    $uid = \Drupal::currentUser()->id();
    $current_user = \Drupal\user\Entity\User::load($uid);

    // Get groups which this user is belonged to
    $groups = array();
    $grp_membership_service = \Drupal::service('group.membership_loader');
    $grps = $grp_membership_service->loadByUser($current_user);
    foreach ($grps as $grp) {
      $groups[$grp->getGroup()->id()] = $grp->getGroup()->label();
    }

    if (count($groups) > 0) {
      // - from those group, get the taxonomy term in field_access_terms
      // - from those terms, get term ids
      $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree("islandora_access");
      $conditions = $query->createConditionGroup('OR');
      $conditions->addCondition("group_access_control", '200', "=");
      foreach ($terms as $term) {
        if (in_array($term->name, $groups)) {
          $conditions->addCondition('group_access_control', $term->name, "IN");
        }
      }
      $query->addConditionGroup($conditions);
    }
    else {
      $query->addCondition('group_access_control', "200", '=');
    }
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form["filter-descritpion"] = [
      "#markup" => $this->t("Adding this fitler, it will check current user 
      with the existing access control with Groups and filter the results.")
    ];
    unset($form['expose_button']);
  }
}