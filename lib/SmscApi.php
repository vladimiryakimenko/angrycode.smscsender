<?php
namespace Angrycode\SMSCSender;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use \Bitrix\Main\Data\Cache;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
class SmscApi
{
  private $login;

  private $password;

  private $provider;

  public function __construct($login, $password, $provider)
  {
    $this->login = $login;
    $this->password = $password;
    $this->provider = $provider;
  }

  public function getSenderList()
  {
    $result = new Result();
    $cache_key = 'SMSC_GET_SENDER_LIST';
    $cache = Cache::createInstance(); // получаем экземпл€р класса
    $response = [];
    if ($cache->initCache(3360, $cache_key)) { // провер€ем кеш и задаЄм настройки
      $result = $cache->getVars(); // достаем переменные из кеша
    }
    elseif ($cache->startDataCache()) {

      $senders = [];
      $response = $this->query('senders', ['get' => 1]);
      if (!$response->isSuccess()) {
        $result->addErrors($response->getErrors());

        return $result;
      }

      $data = $response->getData();
      foreach ($data as $sender) {
        $senders[] = [
          'id' => $sender['sender'],
          'name' => $sender['sender']
        ];
      }

      $result->setData($senders);
      $cache->endDataCache($result); // записываем в кеш
    }

    return $result;
  }

  private function query($method, $parameters = [], $httpMethod = HttpClient::HTTP_POST)
  {
    $result = new Result();

    $parameters['login'] = $this->login;
    $parameters['psw'] = $this->password;
    $parameters['fmt'] = 3;

    $http = new HttpClient();
    if ($httpMethod === HttpClient::HTTP_GET) {
      $http->query($httpMethod, 'https://'.$this->provider.'/sys/'.$method.'.php?', http_build_query($parameters));
    } else {
      $http->query($httpMethod, 'https://'.$this->provider.'/sys/'.$method.'.php', $parameters);
    }

    try {
      $data = Json::decode($http->getResult());
      if (isset($data['error'])) {
        $result->addError(new Error($data['error']));

        return $result;
      }

      $result->setData($data);
    } catch (ArgumentException $e) {
    }

    return $result;
  }

  public function send($parameters)
  {
    $result = new Result();

    $response = $this->query('send', $parameters);

    if (!$response->isSuccess()) {
      $result->addErrors($response->getErrors());
    }

    return $result;
  }

  public function getBalance()
  {
    $result = new Result();

    $response = $this->query('balance');
    if (!$response->isSuccess()) {
      $result->addErrors($response->getErrors());
    }

    $result->setData($response->getData());

    return $result;
  }
}