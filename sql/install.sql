-- --------------------------------------------------------

--
-- Table structure for table `civicrm_rule_action`
--

CREATE TABLE IF NOT EXISTS `civicrm_action_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_trigger_rule`
--

CREATE TABLE IF NOT EXISTS `civicrm_trigger_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `operation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_trigger_action`
--

CREATE TABLE IF NOT EXISTS `civicrm_trigger_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_rule_id` int(11) NOT NULL,
  `action_rule_id` int(11) NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `last_run` datetime DEFAULT NULL,
  `next_run` datetime DEFAULT NULL,
  `active` int(1) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `civicrm_trigger_rule_condition`
--

CREATE TABLE IF NOT EXISTS `civicrm_trigger_rule_condition` (
  `Iid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_rule_id` int(11) unsigned NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `old_value` text,
  `old_op` varchar(255) DEFAULT NULL,
  `new_value` text NOT NULL,
  `new_op` int(255) NOT NULL,
  `aggregate_function` varchar(255) DEFAULT NULL,
  `grouping_field` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Iid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
