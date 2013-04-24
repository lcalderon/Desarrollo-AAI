// $Id: zptabs-wizard.js 7343 2007-06-04 17:05:28Z vkulov $
/**
 * @fileoverview Tabs + Forms Wizard extension. Extends Tabs class (zptabs.js)
 * adding Forms functionality to create wizards.
 *
 * <pre>
 * Copyright (c) 2004-2006 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 * </pre>
 */

/**
 * Tabs + Forms Wizard.
 *
 * <pre>
 * Defines following additional config options:
 *
 * <b>action</b>: [string] "action" attribute value of hidden form.
 *
 * <b>submitTabId</b>: [string] Id of tab which shows sumbit result.
 *
 * <b>method</b>: [string, optional] 'GET' or 'POST'.
 *
 * <b>contentType</b>: [string, optional] Content type when using POST method.
 *
 * <b>formThemePath</b>: [string, optional] Relative or absolute URL to the form
 * themes directory (see Zapatec.From for details).
 *
 * <b>formTheme</b>: [string, optional] Theme name that will be used to display
 * the form (see Zapatec.From for details).
 *
 * <b>formStatusImgPos</b>: [string, optional] (see Zapatec.From for details).
 *
 * <b>showErrors</b>: [string, optional] (see Zapatec.From for details).
 *
 * <b>onError</b>: [function, optional] Callback function to call on error.
 * Receives following object as argument:
 * {
 *   tabId: [string] tab id
 *   serverSide: [boolean] true if this is server response or false if
 *    validation result,
 *   generalError: [string] Human readable error description,
 *   fieldErrors: [
 *     {
 *       field: [object] field element object,
 *       errorMessage: [string] Human readable error description
 *     },
 *     ...
 *   ]
 * }
 * See Zapatec.From submitErrorFunc config option for details.
 *
 * <b>onValid</b>: [function, optional] Callback function reference to call
 * after validation is passed. Receives following object as argument:
 * {
 *   tabId: [string] tab id
 * }
 * See Zapatec.From submitValidFunc config option for details.
 *
 * <b>onSuccess</b>: [function, optional] Callback function to call after form
 * was submitted and "success" response received from server. Receives object
 * passed from server as argument.
 * See Zapatec.From asyncSubmitFunc config option for details.
 * </pre>
 *
 * @constructor
 * @param {object} objArgs [object] User configuration
 */
Zapatec.TabsWizard = function(objArgs) {
	// Call constructor of superclass
	Zapatec.TabsWizard.SUPERconstructor.call(this, objArgs);
};

/**
 * Unique static id of the widget class. Gives ability for Zapatec#inherit to
 * determine and store path to this file correctly when it is included using
 * Zapatec#include. When this file is included using Zapatec#include or path
 * to this file is gotten using Zapatec#getPath, this value must be specified
 * as script id.
 * @private
 */
Zapatec.TabsWizard.id = 'Zapatec.TabsWizard';

// Inherit Tabs
Zapatec.inherit(Zapatec.TabsWizard, Zapatec.Tabs);

/**
 * Initializes object.
 *
 * @param {object} objArgs User configuration
 */
Zapatec.TabsWizard.prototype.init = function(objArgs) {
	// Define config options
	this.config.action = '';
	this.config.submitTabId = '';
	this.config.method = '';
	this.config.contentType = '';
	this.config.formThemePath = '';
	this.config.formTheme = '';
	this.config.formStatusImgPos = '';
	this.config.showErrors = '';
	this.config.onError = null;
	this.config.onValid = null;
	this.config.onSuccess = null;

	// Reference to this
	var objWizard = this;

	// Patch onInit handler
	var funcOnInit = objArgs.onInit;
	objArgs.onInit = function() {
		// Create hidden form
		var objHiddenForm = Zapatec.Utils.createElement('form');
		objHiddenForm.style.display = 'none';
		objHiddenForm.setAttribute('action', objWizard.config.action);
		objHiddenForm.setAttribute('method', objWizard.config.method);
		objHiddenForm.setAttribute('enctype', objWizard.config.contentType);
		objWizard.config.tabs.appendChild(objHiddenForm);

		// Initialize hidden form
		new Zapatec.Form({
			form: objHiddenForm,
			theme: objWizard.config.formTheme,
		// Error handler
			submitErrorFunc: function(objArgs) {
				if (typeof objWizard.config.onError != 'function') {
					return;
				}
				// If field error descriptions passed
				if (objArgs.fieldErrors && objArgs.fieldErrors.length) {
					for (var iTab = 0; iTab < objWizard.tabsArray.length; iTab++) {
						// Get tab
						var objTab = objWizard.tabsArray[iTab];
						if (!objTab.form) {
							continue;
						}
						// Extract errors belonging to this tab
						var arrFieldErrors = [];
						var objFields = objTab.formSource.elements;
						for (var iField = 0; iField < objFields.length; iField++) {
							// Get source field element
							var objField = objFields[iField];
							if (!Zapatec.Form.Utils.isInputField(objField)) {
								continue;
							}
							// Get field name
							var strName = objField.getAttribute('name');
							if (!strName.length) {
								continue;
							}
							// Find error respective to this field
							for (var iFerr = 0; iFerr < objArgs.fieldErrors.length; iFerr++) {
								if (objArgs.fieldErrors[iFerr].field.name == strName) {
									arrFieldErrors.push(objArgs.fieldErrors[iFerr]);
									break;
								}
							}
						}
						// Go to this tab if there are errors
						if (arrFieldErrors.length) {
							objWizard.changeTab(objTab.id);
							break;
						}
					}
				}
				else {
					// Go to first tab by default
					objWizard.changeTab(objWizard.tabsArray[0].id);
				}
				// Call original function
				objArgs.tabId = objWizard.tabsArray[objWizard.currentIndex].id;
				objWizard.config.onError(objArgs);
			},
		// Success handler
			asyncSubmitFunc: objWizard.config.onSuccess
		});

		// Patch onBeforeTabChange handler
		var funcOnBeforeTabChange = objWizard.config.onBeforeTabChange;
		objWizard.config.onBeforeTabChange = function(objArgs) {
			// Get new tab index
			var iNewTabIndex = -1;
			for (var iTab = 0; iTab < objWizard.tabsArray.length; iTab++) {
				if (objWizard.tabsArray[iTab].id == objArgs.newTabId) {
					iNewTabIndex = iTab;
					break;
				}
			}
			// Validate if trying to go forward
			if (iNewTabIndex > objWizard.currentIndex) {
				// Get old tab
				var objOldTab = objWizard.tabs[objArgs.oldTabId];
				// Validate form
				if (objOldTab && objOldTab.form && !objOldTab.form.submit()) {
					return false;
				}
			}
			// If submit tab clicked
			if (objArgs.newTabId == objWizard.config.submitTabId) {
				// Validate all forms
				for (var iTab = 0; iTab < objWizard.tabsArray.length; iTab++) {
					var objTab = objWizard.tabsArray[iTab];
					if (objTab.id == objArgs.newTabId) {
						// Skip this tab
						continue;
					}
					if (!objTab.parsed) {
						// Tab content is not parsed yet
						objWizard.changeTab(objTab.id);
						return false;
					}
					if (objTab.formSource) {
						// Tab contains form
						if (!objTab.form || !objTab.form.submit()) {
							// Form is not initialized yet or not valid
							objWizard.changeTab(objTab.id);
							return false;
						}
					}
				}
			}
			// Call original function
			if (typeof funcOnBeforeTabChange == 'function') {
				return funcOnBeforeTabChange(objArgs);
			}
			return true;
		};

		// Patch onTabChange handler
		var funcOnTabChange = objWizard.config.onTabChange;
		objWizard.config.onTabChange = function(objArgs) {
			// Get new tab id
			var strNewTabId = objArgs.newTabId;
			// Get new tab
			var objNewTab = objWizard.tabs[strNewTabId];
			if (!objNewTab) {
				return;
			}
			// Parse source to get form
			if (!objNewTab.parsed) {
				var objSource = objNewTab.container.getContentElement();
				// Get form
				var objForm = Zapatec.Utils.getFirstChild(objSource, 'form');
				// Form must have 'zpForm' in class attribute
				if (objForm && objForm.className.indexOf('zpForm') < 0) {
					// Try other forms
					while (objForm = Zapatec.Utils.getNextSibling(objSource, 'form')) {
						if (objForm.className.indexOf('zpForm') >= 0) {
							break;
						}
					}
				}
				if (objForm) {
					// Extend Tab class with formSource property
					objNewTab.formSource = objForm;
				}
				// Extend Tab class with parsed property
				objNewTab.parsed = true;
			}
			// Initialize source form if not initialized yet
			if (!objNewTab.form && objNewTab.formSource) {
				// Extend Tab class with form property
				objNewTab.form = new Zapatec.Form({
					form: objNewTab.formSource,
					themePath: objWizard.config.formThemePath,
					theme: objWizard.config.formTheme,
					statusImgPos: objWizard.config.formStatusImgPos,
					showErrors: objWizard.config.showErrors,
					submitErrorFunc: function(objArgs) {
						objArgs.tabId = strNewTabId;
						if (typeof objWizard.config.onError == 'function') {
							objWizard.config.onError(objArgs);
							// Check if there are invalid fields
							if (objArgs.fieldErrors && objArgs.fieldErrors.length) {
								// Focus on first invalid field
								objNewTab.focusOn = objArgs.fieldErrors[0].field;
							}
						}
					},
					submitValidFunc: function() {
						if (typeof objWizard.config.onValid == 'function') {
							objWizard.config.onValid({
								tabId: strNewTabId
							});
						}
					}
				});
			}
			// If submit tab clicked
			if (strNewTabId == objWizard.config.submitTabId) {
				// Populate hidden form
				for (var iTab = 0; iTab < objWizard.tabsArray.length; iTab++) {
					// Get tab
					var objTab = objWizard.tabsArray[iTab];
					if (!objTab.form) {
						continue;
					}
					// Get source form
					var objForm = objTab.formSource;
					if (!objForm) {
						continue;
					}
					for (var iField = 0; iField < objForm.elements.length; iField++) {
						// Get source field element
						var objField = objForm.elements[iField];
						if (!Zapatec.Form.Utils.isInputField(objField)) {
							continue;
						}
						// Get field name
						var strName = objField.getAttribute('name');
						if (strName && !strName.length) {
							continue;
						}
						// Get hidden field element
						var objHiddenField = objHiddenForm[strName];
						if (!objHiddenField) {
							// Create new field element
							var objHiddenField = Zapatec.Utils.createElement('input');
							objHiddenField.setAttribute('type', 'hidden');
							objHiddenField.setAttribute('name', strName);
							objHiddenForm.appendChild(objHiddenField);
						}
						// Set value
						objHiddenField.setAttribute('value', objField.value);
					}
				}
				// Submit hidden form
				objHiddenForm.onsubmit();
			}
			// Call original function
			if (typeof funcOnTabChange == 'function') {
				funcOnTabChange(objArgs);
			}
		};

		// Call original function
		if (typeof funcOnInit == 'function') {
			funcOnInit(objArgs);
		}
	};

	// Call parent init
	Zapatec.TabsWizard.SUPERclass.init.call(this, objArgs);
};
