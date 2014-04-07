<div class="crm-content-block crm-block">
    <div id="help">
        The existing Triggers are listed below. You can view, add, edit or delete them from this screen. 
    </div>
    <div class="action-link">
        <a class="button new-option" href="{$addUrl}">
            <span><div class="icon add-icon"></div>Add Trigger</span>
        </a>
    </div>
    <div id="trigger-wrapper" class="dataTables_wrapper">
        <table id="trigger-table" class="display">
            <thead>
                <tr>
                    {foreach from=$rowHeaders item=rowHeader}
                        <th class="sorting-disabled">{$rowHeader}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {assign var="rowClass" value="odd-row"}
                {assign var="rowId" value=1}
                {foreach from=$rows item=row}
                    <tr id="row{$row.id}" class={$rowClass}>
                        {foreach from=$row.elements item=rowElement}
                            <td hidden="{$rowElement.hidden}">{$rowElement}</td>
                        {/foreach}
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
            <span><div class="icon add-icon"></div>Add Trigger</span>
        </a>
    </div>
</div>
