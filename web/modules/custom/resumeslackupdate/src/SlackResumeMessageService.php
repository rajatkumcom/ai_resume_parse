<?php

namespace Drupal\resumeslackupdate;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\slack\Slack;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

/**
 * Serice to send resume via interactive slack message.
 */
class SlackResumeMessageService extends Slack {

  /**
   * Constructor of the class.
   */
  public function __construct(ConfigFactoryInterface $config, ClientInterface $http_client, LoggerChannelFactoryInterface $logger, MessengerInterface $messenger) {
    parent::__construct($config, $http_client, $logger, $messenger);
  }

  /**
   * {@inheritdoc}
   */
  protected function sendRequest($webhook_url, $message, array $message_options = []) {
    $headers = [
      'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    $message_options['blocks'] = $this->structureMessage(unserialize($message));

    $sending_data = 'payload=' . urlencode(json_encode($message_options));
    $logger = $this->logger->get('slack');
    try {
      $response = $this->httpClient->request('POST', $webhook_url, [
        'headers' => $headers,
        'body' => $sending_data,
      ]);
      $logger->info('Message was successfully sent!');
      return $response;
    }
    catch (ServerException $e) {
      $logger->error('Server error! It may appear if you try to use unexisting chatroom.');
      watchdog_exception('slack', $e);
      return FALSE;
    }
    catch (RequestException $e) {
      $logger->error('Request error! It may appear if you entered the invalid Webhook value.');
      watchdog_exception('slack', $e);
      return FALSE;
    }
  }

  /**
   * Function to structure the message into slack interactive message.
   */
  protected function structureMessage(array $message): array {
    if (!count($message)) {
      return [];
    }
    $formattedMessage = [
      [
        "type" => "section",
        "text" => [
          "type" => "mrkdwn",
          "text" => "We found the following resumes for today",
        ],
      ],
    ];
    $limit = count($message) >= 8 ? 8 : count($message);
    $remaining = count($message) <= 8 ? 0 : count($message) - 8;
    for ($i = 0; $i < $limit; $i++) {
      $value = $message[$i];
      extract($value);
      $formattedMessage[] = [
        "type" => "divider",
      ];
      $formattedMessage[] = [
        "type" => "section",
        "fields" => [
          [
            "type" => "mrkdwn",
            "text" => "*Name:*\n$name",
          ],
          [
            "type" => "mrkdwn",
            "text" => "*Experience:*\n$exp",
          ],
        ],
      ];
      $formattedMessage[] = [
        "type" => "section",
        "text" => [
          "type" => "mrkdwn",
          "text" => "*Skills:*\n$skills",
        ],
      ];
      $formattedMessage[] = [
        "type" => "section",
        "fields" => [
          [
            "type" => "mrkdwn",
            "text" => "*Current CTC:*\n$cur_ctc",
          ],
          [
            "type" => "mrkdwn",
            "text" => "*Expected CTC:*\n$ex_ctc",
          ],
        ],
      ];
      $formattedMessage[] = [
        "type" => "section",
        "text" => [
          "type" => "mrkdwn",
          "text" => "<$url|Click Here> to visit profile.",
        ],
      ];
    }
    if ($remaining > 0) {
      $formattedMessage[] = [
        "type" => "section",
        "text" => [
          "type" => "mrkdwn",
          "text" => "<https://www.google.com|Show More> ($remaining)",
        ],
      ];
    }
    return $formattedMessage;
  }

}
