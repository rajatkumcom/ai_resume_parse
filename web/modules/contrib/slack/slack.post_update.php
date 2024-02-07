<?php

/**
 * @file
 * Post update functions for Slack module.
 */

/**
 * Add new configuration item slack_link_names.
 */
function slack_post_update_slack_link_names() {
  \Drupal::configFactory()
    ->getEditable('slack.settings')
    ->set('slack_link_names', TRUE)
    ->save(TRUE);
}
