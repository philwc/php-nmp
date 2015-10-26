<?php
/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 11/08/14
 */

namespace philwc;


use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;

class NMPBatch
{
    // api url
    const API_URL = 'http://api.notificationmessaging.com/NMSXML';

    /**
     * Debug? True/false
     *
     * @var bool
     */
    private $_debug;

    /**
     * Messages array, for sending out batches of messages through 1 request
     *
     * @var array
     */
    private $_messages;


    /**
     * Add message to message array
     *
     * @param NMPMessage $message
     *
     * @return $this
     */
    public function addMessage(NMPMessage $message)
    {
        $this->_messages[] = $message;

        return $this;
    }

    /**
     * Set debug
     *
     * @param $debug
     *
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->_debug = (bool) $debug;

        return $this;
    }

    /**
     * Get debug
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->_debug;
    }


    /**
     * @return \DOMDocument
     */
    private function getMessages(\DOMDocument $domTree)
    {

        $requestMain = $domTree->createElement('sendrequest');
        $dynMain     = $domTree->createElement('dyn');
        $entryMain   = $domTree->createElement('entry');

        $ret = [];

        /** @var \philwc\NMPMessage $val */
        foreach ($this->_messages as $val) {
            $request = clone $requestMain;
            $dyn     = clone $dynMain;

            $request->appendChild($dyn);

            $kpv = $val->returnDynamicValues();
            if (count($kpv) > 0) {
                foreach ($kpv as $k => $v) {
                    $e     = clone $entryMain;
                    $key   = $domTree->createElement('key', $k);
                    $value = $domTree->createElement('value', $v);

                    $e->appendChild($key)
                      ->appendChild($value);

                    $dyn->appendChild($e);
                }
            }

            $content = $domTree->createElement('content');
            $entry1  = clone $entryMain;
            $key1    = $domTree->createElement('key', 1);
            $value1  = $domTree->createElement('value', $val->getMailHtml());
            $entry1->appendChild($key1)
                   ->appendChild($value1);

            $entry2 = clone $entryMain;
            $key2   = $domTree->createElement('key', 2);
            $value2 = $domTree->createElement('value', $val->getMailText());
            $entry2->appendChild($key2)
                   ->appendChild($value2);

            $content->appendChild($entry1)
                    ->appendChild($entry2);

            $notificationId = $domTree->createElement('notificationId', $val->getNotificationId());
            $content->appendChild($notificationId);

            $email = $domTree->createElement('email', $val->getEmailRecipient());
            $content->appendChild($email);

            $encrypt = $domTree->createElement('encrypt', $val->getEncryptToken());
            $content->appendChild($encrypt);

            $random = $domTree->createElement('random', $val->getRandomToken());
            $content->appendChild($random);

            $senddate = $domTree->createElement('senddate', $val->getEmailTime());
            $content->appendChild($senddate);

            $synchrotype = $domTree->createElement('synchrotype', $val->getSyncType());
            $content->appendChild($synchrotype);

            $uidkey = $domTree->createElement('uidkey', $val->getSyncKey());
            $content->appendChild($uidkey);

            $request->appendChild($content);

            $ret[] = $request;
        }

        return $ret;
    }


    /**
     * Send to Emailvision API
     *
     * @return bool|array
     */
    public function send()
    {
        $domTree = new \DOMDocument('1.0', 'UTF-8');
        $root    = $domTree->createElement('MultiSendRequest');

        /** @var \DOMElement $message */
        foreach ($this->getMessages($domTree) as $message) {

            //var_dump($message);
            //$domTree->importNode($message, true);

            $root->appendChild($message);
        }

        $root = $domTree->appendChild($root);

        // build final xml
        $xml = $domTree->saveXML($root);
        var_dump($xml);
        die();

        $client = new Client();
        $stream = \GuzzleHttp\Psr7\stream_for($xml);

        $request = $client->request('POST', self::API_URL,
            ['headers' => ['Content-Type' => 'text/xml'], 'body' => $stream]);

        $response = $client->send($request);

        try {
            $xmlResponse = $response->xml();
            $success     = true;
        } catch (\Exception $e) {
            $success     = false;
            $xmlResponse = $response->getBody();
        }

        // if debug mode is true, send input + output, else return booleans
        if ($this->getDebug()) {
            return array('output' => $xmlResponse, 'input' => $this->getMessages());
        }
        else {
            return $success;
        }
    }
}