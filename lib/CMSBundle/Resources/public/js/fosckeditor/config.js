/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    //config.removeButtons = 'Print,Form,TextField,Textarea,Button,CreateDiv,PasteText,PasteFromWord,Select,HiddenField,Radio,Checkbox,ImageButton,Anchor,BidiLtr,BidiRtl,Font,Format,Styles,Preview,Indent,Outdent';
    config.removeButtons= 'SpecialChar,Iframe,About,iFrame,Language,Smiley,Underline,Subscript,Superscript,Form,TextField,Textarea,Button,CreateDiv,Select,HiddenField,Radio,Checkbox,ImageButton,Anchor,Flash,Preview,Indent,Outdent';
    //config.removePlugins = 'blockquote,save,flash,iframe,tabletools,pagebreak,templates,about,showblocks,newpage,language,print,div';
    // config.removePlugins= 'blockquote,save,flash,iframe,tabletools,pagebreak,templates,about,showblocks,newpage,print';

    config.contentsCss = '/assets/css/style.css';

};

CKEDITOR.stylesSet.add('default',[
    { name: "btn-rouge", element: "a", attributes: { 'class': "btn btn-bg-red btn-h50 btn-w15" }},
    { name: "btn-blue", element: "a", attributes: { 'class': "btn btn-bg-blue btn-h50 btn-w15" }},
    { name: "btn-blanc", element: "a", attributes: { 'class': "btn btn-bg-white btn-h50 btn-w15" }},
    { name: "btn-transparent", element: "a", attributes: { 'class': "btn btn-bg-transparent btn-h50 btn-w15" }},
    { name: "btn-transparent-blanc", element: "a", attributes: { 'class': "btn btn-bg-transparent-white btn-h50 btn-w15" }},
    { name: "btn-transparent-noire", element: "a", attributes: { 'class': "btn btn-transparent-black btn-h50 btn-w15" }},
    { name: "btn-transparent-rouge", element: "a", attributes: { 'class': "btn btn-transparent-red btn-h50 btn-w15" }},
    { name: "btn-degradé", element: "a", attributes: { 'class': "btn btn-orange-yellow btn-h50 btn-w15" }},
    { name: "btn-info", element: "a", attributes: { 'class': "btn btn-outline-info btn-h50 btn-w15" }},
    { name: "description", element: "p", attributes: { 'class': "description" }},
    { name: "u-list", element: "ul", attributes: { 'class': "u-list" }},
    { name: "table", element: "table", attributes: { 'class': "table" }},
]);
