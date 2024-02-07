<?php

namespace Drupal\slack;

/**
 * Interface for the Slack service.
 */
interface SlackInterface {

  /**
   * Sends a message to a Slack channel.
   *
   * @param string $message
   *   The message sent to the channel.
   * @param string $channel
   *   The channel in the Slack service to send messages.
   * @param string $username
   *   The bot name displayed in the channel.
   * @param string|null $webhook_url
   *   The webhook url to use. This overrides the sitewide value in
   *   `slack.settings`.
   *
   * @return bool|object
   *   Slack response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function sendMessage($message, $channel = '', $username = '', string $webhook_url = NULL);

}
