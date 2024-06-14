<?php

class Aoe_BlackHoleSession_Model_Session extends Mage_Core_Model_Session
{

    protected $isBot = false;

    public function __construct(array $data)
    {
        parent::__construct($data);
        
        // Don't FATAL when config node does not exist
        if(!empty($_SERVER['HTTP_USER_AGENT'])) {
            if($config = Mage::getConfig()->getNode('global/aoeblackholesession')) {
              $botRegex = (string) $config->descend('bot_regex');
              if (preg_match($botRegex, (string) $_SERVER['HTTP_USER_AGENT'])) {
                  $this->isBot = true;
              }
            } else {
              Mage::log("Aoe_BlackHoleSession is not configured properly", Zend_Log::DEBUG, "aoeblackholesession.log", true);
            }
        }
    }

    public function getSessionSaveMethod()
    {
        if ($this->isBot) {
            return 'user';
        }

        return parent::getSessionSaveMethod();
    }

    public function getSessionSavePath()
    {
        if ($this->isBot) {
            $sessionHandler = Mage::getModel('aoeblackholesession/sessionHandler'); /* @var $sessionHanlder Aoe_BlackHoleSession_Model_SessionHandler */
            return array($sessionHandler, 'setHandler');
        }

        return parent::getSessionSavePath();
    }

}