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

return [
  [
    'name' => 'Membership Churn Chart',
    'entity' => 'Job',
    'params' =>
      [
        'version' => 3,
        'name' => 'Membership Churn Chart - Prepare Data',
        'description' => 'To prepare data for membership churn chart',
        'run_frequency' => 'Daily',
        'api_entity' => 'membershipchurnchart',
        'api_action' => 'preparechurntable',
      ],
  ],
];
