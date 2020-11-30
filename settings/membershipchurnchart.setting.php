<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

use CRM_Membershipchurnchart_ExtensionUtil as E;

return [
  'membershipchurnchart_startyear' => [
    'name' => 'membershipchurnchart_startyear',
    'type' => 'String',
    'html_type' => 'text',
    'default' => '2010',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Start Year'),
    'description' => E::ts('Start year from which the membership churn chart data is to be prepared. (eg. 2010)'),
    'is_required' => TRUE,
    'html_attributes' => [
      'size' => 4,
    ],
    'settings_pages' => [
      'membershipchurnchart' => [
         'weight' => 1,
      ]
    ],
  ],
];
