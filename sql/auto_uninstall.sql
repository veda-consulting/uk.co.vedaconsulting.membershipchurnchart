-- Delete churn data tables
DROP TABLE IF EXISTS `membership_churn_table`;
DROP TABLE IF EXISTS `membership_churn_monthly_table`;

-- Delete scheduled job
DELETE FROM civicrm_job WHERE api_entity = 'membershipchurnchart' AND api_action = 'preparechurntable';
