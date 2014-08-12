<?php
/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 11/08/14
 */

namespace philwc;


class NMPMessage
{
    /**
     * The ID of the template
     *
     * @var int
     */
    private $_notificationId;

    /**
     * The encrypt value provided in the interface
     *
     * @var string
     */
    private $_encryptToken;

    /**
     * Unique random token to authenticate with Emailvision
     *
     * @var string
     */
    private $_randomToken;

    /**
     * The type of synchronization that should be carried out:
     * NOTHING: The MEMBER table will not be synchronized.
     * INSERT: The new profile will be inserted with all the associated DYN data in the MEMBER table.
     * UPDATE: The profile and all the associated DYN data will be updated in the MEMBER table.
     * INSERT_UPDATE: The system will try to update the profile in the MEMBER table. If the update fails, it tries to insert the profile with the associated DYN data into the MEMBER table.
     *
     * @var string
     */
    private $_sync_type = 'UPDATE';

    /**
     * The key you wish to update, normally its email
     *
     * @var string
     */
    private $_sync_key = 'email';

    /**
     * The time you wish to send the notification message. If you put a past time, it will be sent immediately.
     *
     * @var string
     */
    private $_email_time;

    /**
     * The email address to whom you wish to send the notification message
     *
     * @var string
     */
    private $_email_recipient;

    /**
     * Array containing key pair values for dynamic content
     *
     * @var array
     */
    private $_email_dynamic = array();

    /**
     * The HTML mail content
     *
     * @var string
     */
    private $_email_content_html;

    /**
     * The Text mail content
     *
     * @var string
     */
    private $_email_content_text;


    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->setEmailTime(date('c'));
    }

    /**
     * Reset the class to default
     */
    public function reset()
    {
        $this->_notificationId     = null;
        $this->_encryptToken       = null;
        $this->_randomToken        = null;
        $this->_sync_type          = 'UPDATE';
        $this->_sync_key           = 'email';
        $this->_email_time         = null;
        $this->_email_recipient    = null;
        $this->_email_dynamic      = array();
        $this->_email_content_html = null;
        $this->_email_content_text = null;

        $this->__construct();

        return $this;
    }

    /**
     * Set notificationId
     *
     * @param $notificationId
     *
     * @return $this
     */
    public function setNotificationId($notificationId)
    {
        $this->_notificationId = (int) $notificationId;

        return $this;
    }

    /**
     * Get notificationId
     *
     * @return int
     */
    public function getNotificationId()
    {
        return $this->_notificationId;
    }

    /**
     * Set encryptToken
     *
     * @param $encryptToken
     *
     * @return $this
     */
    public function setEncryptToken($encryptToken)
    {
        $this->_encryptToken = (string) $encryptToken;

        return $this;
    }

    /**
     * Get notificationId
     *
     * @return string
     */
    public function getEncryptToken()
    {
        return $this->_encryptToken;
    }

    /**
     * Set randomToken
     *
     * @param $randomToken
     *
     * @return $this
     */
    public function setRandomToken($randomToken)
    {
        $this->_randomToken = (string) $randomToken;

        return $this;
    }

    /**
     * Get randomToken
     *
     * @return string
     */
    public function getRandomToken()
    {
        return $this->_randomToken;
    }

    /**
     * Set sync_type
     *
     * @param $synctype
     *
     * @return $this
     */
    public function setSyncType($synctype)
    {
        $this->_sync_type = (string) $synctype;

        return $this;
    }

    /**
     * Get sync_type
     *
     * @return string
     */
    public function getSyncType()
    {
        return $this->_sync_type;
    }

    /**
     * Set sync_key
     *
     * @param $sync_key
     *
     * @return $this
     */
    public function setSyncKey($sync_key)
    {
        $this->_sync_key = (string) $sync_key;

        return $this;
    }

    /**
     * Get sync_key
     *
     * @return string
     */
    public function getSyncKey()
    {
        return $this->_sync_key;
    }

    /**
     * Set email_time: date('c')
     *
     * @param $email_time
     *
     * @return $this
     */
    public function setEmailTime($email_time)
    {
        $this->_email_time = (string) $email_time;

        return $this;
    }

    /**
     * Get $email_time
     *
     * @return date
     */
    public function getEmailTime()
    {
        return $this->_email_time;;
    }

    /**
     * Set email_recipient
     *
     * @param $email_recipient
     *
     * @return $this
     */
    public function setEmailRecipient($email_recipient)
    {
        $this->_email_recipient = (string) $email_recipient;

        return $this;
    }

    /**
     * Get email_recipient
     *
     * @return string
     */
    public function getEmailRecipient()
    {
        return $this->_email_recipient;
    }

    /**
     * Set dynamic email content
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addDynamicValue($key, $value)
    {
        $this->_email_dynamic[$key] = (string) $value;

        return $this;
    }

    /**
     * Remove dynamic email content
     *
     * @param string $key
     *
     * @return bool
     */
    public function removeDynamicValue($key = null)
    {
        if (array_key_exists($key, $this->_email_dynamic) && $key !== null) {
            unset($this->_email_dynamic[$key]);

            return true;
        }

        return false;
    }

    /**
     * Remove dynamic email content.
     *
     * @return array
     */
    public function returnDynamicValues()
    {
        return $this->_email_dynamic;
    }

    /**
     * Set HTML mail
     *
     * @param $content
     *
     * @return $this
     */
    public function setMailHtml($content)
    {
        $this->_email_content_html = (string) $content;

        return $this;
    }

    /**
     * Get HTML mail
     *
     * @return string
     */
    public function getMailHtml()
    {
        return $this->_email_content_html;
    }

    /**
     * Set Text mail
     *
     * @param $content
     *
     * @return $this
     */
    public function setMailText($content)
    {
        $this->_email_content_text = (string) $content;

        return $this;
    }

    /**
     * Get Text mail
     *
     * @return string
     */
    public function getMailText()
    {
        return $this->_email_content_text;
    }
}