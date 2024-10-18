/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';


    // Define changes to default configuration here. For example:
    config.language = 'nl';
    // config.uiColor = '#AADC6E';
    config.resize_enabled = false;
    config.enterMode = CKEDITOR.ENTER_BR;
    config.height = '400';
    config.fullPage = true;
    config.extraPlugins = 'font';

    config.toolbar_custom =
        [
            ['Cut','Copy','Paste','PasteText','PasteFromWord'],
            ['Bold','Italic','Underline','Strike'],
            ['NumberedList','BulletedList'],
            ['Link','Unlink'],
            ['Image','Table','SpecialChar'],
            ['Font','FontSize'],
            ['TextColor'],
            ['Maximize'],['Source','About']
        ];

    config.allowedContent = {
        $1: {
            // Use the ability to specify elements as an object.
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true
        }
    };

    // prevent inline width of images, for some e-mailclients, and use older HTML tags
    config.disallowedContent = 'img{width,height}';

    //config.skin = 'kama';
    config.toolbar  = 'custom';
};
