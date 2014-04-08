CREATE TABLE IF NOT EXISTS `civicrm_processed_trigger` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_action_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity` varchar(255) NOT NULL,
  `date_processed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;