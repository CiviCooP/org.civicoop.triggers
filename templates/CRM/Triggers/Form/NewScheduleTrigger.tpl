<h3>New Rule Schedule Trigger</h3>
<div id="new-trigger-wrapper" class="dataTables_wrapper">
    <table id="new-trigger-table" class="display">
        <tbody>
            <tr id="head-new-row" class="odd-row">
                {foreach from=$triggerHeaders item=rowHeader}
                    <th class="sorting-disabled">{$rowHeader}</th>
                {/foreach}
                <th class="sorting-disabled"></th>
            </tr>
            <tr id="new-row" class="even-row">
              {if !empty($triggerRows)}
                <td>{$form.logic_operator}</td>
              {/if}
                <td>{$form.rule_schedule_trigger}</td>
                <td>
                    <span class="crm-button">
                        <input id="save-condition" class="validate form-submit default" type="submit" value="Schedule Trigger" name="_qf_RuleSchedule_next" accesskey="S">
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
