<?php

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;

$app->group('/api', function() use ($app, $settings) {
  $app->post('/', function() use ($app, $settings) {
    $bot = $app->bot;
    $logger = $app->logger;
    $headers = $app->request->headers;
    $signature = $headers['X-Line-Signature'];

    if (empty($signature)) {
      writeResponse($app->response, 400, 'Bad request');
      return;
    }

    try {
      $events = $bot->parseEventRequest($app->request->getBody(), $signature);
    } catch (InvalidSignatureException $e) {
      writeResponse($app->response, 400, 'Invalid signature');
      return;
    } catch (UnknownEventTypeException $e) {
      writeResponse($app->response, 400, 'Unknown event type has come');
      return;
    } catch (UnknownMessageTypeException $e) {
      writeResponse($app->response, 400, 'Unknown message type has come');
      return;
    } catch (InvalidEventRequestException $e) {
      writeResponse($app->response, 400, 'Invalid event request');
      return;
    }

    foreach ($events as $event) {
      $handler = null;

      if ($event instanceof MessageEvent) {
        if ($event instanceof TextMessage) {
          require_once ROUTEDIR . 'handler/message.php';
          $handler = new TextMessageHandler($event, $app);
        } else {
          $replytext = 'Duh, saya hanya mengerti pesan teks.';
          $response = $bot->replyText($event->getReplyToken(), $replytext);
          continue;
        }
      } elseif ($event instanceof FollowEvent) {
        require_once ROUTEDIR . 'handler/follow.php';
        $handler = new FollowEventHandler($event, $app);
      } elseif ($event instanceof UnfollowEvent) {
        require_once ROUTEDIR . 'handler/unfollow.php';
        $handler = new UnfollowEventHandler($event, $app);
      } else {
        $logger->info('Unknown event type has come');
        continue;
      }

      if ($handler) {
        $handler->handle();
      }
    }

    writeResponse($app->response, 200, 'Ok');
    return;
  });
});
