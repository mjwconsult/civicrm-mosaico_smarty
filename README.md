# mosaico-smarty

Enables the Smarty templating engine in Mosaico and allows you to use smarty tokens/logic for mosaico mailings.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.24+
* CiviCRM Mosaico 2.4

## Installation

See: https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension

## Usage

Install and enable.

## Known Issues

This extension is not yet complete!

## TODO

CiviCRM Core requires the following patches:

1: In Civi/Token/TokenCompatSubscriber::onRender(), change:

```php
if ($useSmarty) {
$smarty = \CRM_Core_Smarty::singleton();
$e->string = $smarty->fetch("string:" . $e->string);
}
```
To:

```php
if ($useSmarty) {
$smarty = \CRM_Core_Smarty::singleton();
$smarty->assign_by_ref('contact', $e->context['contact']);
$e->string = $smarty->fetch("string:" . $e->string);
}
```

2: In CRM_Utils_Token::getTokens() we want to add a new regular expression to
pull out smarty tokens. The full function becomes:

```php
public static function getTokens($string) {
  $matches = [];
  $tokens = [];
  preg_match_all('/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
    $string,
    $matches,PREG_PATTERN_ORDER
  );
  if ($matches[1]) {
    foreach ($matches[1] as $token) {
      list($type, $name) = preg_split('/\./', $token, 2);
      if ($name && $type) {
        if (!isset($tokens[$type])) {
          $tokens[$type] = [];
        }
        $tokens[$type][] = $name;
      }
    }
  }
  $matches = [];
  preg_match_all('/(?<!\{|\\\\)\{[^}]*\$(\w+\.\w+)[^}]+\}(?!\})/',
    $string,
    $matches,
    PREG_PATTERN_ORDER
  );
  if ($matches[1]) {
    foreach ($matches[1] as $token) {
      list($type, $name) = preg_split('/\./', $token, 2);
      if ($name && $type) {
        if (!isset($tokens[$type])) {
          $tokens[$type] = array();
        }
        $tokens[$type][] = $name;
      }
    }
  }
  return $tokens;
}
```
