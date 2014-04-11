<div class="crm-content-block crm-block">
    <div id="help">
        The existing Scheduled Trigger/Action Rules are listed below. You can view, add, edit, dis/enable or delete them from this screen. 
    </div>
    <div class="action-link">
        <a class="button new-option" href="{$addUrl}">
            <span><div class="icon add-icon"></div>New Schedule Rule</span>
        </a>
    </div>
    <div id="action-rule-wrapper" class="dataTables_wrapper">
        <table id="action-rule-table" class="display">
            <thead>
                <tr>
                    {foreach from=$rowHeaders item=rowHeader}
                        <th class="sorting-disabled">{$rowHeader}</th>
                    {/foreach}
                    <th class="sorting-disabled"></th>
                </tr>
            </thead>
            <tbody>
                {assign var="rowClass" value="odd-row"}
                {assign var="rowId" value=1}
                {foreach from=$rows item=row}
                    <tr id="row{$row.id}" class={$rowClass}>
                        <td hidden="1">{$row.id}</td>
                        <td>{$row.label}</td>
                        <td>{$row.rule_action}</td>
                        <td>{$row.schedule}</td>
                        <td>{$row.start_date|crmDate}</td>
                        <td>{$row.end_date|crmDate}</td>
                        <td>{$row.last_run}</td>
                        <td>{$row.next_run}</td>
                        <td>{$row.is_active}</td>
                        <td>
                            <span>
                                {foreach from=$row.actions item=actionLink}
                                    {$actionLink}
                                {/foreach}
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
    </div>
    <div class="action-link">
        <a class="button new-option" href="{$addUrl}">
            <span><div class="icon add-icon"></div>New Schedule Rule</span>
        </a>
    </div>
</div>
