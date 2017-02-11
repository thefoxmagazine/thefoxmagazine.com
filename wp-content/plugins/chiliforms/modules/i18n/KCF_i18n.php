<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_i18n {
	public static function get_translations() {
		$translations = array(

		    // forms
			'forms.save-form' => __('Save', 'chiliforms'),
			'forms.loading' => __('Loading...', 'chiliforms'),
			'forms.back-to-forms-list' => __('View forms', 'chiliforms'),
			'forms.create-new' => __('Create new', 'chiliforms'),
			'forms.submit' => __('Submit', 'chiliforms'),
			'forms.no-added-fields' => __('You have not added fields yet.', 'chiliforms'),
			'forms.start-dragging-fields' =>
			    __('Start building by dragging some fields from the palette.', 'chiliforms'),
            'forms.popup.delete-field.are-you-sure' => __('Are you sure you want to delete field? It will also remove all entries submitted for this field', 'chiliforms'),
            'forms.popup.delete-form.are-you-sure' => __('Are you sure you want to delete form? It will also delete all entries submitted for this form', 'chiliforms'),
            'forms.popup.delete-entries.are-you-sure' => __('Are you sure you want to delete selected entries?',
                'chiliforms'),
            'forms.popup.common.confirm' => __('Confirm', 'chiliforms'),
            'forms.popup.common.cancel' => __('Cancel', 'chiliforms'),

            // forms list
			'forms.list.edit' => __('Edit', 'chiliforms'),
			'forms.list.delete' => __('Delete', 'chiliforms'),
			'forms.list.no-saved-forms' => __('You have no forms', 'chiliforms'),
			'forms.list.go-ahead' => __('Looks like you haven\'t created any forms yet. Go ahead and', 'chiliforms'),
			'forms.list.create-one' => __('create one now', 'chiliforms'),
			'forms.list.forms' => __('Forms', 'chiliforms'),
			'forms.list.toggle.active' => __('Active', 'chiliforms'),
			'forms.list.toggle.inactive' => __('Inactive', 'chiliforms'),
			'forms.list.table-header.active' => __('Active', 'chiliforms'),
			'forms.list.table-header.id' => __('Id', 'chiliforms'),
			'forms.list.table-header.name' => __('Name', 'chiliforms'),
			'forms.list.table-header.entries' => __('Entries', 'chiliforms'),
			'forms.list.table-header.shortcode' => __('Shortcode', 'chiliforms'),
			'forms.list.table-header.edit' => __('Edit', 'chiliforms'),
			'forms.list.table-header.delete' => __('Delete', 'chiliforms'),

			// forms editor
			'forms.editor.delete-field' => __('Delete field', 'chiliforms'),
			'forms.editor.field-id' => __('Id', 'chiliforms'),
			'forms.editor.field-name' => __('Name', 'chiliforms'),
			'forms.editor.field-type' => __('Type', 'chiliforms'),
			'forms.editor.field-options' => __('Options', 'chiliforms'),
			'forms.editor.field-settings' => __('Field settings', 'chiliforms'),
			'forms.editor.add-fields' => __('Add fields', 'chiliforms'),

			// forms notifications
			'forms.notifications.form-save-success' => __('Form saved successfully', 'chiliforms'),
			'forms.notifications.form-delete-success' => __('Form deleted successfully', 'chiliforms'),
			'forms.notifications.form-create-success' => __('New form created', 'chiliforms'),

            // common
			'forms.field-options.common.custom-css.label' => __('Custom CSS classes', 'chiliforms'),
            'forms.field-options.common.required.label' => __('Required field?', 'chiliforms'),
			'forms.field-options.common.label.label' => __('Enter field label', 'chiliforms'),

			// input
            'forms.field-options.input.label.value' => __('Enter your name', 'chiliforms'),
            'forms.field-options.input.placeholder.label' => __('Enter placeholder for input', 'chiliforms'),
            'forms.field-options.input.placeholder.value' => __('John Doe', 'chiliforms'),

            // textarea
            'forms.field-options.textarea.label.value' => __('Type your message', 'chiliforms'),
            'forms.field-options.textarea.placeholder.label' => __('Enter placeholder for input', 'chiliforms'),
            'forms.field-options.textarea.placeholder.value' => __('Start typing message', 'chiliforms'),

            // select
            'forms.field-options.select.label.value' => __('Select one option', 'chiliforms'),
            'forms.field-options.select.placeholder.label' =>
                __('Enter placeholder for dropdown (default empty option)', 'chiliforms'),
            'forms.field-options.select.placeholder.value' => __('Select option', 'chiliforms'),
            'forms.field-options.select.options.label' => __('Add multiple options', 'chiliforms'),
            'forms.field-options.select.options.option.value' => __('New option', 'chiliforms'),
            'forms.field-options.select.options.option.value.tooltip' => __('Option text label', 'chiliforms'),
            'forms.field-options.select.options.option.key.tooltip' => 
                __('Option key (optional). Use this field if you need saved value different from text label', 
                'chiliforms'),
            'forms.field-options.select.options.default.value1' => __('Option 1', 'chiliforms'),
            'forms.field-options.select.options.default.value2' => __('Option 2', 'chiliforms'),
            'forms.field-options.select.options.default.value3' => __('Option 3', 'chiliforms'),

            // checkbox
            'forms.field-options.checkbox.options.label' => __('Add multiple options', 'chiliforms'),
            'forms.field-options.checkbox.options.option.value' => __('New option', 'chiliforms'),
            'forms.field-options.checkbox.options.option.value.tooltip' => __('Checkbox label', 'chiliforms'),
            'forms.field-options.checkbox.options.option.key.tooltip' => 
                __('Checkbox key (optional). Use this field if you need saved value different from label', 
                'chiliforms'),
            'forms.field-options.checkbox.options.default.value1' => __('Option 1', 'chiliforms'),
            'forms.field-options.checkbox.options.default.value2' => __('Option 2', 'chiliforms'),
            'forms.field-options.checkbox.options.default.value3' => __('Option 3', 'chiliforms'),

            // radio
            'forms.field-options.radio.options.label' => __('Add multiple options', 'chiliforms'),
            'forms.field-options.radio.options.option.value' => __('New option', 'chiliforms'),
            'forms.field-options.radio.options.option.value.tooltip' => __('Radio label', 'chiliforms'),
            'forms.field-options.radio.options.option.key.tooltip' =>
                __('Radio key (optional). Use this field if you need saved value different from label',
                'chiliforms'),
            'forms.field-options.radio.options.default.value1' => __('Option 1', 'chiliforms'),
            'forms.field-options.radio.options.default.value2' => __('Option 2', 'chiliforms'),

            // entries
			'entries.show-empty-fields' => __('Show empty fields', 'chiliforms'),
			'entries.hide-empty-fields' => __('Hide empty fields', 'chiliforms'),
			'entries.no-entries' => __('No entries to display', 'chiliforms'),
			'entries.form' => __('Form', 'chiliforms'),
			'entries.ip' => __('IP', 'chiliforms'),
			'entries.forms' => __('Forms', 'chiliforms'),
			'entries.starred' => __('Starred', 'chiliforms'),
			'entries.unread' => __('Unread', 'chiliforms'),
			'entries.delete' => __('Delete', 'chiliforms'),
			'entries.mark-as-read' => __('Mark as read', 'chiliforms'),
			'entries.mark-as-unread' => __('Mark as unread', 'chiliforms'),
			'entries.count-tooltip.unread-read' => __('Unread/Total', 'chiliforms'),
			'entries.select-entries' => __('Select entries to show actions', 'chiliforms')
		);

		return $translations;
	}
}