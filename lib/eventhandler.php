<?php

namespace Angrycode\SMSCSender;

class EventHandler
{
  function eventHandler()
  {
    return [
      // Класс нашего СМС-сервиса
      new Smsc(),
    ];
  }
}