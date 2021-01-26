-- Create tables to save chart data
CREATE TABLE IF NOT EXISTS `civicrm_membership_churn_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Id',
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `membership_id` int(11) DEFAULT NULL,
  `membership_type_id` int(11) DEFAULT NULL,
  `current` int(11) DEFAULT NULL,
  `joined` int(11) DEFAULT NULL,
  `resigned` int(11) DEFAULT NULL,
  `rejoined` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `civicrm_membership_churn_monthly_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Id',
  `month_year` VARCHAR(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `membership_type_id` int(11) DEFAULT NULL,
  `current` int(11) DEFAULT NULL,
  `joined` int(11) DEFAULT NULL,
  `resigned` int(11) DEFAULT NULL,
  `rejoined` int(11) DEFAULT NULL,
  `brought_forward` int(11) DEFAULT NULL,
  `churn` double(10, 2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
