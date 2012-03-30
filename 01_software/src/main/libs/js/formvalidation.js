// $Id: formvalidation.js,v 1.1 2007-07-10 09:30:20 pl Exp $
// (c) 2011 INRIA
// (c) 2004-2007 Icatis
//
// This file contains several javascript functions used for form validations
//
// Note: These functions are meant to improve the user experience when
// javascript is available. That does not mean you may skip server-side validations !

// {{{ function anyelementchecked()
// ROLE Check if at least one checkbox is checked in form
// IN form name
// RET boolean true if at least one box checked, false otherwise
// CALL in order to prevent the form submit action if this function returns false,
//      call it in a <form> tag using onsubmit="return anyelementchecked(...)"
//      or in a <input type="submit"> tag using onclick="return anyelementchecked(...)"
function anyelementchecked(form_name) {
	var l = form_name.elements.length;
	var onecheck = false;

	for (i = 0; i < l; ++i) {
		if (form_name.elements[i].checked) {
			onecheck = true;
		}
	}
	if (!onecheck) {
		alert('Please select at least one item for this action');
		return false;
	}
	return true;
}
// }}}

// {{{ function oneelementchecked()
// ROLE Check if exactly one checkbox is checked in form
// IN form name
// RET boolean true if one box checked, false otherwise
// CALL in order to prevent the form submit action if this function returns false,
//      call it in a <form> tag using onsubmit="return anyelementchecked(...)"
//      or in a <input type="submit"> tag using onclick="return anyelementchecked(...)"
function oneelementchecked(form_name) {
	var l = form_name.elements.length;
	var checks = 0;

	for (i = 0;i < l; ++i) {
		if (form_name.elements[i].checked) {
			++checks;
		}
	}
	if (checks != 1) {
		alert('Please select one item for this action');
		return false;
	}
	return true;
}
// }}}

// {{{ function select_all(checkall_cb, form_name, cb_name)
// ROLE: handle the use of a checkall button in the forms
// IN: checkall_cb as the checkbox which control the others
// IN: document.biform as the form containing every checkboxes
// IN: the checkboxes names - note that [] will be added
// RET: nothing
// Example:
//  <input type="checkbox" onclick="javascript:select_all(this, document.biform, 'selectedbootimageid');">
function select_all(checkall_cb, form_name, cb_name) {
	if (!checkall_cb) {
		return;
	}
	if (!form_name) {
		return;
	}
	if (!cb_name) {
		return;
	}

	var all_cb = form_name.elements[cb_name+'[]'];


	var box = (all_cb.length) ? all_cb : new Array(all_cb);
	var check = checkall_cb.checked;
	for (var i = 0; i < box.length; ++i) {
		box[i].checked = check;
	}
}
// }}}

