<?php

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
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
      if (!($event instanceof MessageEvent)) {
        $logger->info('Non message event has come');
        continue;
      }

      if (!($event instanceof TextMessage)) {
        $logger->info('Non text message has come');
        continue;
      }

      $replyText = $event->getText();
      $logger->info('Reply text: ' . $replyText);
      $resp = $bot->replyText($event->getReplyToken(), $replyText);
      $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
    }

    writeResponse($app->response, 200, 'Ok');
    return;
  });
});
