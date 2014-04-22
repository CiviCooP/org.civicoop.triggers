<h3>Current Conditions</h3>
<table id="current-division-table" class="display">
    <thead>
        <tr>
            {foreach from=$conditionHeaders item=rowHeader}
                <th class="sorting-disabled">{$rowHeader}</th>
            {/foreach}
            <th class="sorting-disabled"></th>
        </tr>
    </thead>
    <tbody>
        {assign var="rowClass" value="odd-row"}
        {foreach from=$conditionRows item=row}
            <tr id="row1" class={$rowClass}>
                <td hidden="1">{$row.id}</td>
                <td>{$row.field_name}</td>
                <td>{$row.operation}</td>
                <td>{$row.value}</td>
                <td>{$row.aggregate_function}</td>
                <td>{$row.grouping_field}</td>
                <td>
                    <span>
                        <a class="action-item" title="Delete condition" href="{$row.delete}">Delete</a>
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
