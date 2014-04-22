{* Donor Journeys Triggers and Actions - funded by MAF Norge *}
{* Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>         *}
{* 11 April 2014                                              *}

{* HEADER *}
<div class="crm-block crm-form-block">
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
    <table class="form-layout-compressed">
        <tbody>
          {* label *}
          <div class="crm-section">
            <div class="label">{$form.label.label}</div>
            {if $action eq 4}
              <div class="content">{$form.label.value}</td>
            {else}
              <div class="content">{$form.label.html}</div>
            {/if}
            <div class="clear"></div>
          </div>
          {* action rule *}
          <div class="crm-section">
            <div class="label">{$form.action_rule.label}</div>
            {if $action eq 4}
              <div class="content">{$form.action_rule.value}</td>
            {else}
              <div class="content">{$form.action_rule.html}</div>
            {/if}
            <div class="clear"></div>
          </div>
          {* schedule *}
          <div class="crm-section">
            <div class="label">{$form.schedule.label}</div>
            {if $action eq 4}
              <div class="content">{$form.schedule.value}</td>
            {else}
              <div class="content">{$form.schedule.html}</div>
            {/if}
            <div class="clear"></div>
          </div>
          {* start date *}
          <div class="crm-section">
            <div class="label">{$form.start_date.label}</div>
            {if $action eq 4}
              <div class="content">{$form.start_date.value|crmDate}</div>
            {else}
              <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=start_date}</div>
            {/if}
          </div>
          {* end date *}
          <div class="crm-section">
            <div class="label">{$form.end_date.label}</div>
            {if $action eq 4}
              <div class="content">{$form.end_date.value|crmDate}</div>
            {else}
              <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=end_date}</div>
            {/if}
          </div>
          {if $action neq 1}
            {* last run *}
            <div class="crm-section">
              <div class="label">{$form.last_run.label}</div>
              <div class="content">{$form.last_run.value|crmDate}</div>
            </div>
            {* next run *}
            <div class="crm-section">
              <div class="label">{$form.next_run.label}</div>
              <div class="content">{$form.next_run.value|crmDate}</div>
            </div>
          {/if}
          <div class="crm-section">
            <div class="label">{$form.is_active.label}</div>
            {if $action eq 4}
              <div class="content">{$form.is_active.value}</div>
            {else}
              <div class="content">{$form.is_active.html}</div>
            {/if}
          </div>
          
        </tbody>
    </table>
    {* allow add scheduled trigger rules if action is edit *}
    {if $action eq 2}
        {include file="CRM/Triggers/Form/NewScheduleTrigger.tpl"}
    {/if}
    {* include scheduled trigger details if they are there any *}
    {if isset($scheduledTriggerRows)}
        {include file="CRM/Triggers/Page/CurrentScheduleTrigger.tpl"}
    {/if}

    {* FOOTER *}
    <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div>
