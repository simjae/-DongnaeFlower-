//javascript
var editorConf = {
	'editorPath':'/gmeditor',
	'fileTmp':'/gmeditor/_temp',
	'fileUp':'/data/contentImage',	
};
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/js/jquery.js"></s'+'cript>');
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/js/jquery.event.drag-2.0.min.js"></s'+'cript>');
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/js/jquery.resizable.js"></s'+'cript>');
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/js/ajax_upload.3.6.js"></s'+'cript>');
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/js/ej.h2xhtml.js"></s'+'cript>');
document.write('<scrip'+'t type="text/javascr'+'ipt" src="'+editorConf.editorPath+'/editor.js"></s'+'cript>'); 
alert('d');
$(document).ready(function() {
	ejEditor();
	alert('dd');
});
