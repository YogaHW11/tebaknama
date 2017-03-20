<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\UnfollowEvent;

class UnfollowEventHandler implements EventHandler {
  private $bot;
  private $logger;
  private $followEvent;

  public function __construct(UnfollowEvent $unfollowEvent, Slim\Slim $app) {
    $this->bot = $app->bot;
    $this->logger = $app->logger;
    $this->unfollowEvent = $unfollowEvent;
  }

  public function handle() {
    Users::where('userId', '=', $this->unfollowEvent->getUserId())->delete();
  }
}
