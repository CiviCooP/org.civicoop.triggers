--
-- Database: `trigger_civicrm`
--

--
-- Dumping data for table `civicrm_action_rule`
--

INSERT INTO `civicrm_action_rule` (`id`, `label`, `name`, `entity`, `action`, `params`) VALUES
(1, 'Actie 1', 'actie1', 'GroupContact', 'create', 'group_id=6&contact_id={contact.id}');

--
-- Dumping data for table `civicrm_trigger_action`
--

INSERT INTO `civicrm_trigger_action` (`id`, `trigger_rule_id`, `action_rule_id`, `schedule`, `last_run`, `next_run`, `is_active`, `start_date`, `end_date`) VALUES
(1, 1, 1, '', NULL, '2013-03-01 00:00:00', 1, NULL, NULL);

--
-- Dumping data for table `civicrm_trigger_rule`
--

INSERT INTO `civicrm_trigger_rule` (`id`, `label`, `entity`, `operation`) VALUES
(1, 'All contacten', 'Contact', 'op');

--
-- Dumping data for table `civicrm_trigger_rule_condition`
--

INSERT INTO `civicrm_trigger_rule_condition` (`Iid`, `trigger_rule_id`, `field_name`, `value`, `operation`, `aggregate_function`, `grouping_field`) VALUES
(1, 1, 'id', '10', '>=', NULL, NULL);
