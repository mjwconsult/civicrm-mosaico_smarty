# Moved to https://lab.civicrm.org/extensions/mosaico_smarty

# mosaico-smarty

Enables the Smarty templating engine in Mosaico and allows you to use smarty tokens/logic for mosaico mailings as well as
the standard CiviCRM tokens.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.28+
* CiviCRM Mosaico 2.5+
* FlexMailer must be enabled (required for Mosaico). To work for "traditional/legacy" mailings you must set FlexMailer `Traditional Mailing Handler` to "Flexmailer pipeline".

## Installation

See: https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension

## Usage

Install and enable.

#### Example of CiviCRM token
Add the following to a mosaico text block:

```Token: {contact.display_name}```

#### Example of Smarty code
Add the following to a mosaico text block:

```Smarty:  {if $contact}Welcome {$contact.display_name}{/if}```

## Support and Maintenance
This extension is supported and maintained with the help and support of the CiviCRM community by:

[![MJW Consulting](docs/images/mjwconsulting.jpg)](https://www.mjwconsult.co.uk)

We offer paid [support and development](https://mjw.pt/support) as well as a [troubleshooting/investigation service](https://mjw.pt/investigation).

