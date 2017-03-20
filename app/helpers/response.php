<?php

function writeResponse($response, $status, $message) {
  $response->setStatus($status);
  $response->write($message);
}
