<h3>{$ruleTriggerHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-cron_trigger-block-activity_by_type_and_status">
    <div class="crm-section">
        <div class="label">{$form.activity_type_id.label}</div>
        <div class="content">{$form.activity_type_id.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.status_id.label}</div>
        <div class="content">{$form.status_id.html}</div>
        <div class="clear"></div>
        <div class="label">{$form.new_status_id.label}</div>
        <div class="content">{$form.new_status_id.html}</div>
    </div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>