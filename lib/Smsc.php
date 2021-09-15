<?php

namespace Angrycode\SMSCSender;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\MessageService\Sender\Base;
use Bitrix\MessageService\Sender\Result\SendMessage;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);



class Smsc extends Base
{
  private $login;

  private $password;

  private $provider;

  private $client;

  public function __construct() {
    $this->login = Option::get('angrycode.smscsender', "login", '');
    $this->password = Option::get('angrycode.smscsender', "password", '');
    $this->provider = Option::get('angrycode.smscsender', "provider", '');

    $this->client = new SmscApi($this->login, $this->password, $this->provider);
  }

  public function sendMessage(array $messageFields) {
    if (!$this->canUse()) {
      $result = new SendMessage();
      $result->addError(new Error($MESS['ANGRYCODE_SMSCSENDER_SENDMESSAGE']));
      return $result;
    }

    $parameters = [
      'phones' => $messageFields['MESSAGE_TO'],
      'mes' => $messageFields['MESSAGE_BODY'],
    ];

    if ($messageFields['MESSAGE_FROM']) {
      $parameters['sender'] = $messageFields['MESSAGE_FROM'];
    }

    $result = new SendMessage();
    $response = $this->client->send($parameters);

    if (!$response->isSuccess()) {
      $result->addErrors($response->getErrors());
      return $result;
    }

    return $result;
  }

  public function getShortName() {
    return 'smsc';
  }

  public function getId() {
    return 'smsc';
  }

  public function getName() {
    return 'SMSC Sender';
  }

  public function canUse() {
    return true;
  }

  public function getFromList() {
    $data = $this->client->getSenderList();
    if ($data->isSuccess()) {
      $response = $data->getData();
      if(!count($response)){
        $response = [array ( 'id' => 'SMSC', 'name' => 'SMSC' ) ];
      }
      return $response;
    }

    return [];
  }
}