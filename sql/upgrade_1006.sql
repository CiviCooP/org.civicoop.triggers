DROP TABLE `civicrm_trigger_action`;

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