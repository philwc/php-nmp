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
     */
    public function addMessage(NMPMessage $message)
    {
        $this->_messages[] = $message;
    }

    /**
     * Set debug
     *
     * @param int $debug
     */
    public function setDebug($debug)
    {
        $this->_debug = (bool) $debug;
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
     * Returns all messages
     *
     * @return array
     */
    private function getMessages()
    {
        $output = '';
        foreach ($this->_messages as $val) {
            $output .= '<sendrequest>
				<dyn>';

            $kpv = $val->returnDynamicValues();
            if (count($kpv) > 0) {
                foreach ($kpv as $k => $v) {
                    $output .= '
                    <entry>
                        <key>' . $k . '</key>
						<value>' . $v . '</value>
					</entry>';
                }
            }

            $output .= '
				</dyn>
				<content>
					<entry>
						<key>1</key>
						<value>
							<![CDATA[' . $val->getMailHtml() . ']]>
						</value>
					</entry>
					<entry>
						<key>2</key>
						<value>
							<![CDATA[' . $val->getMailText() . ']]>
						</value>
					</entry>
				</content>

				<notificationId>' . $val->getNotificationId() . '</notificationId>
				<email>' . $val->getEmailRecipient() . '</email>
				<encrypt>' . $val->getEncryptToken() . '</encrypt>
				<random>' . $val->getRandomToken() . '</random>
				<senddate>' . $val->getEmailTime() . '</senddate>
				<synchrotype>' . $val->getSyncType() . '</synchrotype>
				<uidkey>' . $val->getSyncKey() . '</uidkey>
			</sendrequest>';
        }

        return $output;
    }


    /**
     * Send to Emailvision API
     *
     * @return bool|array
     */
    public function send()
    {
        // build final xml
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
		<MultiSendRequest>
		' . $this->getMessages() . '
		</MultiSendRequest>';

        $client  = new Client();
        $request = $client->createRequest('POST', self::API_URL);
        $request->addHeader('Content-Type', 'text/xml');
        $request->setBody(Stream::factory($xml));
        $response = $client->send($request);

        try {
            $xmlResponse = $response->xml();
        } catch (\Exception $e) {
            $xmlResponse = '';
        }

        // if debug mode is true, send input + output, else return booleans
        if ($this->getDebug()) {
            return array('output' => $xmlResponse, 'input' => $this->getMessages());
        } else {
            return $xmlResponse != '';
        }
    }
}