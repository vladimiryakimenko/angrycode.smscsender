<?php

namespace Angrycode\SMSCSender;

class EventHandler
{
  function eventHandler()
  {
    return [
      // ����� ������ ���-�������
      new Smsc(),
    ];
  }
}