<h3>New Condition</h3>
<div id="new-condition-wrapper" class="dataTables_wrapper">
    <table id="new-condition-table" class="display">
        <tbody>
            <tr id="head-new-row" class="odd-row">
                {foreach from=$conditionHeaders item=rowHeader}
                    <th class="sorting-disabled">{$rowHeader}</th>
                {/foreach}
                <th class="sorting-disabled"></th>
            </tr>
            <tr id="new-row" class="even-row">
                <td>{$form.field_name.html}</td>
                <td>{$form.operation.html}</td>
                <td>{$form.value.html}</td>
                <td>{$form.aggregate_function.html}</td>
                <td>{$form.grouping_field.html}</td>
                <td>
                    <span class="crm-button">
                        <input id="save-condition" class="validate form-submit default" type="submit" value="Add Condition" name="_qf_TriggerRules_next" accesskey="S">
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
