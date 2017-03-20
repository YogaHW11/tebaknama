<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class FollowEventHandler implements EventHandler {
  private $bot;
  private $logger;
  private $followEvent;

  public function __construct(FollowEvent $followEvent, Slim\Slim $app) {
    $this->bot = $app->bot;
    $this->logger = $app->logger;
    $this->followEvent = $followEvent;
  }

  public function handle() {
    Users::insert(array('userId' => $this->followEvent->getUserId()));
    $this->bot->replyText($this->followEvent->getReplyToken(), 'Halo, selamat datang di permainan TebakNama.'.chr(10).'Permainan ini akan menguji wawasanmun tentang pengetahuan umum.');
    $this->bot->pushMessage($this->followEvent->getUserId(), new TextMessageBuilder(
      'Cara bermainnya sangat mudah. Kamu cukup menebak nama sesuatu berdasarkan petunjuk yang diberikan. Untuk memudahkan kamu dalam menjawab setiap soal, saya akan membocorkan jumlah karakter dari jawaban yang dimaksud.',
      'Untuk menebak setiap soal, kamu cukup menebak dan mengetikkan satu (1) karakter dari jawaban yang kamu tebak. Apabila kamu mengetikkan lebih dari satu karakter, hanya karakter awal saja yang akan saya terima.',
      'Kamu memiliki tiga (3) kesempatan dalam menebak setiap soal. Dan apabila kamu memiliki soal yang salah paling banyak tiga (3), permainan akan berakhir.'
    ));
    $buttonTemplateBuilder = new ButtonTemplateBuilder(
      null,
      'Selamat bermain!',
      null,
      [
        new MessageTemplateActionBuilder('Mulai', 'Mulai')
      ]
    );
    $templateMessage = new TemplateMessageBuilder('Mulai', $buttonTemplateBuilder);
    $this->bot->pushMessage($this->followEvent->getUserId(), $templateMessage);
    
  }
}
