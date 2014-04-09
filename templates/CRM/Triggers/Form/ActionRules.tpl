{* Donor Journeys Triggers and Actions - funded by MAF Norge *}
{* Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>         *}
{* 9 April 2014                                              *}

{* HEADER *}
<div class="crm-block crm-form-block">
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
    <table class="form-layout-compressed">
        <tbody>
            {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

            {foreach from=$elementNames item=elementName}
              <div class="crm-section">
                  <div class="label">{$form.$elementName.label}</div>
                  {if $action eq 4}
                      <div class="content" readonly="readonly">{$form.$elementName.html}</div>
                  {else}
                      <div class="content">{$form.$elementName.html}</div>
                  {/if}
                  <div class="clear"></div>
              </div>
            {/foreach}
        </tbody>
    </table>

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div
