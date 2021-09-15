<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Angrycode\SMSCSender\ExampleTable;

Loc::loadMessages(__FILE__);

class angrycode_smscsender extends CModule
{
  public function __construct()
  {
    $arModuleVersion = array();

    include __DIR__ . '/version.php';

    if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
      $this->MODULE_VERSION = $arModuleVersion['VERSION'];
      $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    $this->MODULE_ID = 'angrycode.smscsender';
    $this->MODULE_NAME = Loc::getMessage('ANGRYCODE_SMSCSENDER_MODULE_NAME');
    $this->MODULE_DESCRIPTION = Loc::getMessage('ANGRYCODE_SMSCSENDER_MODULE_DESCRIPTION');
    $this->MODULE_GROUP_RIGHTS = 'N';
    $this->PARTNER_NAME = Loc::getMessage('ANGRYCODE_SMSCSENDER_MODULE_PARTNER_NAME');
    $this->PARTNER_URI = 'https://angrycode.kz';
  }


  function InstallEvents()
  {
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->registerEventHandlerCompatible(
      'messageservice',
      'onGetSmsSenders',
      $this->MODULE_ID,
      '\Angrycode\SMSCSender\EventHandler',
      'eventHandler'
    );

    return true;
  }

  function UnInstallEvents()
  {
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->unRegisterEventHandler(
      'messageservice',
      'onGetSmsSenders',
      $this->MODULE_ID,
      '\Angrycode\SMSCSender\EventHandler',
      'eventHandler'
    );

    return true;
  }

  public function doInstall()
  {
    ModuleManager::registerModule($this->MODULE_ID);
    $this->installDB();
    $this->InstallEvents();
  }

  public function doUninstall()
  {
    $this->UnInstallEvents();
    $this->uninstallDB();
    ModuleManager::unRegisterModule($this->MODULE_ID);
  }

  public function installDB()
  {

  }

  public function uninstallDB()
  {

  }
}


