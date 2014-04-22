{* Donor Journeys Triggers and Actions - funded by MAF Norge *}
{* Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>         *}
{* 7 April 2014                                              *}

{* HEADER *}
<div class="crm-block crm-form-block">
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
    <table class="form-layout-compressed">
        <tbody>
            {foreach from=$elementNames item=elementName}
              <div class="crm-section">
                  <div class="label">{$form.$elementName.label}</div>
                  <div class="content">{$form.$elementName.html}</div>
                  <div class="clear"></div>
              </div>
            {/foreach}
        </tbody>
    </table>
    {* allow add condition rules if action is edit *}
    {if $action eq 2}
        {include file="CRM/Triggers/Form/NewCondition.tpl"}
    {/if}
    {* include condition details if they are there *}
    {if isset($conditionRows)}
        {include file="CRM/Triggers/Page/CurrentConditions.tpl"}
    {/if}

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div>
