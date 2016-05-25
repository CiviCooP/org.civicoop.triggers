# ATTENTION!!! THIS EXTENSION HAS BEEN REPLACED BY CIVIRULES AND IS NO LONGER SUPPORTED!
but feel free to (ab)use :-)
CiviRules can be found here: https://civicrm.org/extensions/civirules

# CiviCRM Triggers and Actions

## Explanation

This example will show what the trigger/action parameters are.

We want to achieve the following:

- Every contact which first name is equal to John we want them to be added to the group 'Johns'. 
- And when the contact has contributed something 7 days ago
- This group has id 6.

###Trigger Rules

The trigger rules and the condition determine which entities to match for triggering

<table>
<thead><tr><th>ID</th><th>Label</th><th>Name</th><th>Entity</th></tr></thead>
<tbody>
<tr>
    <td>20</td><td>When firstname = John</td><td>firstname_john</td><td>Contact</td></tr>
    <td>21</td><td>When contribution completed and 7 days ago</td><td>contribution_completed</td><td>Contribution</td></tr>
</tbody>
</table>

###Trigger conditions

<table>
<thead><tr><th>id</th><th>Trigger rule ID</th><th>Field</th><th>Value</th><th>Special processing</th><th>Operator</th><th>Aggregate function</th><th>Grouping field</th></tr></thead>
<tbody>
    <tr><td>30</td><td>20</td><td>first_name</td><td>John</td><td>false</td><td>=</td><td> </td><td> </td></tr>
    <tr><td>31</td><td>21</td><td>status_id</td><td>1</td><td>false</td><td>=</td><td> </td><td> </td></tr>
    <tr><td>32</td><td>21</td><td>receive_date</td><td>DATE_SUB(CUR_DATE() INTERVAL 7 DAYS)</td><td>true</td><td>=</td><td> </td><td> </td></tr>
</tbody>
</table>

###Action rules

The action is the action which is executed on a found entity. The action consist of calling the civicrm api.

<table>
<thead><tr><th>id</th><th>Label</th><th>Name</th><th>API Entity</th><th>API Action</th><th>API Parameters</th></tr></thead>
<tbody>
    <tr><td>40</td><td>Add contact to group John</td><td>add_to_group_john</td><td>GroupContact</td><td>Create</td><td>group_id=6&amp;contact_id={contact.id}</td></tr>
</tbody>
</table>

The API parameters can contain *tokens* which consist of curly brackets around them and the entity name with a dot for the field of the entity. e.g. {contribution.total_amount}

The entities refer back to the entities used in the trigger part. If you have moltiple entities e.g. multiple groups you can use group one as {group1.id} and for the second group {group2.id}

###Rule Schedule

A rule schedule determines when a rule is scheduled for checking and executing the action

<table>
<thead><tr><th>ID</th><th>Label</th><th>Name</th><th>Action rule ID</th><th>Schedule</th><th>is active</th><th>Start date</th><th>End date</th></tr></thead>
<tbody>
    <tr><td>50</td><td>John to Johns (every morning at 7)</td><td>john_to_johns</td><td>40</td><td>Tomorrow +7 hours</td><td>1</td><td> </td><td> </td></tr> 
</tbody>
</table>

The schedule parameter is a php relative date format see [http://www.php.net/manual/en/datetime.formats.relative.php](http://www.php.net/manual/en/datetime.formats.relative.php) for this fornat specification.

###Rule Schedule Trigger

The rule schedule trigger contains the linked triggers to a rule schedule.
<table>
<thead><tr><th>ID</th><th>Rule Schedule ID</th><th>Trigger rule ID</th><th>Logic operator</th></tr></thead>
<tbody><tr><td>100</td><td>50</td><td>20</td><td> </td><tr>
<tr><td>100</td><td>50</td><td>21</td><td>AND</td><tr>
</tbody>
</table>

## Hooks

This extension implements several hooks. For a complete description of those hooks see [docs/hooks.md](docs/hooks.md)

## Data structure

See [docs/dataStructure.md](docs/dataStructure.md) for a description of the data structure.
