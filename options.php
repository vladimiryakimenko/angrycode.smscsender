<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'angrycode.smscsender');

if (!$USER->isAdmin()) {
  $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot() . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
    "DIV" => "edit1",
    "TAB" => Loc::getMessage("MAIN_TAB_SET"),
    "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
  ),
));

if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
  if (!empty($restore)) {
    Option::delete(ADMIN_MODULE_NAME);
    CAdminMessage::showMessage(array(
      "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
      "TYPE" => "OK",
    ));
  } elseif ($request->getPost('login') && $request->getPost('password')) {

    Option::set(
      ADMIN_MODULE_NAME,
      "provider",
      $request->getPost('provider')
    );


    Option::set(
      ADMIN_MODULE_NAME,
      "login",
      $request->getPost('login')
    );

    Option::set(
      ADMIN_MODULE_NAME,
      "password",
      $request->getPost('password')
    );

    CAdminMessage::showMessage(array(
      "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_SAVED"),
      "TYPE" => "OK",
    ));
  } else {
    CAdminMessage::showMessage(Loc::getMessage("REFERENCES_INVALID_VALUE"));
  }
}

$tabControl->begin();
?>

<form method="post"
      action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>">
  <?php
  echo bitrix_sessid_post();
  $tabControl->beginNextTab();
  $providerList = array(
    'smsc.ru' => 'SMSC.RU',
    'smsc.kz' => 'SMSC.KZ',
  );
  $selectProvider = Option::get(ADMIN_MODULE_NAME, "provider", '');
  ?>

    <tr>
        <td width="40%">
            <label for="provider"><?= Loc::getMessage("REFERENCES_PROVIDER") ?>:</label>
        <td width="60%">
            <select name="provider">
                <option value="">----</option>
              <? foreach ($providerList as $value => $name): ?>
                  <option value="<?= $value ?>" <? echo $selectProvider == $value ? 'selected' : '' ?> ><?= $name ?></option>
              <? endforeach; ?>
            </select>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="login"><?= Loc::getMessage("REFERENCES_LOGIN") ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="login"
                   value="<?= HtmlFilter::encode(Option::get(ADMIN_MODULE_NAME, "login", '')); ?>"
            />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label for="password"><?= Loc::getMessage("REFERENCES_PASSWORD") ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="password"
                   value="<?= HtmlFilter::encode(Option::get(ADMIN_MODULE_NAME, "password", '')); ?>"
            />
        </td>
    </tr>

  <?php
  $tabControl->buttons();
  ?>
    <input type="submit"
           name="save"
           value="<?= Loc::getMessage("MAIN_SAVE") ?>"
           title="<?= Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
    />
    <input type="submit"
           name="restore"
           title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?= Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
  <?php
  $tabControl->end();
  ?>
</form>
