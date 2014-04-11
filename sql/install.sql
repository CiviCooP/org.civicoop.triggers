-- --------------------------------------------------------

--
-- Table structure for table `civicrm_rule_action`
--

CREATE TABLE IF NOT EXISTS `civicrm_action_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `params` text,
  `process_contacts` TINYINT UNSIGNED NOT NULL DEFAULT  '1'
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_trigger_rule`
--

CREATE TABLE IF NOT EXISTS `civicrm_trigger_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `operation` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_rule_schedule`
--

CREATE TABLE IF NOT EXISTS `civicrm_rule_schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `action_rule_id` int(11) NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `last_run` datetime DEFAULT NULL,
  `next_run` datetime DEFAULT NULL,
  `is_active` int(1) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `civicrm_rule_schedule_trigger` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_schedule_id` int(10) unsigned NOT NULL,
  `trigger_rule_id` int(10) unsigned NOT NULL,
  `logic_operator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_trigger_rule_condition`
--

CREATE TABLE IF NOT EXISTS `civicrm_trigger_rule_condition` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_rule_id` int(11) unsigned NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `special_processing` TINYINT(255) NOT NULL DEFAULT  '0',
  `operation` varchar(255) NOT NULL,
  `aggregate_function` varchar(255) DEFAULT NULL,
  `grouping_field` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `civicrm_processed_trigger` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_schedule_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `date_processed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `trigger_action` (`trigger_action_id`,`entity`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;
