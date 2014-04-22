<h3>Current Rule Schedule Triggers</h3>
<table id="current-triggers-table" class="display">
    <thead>
        <tr>
            {foreach from=$triggerHeaders item=rowHeader}
                <th class="sorting-disabled">{$rowHeader}</th>
            {/foreach}
            <th class="sorting-disabled"></th>
        </tr>
    </thead>
    <tbody>
        {assign var="rowClass" value="odd-row"}
        {foreach from=$scheduledTriggerRows item=row}
            <tr id="row1" class={$rowClass}>
                <td hidden="1">{$row.id}</td>
                <td>{$row.logic_operator}</td>
                <td>{$row.trigger_label}</td>
                <td>{$row.trigger_entity}</td>
                <td>
                  {foreach from=$row.trigger_conditions item=triggerCondition}
                    {$triggerCondition}<br/>
                  {/foreach}
                </td>
                <td>
                    <span>
                        <a class="action-item" title="Delete Rule Schedule Trigger" href="{$row.delete}">Delete</a>
                    </span>
                </td>
            </tr>
            {if $rowClass eq "odd-row"}
                {assign var="rowClass" value="even-row"}
            {else}
                {assign var="rowClass" value="odd-row"}                        
            {/if}
        {/foreach}
    </tbody>
</table>    
