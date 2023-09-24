/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {

	config.toolbarGroups = [
		{ name: 'clipboard', groups: ['clipboard', 'undo'] },
		{ name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'tools' },
		{ name: 'document', groups: ['mode', 'document', 'doctools'] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
		{ name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];
	config.extraPlugins = 'docprops';
	config.fullPage = true;
	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removePlugins = 'exportpdf';
	config.removeButtons = 'Templates,Save,Print,Flash,NewPage,TextField,Textarea,Form,Checkbox,SelectionField,RadioButton,HiddenField,Language,Iframe';
	config.enterMode = CKEDITOR.ENTER_BR;
	// Set the most common block elements.
	config.format_tags = 'div;p;h1;h2;h3;pre';
};
