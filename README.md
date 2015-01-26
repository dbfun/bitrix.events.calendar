# Описание

Календарь событий из разных инфоблоков для Битрикса

# Использование

На странице

```
  <?$APPLICATION->IncludeComponent(
    "mycomponents:events.calendar",
    "",
    Array(
      "IBLOCK_ID" => array(BFactory::_()->akcii, BFactory::_()->news, BFactory::_()->events),
      "SHOW_DOW" => "N",
      "URL_TEMPLATES_DETAIL" => "?year=#year#&month=#month#&day=#day#"
    ),
  false
  );?>

```

В Ajax:

```
ob_start();
$APPLICATION->IncludeComponent(
    "mycomponents:events.calendar",
    "",
    Array(
      "IBLOCK_ID" => explode(',', $_REQUEST['iblocks']),
      "SHOW_DOW" => $_REQUEST['showdow'],
      "URL_TEMPLATES_DETAIL" => $_REQUEST['urltemplate']
    ),
  false
  );
$pageContent = ob_get_contents();
ob_end_clean();

die(json_encode($pageContent));
```