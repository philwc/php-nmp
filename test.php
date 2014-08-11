<?php

$users = array(
    array('username' => 'Phil Wright-Christie', 'email' => 'philwc@gmail.com'),
);

require 'vendor/autoload.php';

$batch = new \philwc\NMPBatch();

$id       = '';
$security = '';

foreach ($users as $k => $v) {
    $email = new \philwc\NMPMessage();
    $email->setEncryptToken($security);
    $email->setRandomToken($id);
    $email->setEmailRecipient($v['email']);
    $email->addDynamicValue('subject', 'Test subject for ' . $v['username']);
    $email->addDynamicValue('comment', 'Hello World');
    $batch->addMessage($email);
}

/* SEND BATCH */
$batch->setDebug(true);
$batch = $batch->send();

echo '<pre>';
var_dump($batch);
echo '</pre>';
