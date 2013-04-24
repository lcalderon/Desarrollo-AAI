// $Id: zptabs-core.js 7561 2007-07-16 21:06:11Z vkulov $
/**
 * @fileoverview Tabs widget. Extends base Widget class (utils/zpwidget.js).
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
 * Zapatec.Tab constructor. Creates a new tab object with given
 * parameters.
 *
 * @constructor
 * @extends Zapatec.Widget
 * @param {object} objArgs Tab configuration
 * @private Don't use this class directly
 *
 * Constructor recognizes the following properties of the config object
 * \code
 *	property name			| description
 *-------------------------------------------------------------------------------------------------
 *	id			| [string] Id of the tab. Used to identify the tab uniquely.
 *  index   | [number] Index where tab is to be inserted at
 *	linkInnerHTML   | [string] The HTML content to be placed inside link part
 *          | of the tab. That is the one inside the tab bar that you click on
 *          | to activate the tab (also called tab label).
 *	accessKey	| [string] Keyboard shortcut that activates the tab when pressed in
 *          | combination with ALT
 *	content	| [mixed, optional] Reference to HTMLElement object holding
 *          | content of the tab or an URL string to tab content
 *	contentType	| [string, optional] Meaning of content config option. Valid
 *          | values are "html", "html/text", "html/url". See Zapatec.Pane for
 *          | more details.
 *	url	| [string, optional] Url location to fetch tab content from. Deprecated.
 *          | Use content config param along with contentType="html/url"
 *	title	| [string, optional] Title html attribute to apply to tab label anchor.
 *          | Acts as a tooltip of the tab label.
 *	tabType	| [string] "div" or "iframe" depending on what kind of pane is
 *          | needed for the tab
 *	tab2tab	| [boolean] If true, pressing Tab key will open next tab. If false,
 *          | Tab key will also navigate through anchors and form fields inside
 *          | tab. Default: false.
 *	tabParent	| [object] Reference to HTMLElement that this tab is to be added to.
 *          | Used internally to decouple Tab from Tabs.
 *  changeUrl | [boolean] Specifies if browser location hash is to be changed
 *          | on every tab change to reflect the current tab
 *  overflow | [string] Tab content overflow html attribute. Default: null (use CSS
 *          | applied overflow style)
 *  refreshOnTabChange | [boolean] When this option is set to true this tab url
 *          | is re-fetched before it becomes active. Default is false.
 *  shouldLoadOnInit | [boolean] When this option is set to true this tab url
 *          | will be loaded as soon as tab is created. Default is false.
 *  activeTabId | [string] Specifies the tab that is to become active after Tabs
 *          | initialization. If not specified the first tab will become active.
 * \endcode
 *
 */
Zapatec.Tab = function(objArgs) {
	if (arguments.length == 0) {
		objArgs = {};
	}

	// Call constructor of superclass
	Zapatec.Tab.SUPERconstructor.call(this, objArgs);
};

/**
 * Unique static id of the widget class. Gives ability for Zapatec#inherit to
 * determine and store path to this file correctly when it is included using
 * Zapatec#include. When this file is included using Zapatec#include or path
 * to this file is gotten using Zapatec#getPath, this value must be specified
 * as script id.
 * @private
 */
Zapatec.Tab.id = 'Zapatec.Tab';

// Inherit Widget
Zapatec.inherit(Zapatec.Tab, Zapatec.Widget);

/**
 * Initializes object.
 *
 * @param {object} objArgs User configuration
 */
Zapatec.Tab.prototype.init = function(objArgs) {

	// processing Widget functionality
	Zapatec.Tab.SUPERclass.init.call(this, objArgs);

	this.createTab();
};

/**
 * Creates elements needed for the new tab
 */
Zapatec.Tab.prototype.createTab = function () {
	// Reference to tab link element
	this.createProperty(this, 'linkNode', null);
	// Reference to pane containing tab content
	this.createProperty(this, 'container', null);
	// Child element which gets focus first (needed for keyboard navigation)
	this.createProperty(this, 'focusOn', null);
	this.createProperty(this, 'linkHash', null);
	// If set an onTabChange event is to be called after onContentLoad
	this.createProperty(this, 'pendingOnTabChange', null);

	// Create content with pane
	this.container = new Zapatec.Pane({
		containerType : this.config.tabType,
		parent : this.config.tabParent,
		overflow : this.config.overflow,
		id: this.config.id
	});
	this.container.removeBorder();

	// Add "zpTab" class to to all tab content divs
	var className = "zpTab";
	// If a class name is specified for the tab content div
	if (this.config.className) {
		className += " " + this.config.className;
	}
	// Set tab container className
	this.container.getContainer().className = className;

	var self = this;
	// Define pane content loaded handler
	var onContentLoaded = function() {
		if (self.pendingOnTabChange) {
			self.pendingOnTabChange(self);
			self.pendingOnTabChange = null;
		}

		self.fireEvent('tabOnLoad');

		setTimeout(function() {
			// Re-add pane contentLoaded listener
			self.container.addEventListener('contentLoaded', self.onContentLoaded);
		}, 10);
	}
	// Store onLoad event handler
	this.createProperty(this, 'onContentLoaded', onContentLoaded);

	// Add pane contentLoaded listener
	this.container.addEventListener('contentLoaded', onContentLoaded);

	// Hide content
	this.container.hide();
	this.container.getContainer().style.width = '100%';
	this.container.getContainer().style.height = '100%';

	if (!this.config.url) {
		// Update tab content
		this.setPaneContent();
	}
	else {
		if (this.config.shouldLoadOnInit) {
			// Update tab content
			this.setPaneContent();
		}
	}

	// Get id
	if (this.config.id) {
		this.id = this.config.id;
	}
	else {
		this.id = this.container.getContainer().getAttribute('id');
		if (typeof this.id == 'string') {
			this.container.getContainer().removeAttribute('id');
		}
	}
	if (typeof this.id != 'string' || !this.id.length) {
		// Generate unique id
		this.id = Zapatec.Utils.generateID('tab');
	}
	// Create link node
	this.linkNode = Zapatec.Utils.createElement('div');

	this.linkNode.onmouseover = function() {
		var outer = Zapatec.Utils.getFirstChild(self.linkNode, "div");
		Zapatec.Utils.addClass(outer, "zpTabsHover");
	}
	this.linkNode.onmouseout = function() {
		var outer = Zapatec.Utils.getFirstChild(self.linkNode, "div");
		Zapatec.Utils.removeClass(outer, "zpTabsHover");
	}
	this.linkNode.className = "zpTab";
	this.linkNode.name = this.id;
	// Need "<div><div>" for themes
	var innerClass = "zpTabLinkInner";
	if (this.config.closable) {
		innerClass += " zpTabClosable";
	}
	var innerHtml = '<div class="zpTabLinkOuter"><div class="' + innerClass + '">' +
	                '<div class="zpTabAnchorHolder"><a ';

	if (this.config.accessKey) {
		innerHtml += 'accessKey="' + this.config.accessKey + '" ';
	}
	if (this.config.title) {
		innerHtml += 'title="' + this.config.title + '" ';
	}
	innerHtml += '>' + this.config.linkInnerHTML + '</a></div>';

	if (this.config.closable) {
		var closeUrl = this.config.themePath + this.config.theme + "/close.gif";

		var closeImgHtml = '<img class="zpTabClose" border=0 src="' + closeUrl + '">';
		innerHtml += closeImgHtml;
	}
	innerHtml += '<div class="zpTabsClearer"></div>';
	innerHtml += '</div></div>';
	this.linkNode.innerHTML = innerHtml;

	var self = this;

	var closeImages = this.linkNode.getElementsByTagName('img');
	if (closeImages && 0 < closeImages.length) {
		closeImages[0].onmousedown = function(ev) {
			Zapatec.Tab.CloseTab(self.config.tabsId, self.id);

			Zapatec.Utils.stopEvent(ev);

			return false;
		}
	}

	// Mouse navigation support
	this.onActivate = function(e) {
		self.fireEvent('activateTab');

		if (self.blur) {
			self.blur();
		}

		// Don't navigate to link (don't refresh the page)
		var doNavigate = false;

		// If url is to be changed
		if (self.config.changeUrl) {
			if (Zapatec.is_khtml) {
				// Make safari navigate the link.
				// Changing location.hash does not work properly on Safari
				doNavigate = true;
			}
			else {
				// Modify window URL
				window.location.hash = self.linkHash;
			}
		}

		Zapatec.Utils.stopEvent(e);

		return doNavigate;
	};
	// If tab are to be changed on mouse over
	if (this.config.mouseOverChangesTab) {
		this.linkNode.onmouseover = this.onActivate;
	}
	else {
		//this.linkNode.onclick = this.onActivate;
	}

	// Keyboard navigation support
	//this.linkNode.tabIndex = Zapatec.Tab.tabIndex;
	if (!this.tab2tab) {
		// Next tabIndex is reserved for tab content
		//Zapatec.Tab.tabIndex += 2;
	}
	// Activate tab on focus
	this.linkNode.onfocus = this.onActivate;
	// Setup keys
	this.linkNode.onkeydown = function(ev) {
		ev || (ev = window.event);
		switch (ev.keyCode) {
			case 13: // Enter
			case 32: // Space
				if (self.focusOn && self.focusOn.focus) {
					self.focusOn.focus();
				}
				// Stop event
				return false;
		}
		// Continue event
		return true;
	}
	// Determine child element which gets focus first
	if (this.container.getContainer().hasChildNodes()) {
		this.getFocusOn();
	}
}

/**
 * Sets tab link href given the hash
 *
 * @private
 * @param {string} linkHash Bookmark hash to set to link href
 */
Zapatec.Tab.prototype.setLinkHash = function(linkHash) {
	this.linkHash = linkHash;

	this.linkNode.setAttribute('href', '#' + linkHash);
}

/**
 * Configures the widget. Gets called from init and reconfigure methods of
 * superclass.
 *
 * @private
 * @param {object} objArgs User configuration
 */
Zapatec.Tab.prototype.configure = function(objArgs) {
	// Define config options
	// Id of the tab
	this.defineConfigOption('id', null);
	// Index of the tab
	this.defineConfigOption('index', -1);
	// Tab link inner html
	this.defineConfigOption('linkInnerHTML', '');
	// Tab access key
	this.defineConfigOption('accessKey', '');
	// Tab content
	this.defineConfigOption('content', null);
	// Tab content type
	this.defineConfigOption('contentType', null);
	// URL of the content
	this.defineConfigOption('url', '');
	//should we show content in IFRAME Pane or just simple div Pane
	this.defineConfigOption('tabType', "div");

	// Keyboard navigation type
	this.defineConfigOption('tab2tab', false);
	// Tab parent
	this.defineConfigOption('tabParent', null);
	this.defineConfigOption('title', null);

	// Specifies if tab can be closed using an x button that is visible
	this.defineConfigOption('closable', false);
	// Specifies what happens then the x button is clicked
	this.defineConfigOption('closeAction', 'close');

	// If browser url needs to be changed on every tab change
	this.defineConfigOption('changeUrl', true);

	// Determine tab content overflow style attribute
	this.defineConfigOption('overflow', null);

	// Determine if tabs are to be changed on mouse over
	this.defineConfigOption('mouseOverChangesTab', false);

	// Determine if tabs are to be re-fetched every time they are activated
	this.defineConfigOption('refreshOnTabChange', false);

	// Determine if tab content is to be fetched on tab creation
	this.defineConfigOption('shouldLoadOnInit', false);

	this.defineConfigOption('tabsId', null);

	// Determines tab initial visibility
	this.defineConfigOption('visible', true);
	// Determines tab content container class names
	this.defineConfigOption('className', null);

	this.defineConfigOption('langId', Zapatec.Tabs.id);
	this.defineConfigOption('lang', "eng");

	// Call parent method
	Zapatec.Tab.SUPERclass.configure.call(this, objArgs);

	// Check if required param "tabParent" is defined
	if (typeof(this.config.tabParent) == "undefined") {
		this.initLang();
		Zapatec.Log({description: this.getMessage("unknownTabParentError")})
		return false;
	}

	// Check keyboard navigation type
	if (this.config.tab2tab && false != this.config.tab2tab) {
		this.config.tab2tab = false;
	}

	// Check tabType
	if (typeof this.config.tabType == "string") {
		this.config.tabType = this.config.tabType.toLowerCase();
	}
	if (this.config.tabType != "div" && this.config.tabType != "iframe")
	{
		this.config.tabType = "div";
	}

	if (this.config.index < 0) {
		this.config.index = -1;
	}
};

/**
 * Counter that gets increased after Tab is added. Required for keyboard
 * navigation support.
 *
 * Note:
 * In Opera tabIndex property value must be > 0, otherwise node is ignored.
 * Mozilla starts travelling from nodes with tabIndex > 0.
 * IE starts travelling from nodes with tabIndex == 0.
 * All nodes without tabIndex set explicitly have tabIndex == 0.
 */
//Zapatec.Tab.tabIndex = 1000;

/**
 * Determines child node of the container which gets focus first.
 * Needed for keyboard navigation.
 * @private
 */
Zapatec.Tab.prototype.getFocusOn = function() {
	// Remove old value
	this.focusOn = null;
	// Check keyboard navigation type
	if (this.tab2tab) {
		return false;
	}
	// Put it in separate process to speed up initialization
	var self = this;
	setTimeout(function() {
		// Flag to determine lower tabIndex
		var iTabIndex = 0;
		// Gets element with lower tabIndex
		function parse(objNode) {
			var objChild = objNode.firstChild;
			while (objChild) {
				if (objChild.nodeType == 1) { // ELEMENT_NODE
					var strTag = objChild.tagName.toLowerCase();
					if (strTag == 'a' || strTag == 'input' || strTag == 'select' ||
					    strTag == 'textarea' || strTag == 'button') {
						// Element may obtain focus
						if (!self.focusOn) {
							self.focusOn = objChild;
						}
						else if (objChild.tabIndex && objChild.tabIndex > 0 &&
						         (!iTabIndex || iTabIndex > objChild.tabIndex)) {
							self.focusOn = objChild;
							//iTabIndex = objChild.tabIndex;
						}
						if (!objChild.tabIndex) {
							//objChild.tabIndex = self.linkNode.tabIndex + 1;
						}
					}
					parse(objChild);
				}
				objChild = objChild.nextSibling;
			}
		}
		;
		// Parse tab contenet
		parse(self.container);
	}, 0);
};

/**
 * Sets tab content from given HTML fragment.
 *
 * @param {string} strHtml HTML fragment.
 * @private
 */
Zapatec.Tab.prototype.setInnerHtml = function(strHtml) {
	// Set tab content
	Zapatec.Transport.setInnerHtml({
		html: strHtml,
		container: this.container.getContainer()
	});
	// Determine child element which gets focus first
	this.getFocusOn();
}

/**
 * Sets tab pane content
 *
 * @public
 * @param {mixed} content value for the content
 * @param {string} type type of content: "html", "html/text", "html/url"
 * @return {boolean} true if successfull, otherwise false.
 */
Zapatec.Tab.prototype.setPaneContent = function(content, type) {
	var self = this;

	var paneContent = null;
	var paneContentType = null;

	if (content) {
		paneContent = content;
		paneContentType = type;
	}
	else if (this.config.url && 0 < this.config.url.length) {
		paneContent = this.config.url;
		paneContentType = 'html/url';
	}
	else {
		paneContent = this.config.content;
		paneContentType = this.config.contentType;
	}

	if (paneContent) {

		this.config.content = content;
		this.config.contentType = type;

		// If provided content is an URL
		if (paneContentType == "html/url") {
			this.config.url = paneContent;
			this.lastUrlSet = paneContent;
		}

		this.container.setPaneContent(paneContent, paneContentType);
	}
}

/**
 * Shows/hides a tab
 *
 * @param {boolean} isVisible HTML fragment.
 * @private
 */
Zapatec.Tab.prototype.setVisible = function(isVisible) {
	this.config.visible = isVisible;

	if (isVisible) {
		this.linkNode.style.display = 'block';
	}
	else {
		this.linkNode.style.display = 'none';
	}
}

/**
 * Removes a specified tab from some tabs object
 *
 * @private
 * @param {number} tabsId id of the tabs object 
 * @param {string} id tab id
 */
Zapatec.Tab.CloseTab = function(tabsId, id) {

	// Get tabs object by id
	var objTabs = Zapatec.Widget.getWidgetById(tabsId);

	var objTab = objTabs.getTab(id);
	if (objTab.config.closeAction == 'close') {
		// Remove tab
		objTabs.removeTab(id);
	}
	else if (objTab.config.closeAction == 'hide') {
		// Determine if we are removing the currently active tab
		var isRemovingCurrent = objTab.index == objTabs.currentIndex;

		// if we are removing the currently active tab
		if (isRemovingCurrent) {
			// Get previous visible tab
			var previousTab = objTabs.getPreviousTab(true, objTabs.currentIndex);
			if (previousTab) {
				//objTabs.changeTab(previousTab.id);
			}
			else {
				// Get next visible tab
				var nextTab = objTabs.getNextTab(true, objTabs.currentIndex);
				if (nextTab) {
					//objTabs.changeTab(nextTab.id);
				}
			}
		}

		objTab.setVisible(false);
	}

	objTabs.fireEvent('tabOnClose', id);
}




/**
 * Zapatec.Tabs constructor. Creates a new tabs object with given
 * parameters.
 *
 * @constructor
 * @extends Zapatec.Widget
 * @param {object} objArgs Tab configuration
 *
 * Constructor recognizes the following properties of the config object
 * \code
 *	property name			| description
 *-------------------------------------------------------------------------------------------------
 *	tabBar		| [object or string] Element or id of element that will hold
 *            | the tab bar.
 *	tabs			| [object or string] Element or id of element that will hold the
 *            | all content panes. This element can also hold tabs content.
 *	tabType	  | [string] "div" or "iframe" depending on what kind of pane is
 *            | needed for the tabs
 *  overflow | [string] Tab content overflow html attribute. Default: null
 *	onInit		| [function] Called when tabs are created. Users can perform
 *            | problem-specific initializations at this stage. No arguments.
 *	onTabChange		| [function] Called after the tab was changed. Receives
 *            |  following object:
 *            |  {
 *            |  oldTabId: [string] id of the old tab,
 *            |  newTabId: [string] id of the new tab
 *            |  }
 *	onBeforeTabChange		| [function] Called when the tab is about to be changed,
 *            | just before. Receives following object:
 *            | {
 *            |  oldTabId: [string] id of the old tab,
 *            |  newTabId: [string] id of the new tab
 *            | }
 *            | Should return boolean. If returns other than "true", tab will
 *            | not be changed.
 *	ignoreUrl		| [boolean] If true, "#tabsId=tabId" part of URL is ignored and
 *            | always the first tab is opened after page load.
 *  changeUrl | [boolean] Specifies if browser location hash is to be changed
 *          | on every tab change to reflect the currently active tab.
 *	tab2tab		| [boolean] If true, pressing Tab key on the keyboard will navigate
 *            | to next tab. If false, Tab key will also navigate through
 *            | anchors and form fields inside tab. Default: false.
 *	scrollMultiple | [boolean] If true, scrolls the tabs one 'page' at a time.
 *            | Otherwise, scrolls singly.
 *	iframeContent | [boolean] Marks the pane content as being an <iframe>.
 *            | Deprecated. Use tabType instead.
 *	windowOnLoad | [function] If set, this is run on window load.
 *	showEffect | [string or array] What effects to apply to tab content when a
 *            | tab is about to be visible.
 *	showEffectSpeed | [number] Tab show animation speed. From 1(low speed) to
 *            | 100(high speed)
 *  mouseOverChangesTab | [boolean] specifies if tabs are to be changed by
 *            | merely hoveing over them with the mouse. Default is false.
 *  refreshOnTabChange | [boolean] When this option is set to true tab url
 *           | is re-fetched on every tab change. Default is false.
 *  shouldLoadOnInit | [boolean] When this option is set to true this tab url
 *          | will be loaded as soon as tab is created. Default is false.
 */
Zapatec.Tabs = function(objArgs) {
	if (arguments.length == 0) {
		objArgs = {};
	}

	Zapatec.Tabs.SUPERconstructor.call(this, objArgs);
};

/**
 * Unique static id of the widget class. Gives ability for Zapatec#inherit to
 * determine and store path to this file correctly when it is included using
 * Zapatec#include. When this file is included using Zapatec#include or path
 * to this file is gotten using Zapatec#getPath, this value must be specified
 * as script id.
 * @private
 */
Zapatec.Tabs.id = 'Zapatec.Tabs';

// Inherit Widget
Zapatec.inherit(Zapatec.Tabs, Zapatec.Widget);

/**
 * Initializes object.
 *
 * @param {object} objArgs User configuration
 */
Zapatec.Tabs.prototype.init = function(objArgs) {

	// Call init method of superclass
	Zapatec.Tabs.SUPERclass.init.call(this, objArgs);

	this.createTabs();

	this.initTabBar();
};

/**
 * Creates tabs DOM elements
 * @private
 */
Zapatec.Tabs.prototype.createTabs = function() {
	// To maintain by id
	this.createProperty(this, 'tabs', {});
	// To maintain by index
	this.createProperty(this, 'tabsArray', []);

	if (null == this.tabsThemeSuffix) {
		this.tabsThemeSuffix = 'Content';
	}

	// Apply theme
	Zapatec.Utils.addClass(this.config.tabs, this.getClassName({
		prefix: 'zpTabs',
		suffix: this.tabsThemeSuffix
	}));

	// Call parent method to load data from the specified source
	this.loadData();

	// Index of the current tab
	this.currentIndex = -1;
	// onInit
	if (typeof this.config.onInit == 'function') {
		this.config.onInit();
	}

	// Decide which tab to activate initially
	if (this.tabsArray.length) {
		var strId = this.getInitialActiveTabId();
		if (-1 != strId) {
			//this.changeTab(strId);
		}
	}

	// If windowOnLoad is set, run it
	if (this.config.windowOnLoad != null)
	{
		this.config.windowOnLoad();
	}

	// If tab bar is not disabled
	if (true != this.noTabBar) {
		// Make tabs visible
		this.addEventListener('loadThemeEnd', function() {
			this.config.tabBar.style.display = 'block';
		});
	}
}

/**
 * Configures the widget. Gets called from init and reconfigure methods of
 * superclass.
 *
 * @private
 * @param {object} objArgs User configuration
 */
Zapatec.Tabs.prototype.configure = function(objArgs) {
	// Define config options
	this.defineConfigOption('tabBar', null);
	this.defineConfigOption('tabs', null);
	this.defineConfigOption('onInit', null);
	this.defineConfigOption('onTabChange', null);
	this.defineConfigOption('onBeforeTabChange', null);

	// If location hash is to be parsed on page initialization
	this.defineConfigOption('ignoreUrl', false);
	// If browser url needs to be changed on every tab change
	this.defineConfigOption('changeUrl', true);

	this.defineConfigOption('tab2tab', false);
	this.defineConfigOption('scrollMultiple', null);

	//should we show content in IFRAME Pane or just simple div Pane
	this.defineConfigOption('iframeContent', null);
	this.defineConfigOption('tabType', 'div');

	this.defineConfigOption('windowOnLoad', null);

	this.defineConfigOption('scrolls', false);
	this.defineConfigOption('noMoreTabsLeft', false);
	this.defineConfigOption('lastIndexLeft', 0);
	this.defineConfigOption('noMoreTabsRight', true);
	this.defineConfigOption('lastIndexRight', null);

	this.defineConfigOption('showEffect', null);
	this.defineConfigOption('showEffectSpeed', null);

	// Determine if tabs are to be changed on mouse over
	this.defineConfigOption('mouseOverChangesTab', false);

	// Determine if tabs are to be re-fetched every time they are activated
	this.defineConfigOption('refreshOnTabChange', false);

	// Determine tab content overflow style attribute that is default for every tab
	this.defineConfigOption('overflow', null);

	// Determine if tab content is to be fetched on tab creation
	this.defineConfigOption('shouldLoadOnInit', false);

	// Specifies if tab can be closed using an x button that is visible
	this.defineConfigOption('closable', false);
	// Specifies what happens then the x button is clicked
	this.defineConfigOption('closeAction', 'close');

	// Determine tab to activate upon initialization
	this.defineConfigOption('activeTabId', null);

	this.defineConfigOption('langId', Zapatec.Tabs.id);
	this.defineConfigOption('lang', "eng");

	// Determine scroll type
	if (this.config.scroll == null)
	{
		this.config.scrollMultiple = false;
	}
	else if (this.config.scrollMultiple != true && this.config.scrollMultiple != false)
	{
		this.config.scrollMultiple = false;
	}

	// Call parent method
	Zapatec.Tabs.SUPERclass.configure.call(this, objArgs);

	// If tab bar is not disabled
	if (true != this.noTabBar) {
		this.config.tabBar = Zapatec.Widget.getElementById(this.config.tabBar);
		if (!this.config.tabBar) {
			this.initLang();
			Zapatec.Log({description: this.getMessage("unknownTabBarError")});
			return;
		}
	}

	if ("string" == typeof this.config.tabs) {
		this.config.tabs = Zapatec.Widget.getElementById(this.config.tabs);
	}

	if (!this.config.tabs) {
		this.initLang();
		Zapatec.Log({description: this.getMessage("unknownTabsError")});
		return;
	}

	// Check tabType
	if (typeof this.config.tabType == "string") {
		this.config.tabType = this.config.tabType.toLowerCase();
	}
	if (this.config.tabType != "div" && objArgs.tabType != "iframe")
	{
		this.config.tabType = "div";
	}

	if (true == this.config.iframeContent) {
		this.config.tabType = "iframe";
	}
};

/**
 * Initializes the tab bar based on tabBar config property.
 */
Zapatec.Tabs.prototype.initTabBar = function() {
	// If tab bar is disabled
	if (true == this.noTabBar) {
		return;
	}

	// Apply theme
	Zapatec.Utils.addClass(this.config.tabBar, this.getClassName({
		prefix: 'zpTabs'
	}));

	// Determine width of content of tab bar
	var _tabBarContentWidth = 0;
	var items = this.config.tabBar.childNodes;
	var tmp = '';

	for (var i = 0; i < items.length; i++) {
		if (items[i].nodeType != 1) {
			continue;
		}

		// Must "cast" this to an absolutely positioned element for IE to get the correct width
		tmp = items[i].style.position;
		items[i].style.position = 'absolute';

		// Store real width in element for future use
		// Opera reports the widths of elements outside the content tab to have width of -1.
		// For this reason, hide elements you've recorded to allow other elements to come into viewport
		// Get the display type for all tabs, default to 'inline'
		items[i].originalDisplayType = items[i].style.display != '' ? items[i].style.display : 'block';
		_tabBarContentWidth += items[i].offsetWidth;
		items[i].realWidth = items[i].offsetWidth;
		items[i].style.display = 'none';

		// "Cast" back to original positioning
		items[i].style.position = tmp;

		// Capture tabs index in the tab bar
		items[i].arrayPosition = i;
	}

	// Restore display to all tabs
	for (var i = 0; i < items.length; i++)
	{
		if (items[i].nodeType != 1) {
			continue;
		}

		items[i].style.display = items[i].originalDisplayType;
	}

	// If the content width of the tab bar exceeds the current width of the tab bar...
	var _tabBarWidth = this.config.tabBar.offsetWidth;
	if (_tabBarContentWidth > _tabBarWidth)
	{
		this.config.scrolls = true;

		// Find the first child node within the tab bar, which extends beyond tab bar's border
		var tmp = 0;
		var i = 0;
		while (tmp < _tabBarWidth) tmp += items[i++].realWidth;

		// Scroll page at a time
		//if(this.config.scrollMultiple)
		//{
		i--;
		//}

		// Hide all elements of tab bar, which extend beyond tab bar's border
		for (i; i < items.length; i++)
		{
			items[i].style.display = 'none';
		}

		// Display scrollies for tabs
		var _leftScrolly = Zapatec.Utils.createElement('div');
		_leftScrolly.innerHTML = '&lt;';
		var _rightScrolly = Zapatec.Utils.createElement('div');
		_rightScrolly.innerHTML = '&gt;';

		// Attach scrolling functions
		var self = this;
		_rightScrolly.onclick = (this.config.scrollMultiple) ? function () {
			self.scrollTabsLeft(true)
		} : function () {
			self.scrollOneTabLeft(true)
		};
		_leftScrolly.onclick = (this.config.scrollMultiple) ? function () {
			self.scrollTabsRight(true)
		} : function () {
			self.scrollOneTabRight(true)
		};

		// Make mouseover effects for the scrolly buttons
		var mouseoverFunc = function ()
		{
			this.style.color = 'black';
		}
		_leftScrolly.onmouseover = mouseoverFunc;
		_rightScrolly.onmouseover = mouseoverFunc;

		var mouseoutFunc = function ()
		{
			this.style.color = '#aaa';
		}
		_leftScrolly.onmouseout = mouseoutFunc;
		_rightScrolly.onmouseout = mouseoutFunc;

		var _scrollyContainer = Zapatec.Utils.createElement('div');
		_scrollyContainer.className = 'zpTabsScrolly';
		_scrollyContainer.appendChild(_leftScrolly);
		_scrollyContainer.appendChild(_rightScrolly);

		if (this.config.scrollMultiple)
		{
			this.config.tabBar.parentNode.insertBefore(_scrollyContainer,
							this.config.tabBar.nextSibling);
		}
	}

}

/**
 * Adds a tab link to the tab bar
 *
 * @param {object} objTab object tab.
 */
Zapatec.Tabs.prototype.addToTabBar = function(objTab) {
	if (true == this.noTabBar) {
		return;
	}

	var configIndex = objTab.config.index;
	var insertBefore = null;

	if (-1 != configIndex) {
		// Insert tab link at the specified index
		var actualIndex = objTab.index;

		var children = this.config.tabBar.childNodes;
		var childrenCount = children.length;

		// If we are inserting the tab at some position
		if (0 < childrenCount && actualIndex < childrenCount) {
			insertBefore = children[actualIndex];
		}
	}

	if (!insertBefore) {
		// Append tab link on the end of the tab bar
		this.config.tabBar.appendChild(objTab.linkNode);
	}
	else {
		this.config.tabBar.insertBefore(objTab.linkNode, insertBefore);
	}

	// If tab is set to be hidden
	if (!objTab.config.visible) {
		objTab.linkNode.style.display = 'none';
	}
}

/**
 * Attaches a tab to the tabs at a specified index
 *
 * @private
 * @param {object} objTab object tab.
 */
Zapatec.Tabs.prototype.attachTab = function(objTab) {

	var index = objTab.config.index;

	if (this.tabsArray.length <= index) {
		index = -1;
	}

	if (-1 == index) {
		objTab.index = this.tabsArray.length;
	}
	else {
		objTab.index = index;
		// Shift tabsArray
		for (var i = this.tabsArray.length - 1; index <= i; --i) {
			var oldTab = this.tabsArray[i];
			oldTab.index = oldTab.index + 1;
			this.tabsArray[i + 1] = oldTab;
		}

		if (index <= this.currentIndex) {
			++this.currentIndex;
		}
	}
	// Attach this Tab to Tabs object
	this.tabsArray[objTab.index] = objTab;
	this.tabs[objTab.id] = objTab;

	// Set tab link hash
	var linkHash = this.config.tabs.id + "=" + objTab.id;
	objTab.setLinkHash(linkHash);

	var self = this;
	objTab.addEventListener("activateTab", function() {
		//self.changeTab(objTab.id);
	});
}

/**
 * Scrolls tabs to the left
 *
 * @private
 * @param {boolean} setTab Whether the function should set the selected tab
 * on its own.
 */
Zapatec.Tabs.prototype.scrollOneTabLeft = function(setTab) {
	var tabBar = this.config.tabBar;

	// Get tabs
	var items = tabBar.childNodes;

	// Find first visible tab
	var i = 0;
	for (i; i < items.length; i++)
	{
		if (items[i].style.display != 'none')
		{
			break;
		}
	}

	// Find first hidden tab
	var j = i;
	for (j; j < items.length; j++)
	{
		if (items[j].style.display == 'none')
		{
			break;
		}
	}

	if (j >= items.length)
	{
		return;
	}

	// Hide first visible tab, show first hidden tab
	items[i].style.display = 'none';

	// If there aren't any hidden tabs left, don't try to display any
	if (j < items.length)
	{
		items[j].style.display = items[j].originalDisplayType;
	}
}

/**
 * Scrolls tabs to the left
 *
 * @private
 * @param {boolean} setTab Whether the function should set the selected tab
 * on its own.
 */
Zapatec.Tabs.prototype.scrollOneTabRight = function(setTab) {
	var tabBar = this.config.tabBar;

	// Get tabs
	var items = tabBar.childNodes;

	// Find first visible tab
	var i = 0;
	for (i; i < items.length; i++)
	{
		if (items[i].style.display != 'none')
		{
			break;
		}
	}

	// If i==0, you're at the beginning of the tabs, just exit
	if (i == 0)
	{
		return;
	}

	// Find first hidden tab
	var j = i;
	for (j; j < items.length; j++)
	{
		if (items[j].style.display == 'none')
		{
			break;
		}
	}

	// Hide last visible tab, show first hidden tab previous to the first visible tab
	items[j - 1].style.display = 'none';
	items[i - 1].style.display = items[i - 1].originalDisplayType;
}

/**
 * Scrolls tabs to the left
 *
 * @private
 * @param {boolean} setTab Whether the function should set the selected tab
 * on its own.
 */
Zapatec.Tabs.prototype.scrollTabsLeft = function(setTab) {
	// Quit if you're at the end
	if (this.config.noMoreTabsLeft)
	{
		return;
	}

	// Mark that there are now (or will be) hidden tabs on the left
	this.config.noMoreTabsRight = false;

	// Check arguments
	if (!this.config.tabBar)
	{
		return;
	}
	var tabBar = this.config.tabBar;

	// Content width of the tab bar
	var contentWidth = parseInt(tabBar.style.width);

	// Run through tabs hiding those that are currently visible
	var items = tabBar.childNodes;
	var i = this.config.lastIndexLeft - 1;
	while (++i < items.length)
	{
		if (items[i].style.display != 'none')
		{
			items[i].style.display = 'none';
		}
		else
		{
			this.config.lastIndexLeft = i;
			break;
		}
	}

	// Now, make elements visible until you run out of room, or until there aren't any left
	var contentWidth = 0;
	var tabBarWidth = parseInt(tabBar.style.width);
	for (i = this.config.lastIndexLeft; i < items.length; i++)
	{
		items[i].style.display = items[i].originalDisplayType;
		contentWidth += items[i].realWidth;

		// If you've exceeded viewable area, retract
		if (contentWidth > tabBarWidth)
		{
			items[i].style.display = 'none';
			this.config.lastIndexRight = i - 1;

			// Make first visible tab the current tab
			if (setTab)
			{
				//this.changeTab(items[this.config.lastIndexLeft].name);
			}

			return;
		}
	}

	// Adjust lastIndexRight for last increment performed in for loop
	this.config.lastIndexRight = i - 1;

	// Make first visible tab the current tab
	if (setTab)
	{
		//this.changeTab(items[this.config.lastIndexLeft].name);
	}

	this.config.noMoreTabsLeft = true;
}

/**
 * Scrolls tabs to the right
 *
 * @private
 * @param {boolean} setTab Whether the function should set the selected tab
 * on its own.
 */
Zapatec.Tabs.prototype.scrollTabsRight = function(setTab) {
	// Quit if you're at the end
	if (this.config.noMoreTabsRight)
	{
		return;
	}

	// Mark that there are now (or will be) hidden tabs on the right
	this.config.noMoreTabsLeft = false;

	// Check arguments
	if (!this.config.tabBar)
	{
		return;
	}
	var tabBar = this.config.tabBar;

	// Content width of the tab bar
	var contentWidth = parseInt(tabBar.style.width);

	// Run through tabs hiding those that are currently visible
	// Need some sort of pagination here
	var items = tabBar.childNodes;
	var i = this.config.lastIndexRight + 1;
	while (--i >= 0)
	{
		if (items[i].style.display != 'none')
		{
			items[i].style.display = 'none';
		}
		else
		{
			this.config.lastIndexRight = i;
			break;
		}
	}

	// Now, make elements visible until you run out of room, or until there aren't any left
	var contentWidth = 0;
	var tabBarWidth = parseInt(tabBar.style.width);
	for (i = this.config.lastIndexRight; i >= 0; i--)
	{
		items[i].style.display = items[i].originalDisplayType;
		contentWidth += items[i].realWidth;

		// If you've exceeded viewable area, retract
		if (contentWidth > tabBarWidth)
		{
			items[i].style.display = 'none';
			this.config.lastIndexLeft = i + 1;

			// Make first visible tab the current tab
			if (setTab)
			{
				//this.changeTab(items[this.config.lastIndexRight].name);
			}

			return;
		}
	}

	// Adjust lastIndexLeft for last decrement performed in for loop
	this.config.lastIndexLeft = i + 1;

	// Make first visible tab the current tab
	if (setTab)
	{
		//this.changeTab(items[this.config.lastIndexRight].name);
	}

	this.config.noMoreTabsRight = true;
}

/**
 * Loads data from the JSON source.
 *
 * @private
 * Following format is recognized:
 * \code
 * {
 *   tabs: [
 *     {
 *       id: [string, optional] id of the tab,
 *       innerHTML: [string] label,
 *       accessKey: [string, optional] access key,
 *       title: [string] title,
 *       url: [string] URL of the content,
 *		 tabType: [string] "div" or "iframe" for the content pane
 *     },
 *     ...
 *   ]
 * }
 * \endcode
 *
 * @param {object} objSource JSON object.
 */
Zapatec.Tabs.prototype.loadDataJson = function(objSource) {
	// Check arguments
	if ((true != this.noTabBar && !this.config.tabBar) || !this.config.tabs) {
		return;
	}
	if (!objSource || !objSource.tabs || !objSource.tabs.length) {
		return;
	}
	// Parse source
	var iLen = objSource.tabs.length;
	for (var iTab = 0; iTab < iLen; iTab++) {
		var objTabDef = objSource.tabs[iTab];

		this.addTab(objTabDef);
	}
};

/**
 * Create a new tab instance
 *
 * @param {object} objArgs tab configuration
 */
Zapatec.Tabs.prototype.newTab = function(objArgs) {
	var objTab = new Zapatec.Tab(objArgs);
	return objTab;
}

/**
 * Loads data from the HTML source.
 *
 * @private
 * Following format is recognized:
 * \code
 * <div id="tabs">
 *   <div id="tab-begin">
 *     <label>The first tab</label>
 *     ... the tab contents here ...
 *   </div>
 *   <div id="tab-second">
 *     <label>The second tab</label>
 *     ... the tab contents here ...
 *   </div>
 *   ...
 * </div>
 * \endcode
 *
 * @param {object} objSource HTMLElement object.
 */
Zapatec.Tabs.prototype.loadDataHtml = function(objSource) {
	// Check arguments
	if ((true != this.noTabBar && !this.config.tabBar) || !this.config.tabs) {
		return;
	}
	if (!objSource) {
		objSource = this.config.tabs;
	}

	var childs = [];
	for (var ii = 0; ii < objSource.childNodes.length; ii++) {
		childs.push(objSource.childNodes[ii]);
	}

	// Parse source
	for (var iChild = 0; iChild < childs.length; iChild++) {
		var objChild = childs[iChild];

		if (objChild.nodeType == 1) { // ELEMENT_NODE
			// Get label
			var objLabel = Zapatec.Utils.getFirstChild(objChild, 'label');
			if (!objLabel) {
				continue;
			}

			// Remove label
			objLabel.parentNode.removeChild(objLabel);
			// Remove child
			objChild.parentNode.removeChild(objChild);

			var objArgs = {
				tabParent: this.config.tabs,
				id: objChild.getAttribute('id'),
				linkInnerHTML: objLabel.innerHTML,
				accessKey: objLabel.getAttribute('accesskey'),
				title: objLabel.getAttribute('title'),
				content: objChild,
				tabType: objChild.getAttribute('tabType'),
				url: objChild.getAttribute('url'),
				className: objChild.className,
				overflow: objChild.style.overflow,
				visible: objLabel.style.display != "none",
				refreshOnTabChange: objChild.getAttribute('refreshOnTabChange'),
				closable: objLabel.getAttribute('closable'),
				closeAction: objLabel.getAttribute('closeAction')
			};

			var objTab = this.addTab(objArgs);

			// Return id of the tab trough container element
			if (objTab.id) {
				objTab.container.getContainer().setAttribute('id', objTab.id);
				// Remove id from div to avoid coliding with tab container
				objChild.removeAttribute('id');
			}
		}
	}

	// Mark that we have initialized tabs by parsing html
	this.isLoadedHtml = true;
};

/**
 * Adds a new tab.
 *
 * @private
 * Following format is recognized:
 * \code
 *  {
 *    id: [string, optional] id of the tab,
 *    innerHTML: [string] label,
 *    accessKey: [string, optional] access key,
 *    title: [string] title,
 *    url: [string] URL of the content,
 *	  tabType: [string] "div" or "iframe" for the content pane
 *    index: [number, optional] index to make insert the new tab at
 * }
 * \endcode
 *
 * @param {object} objTabDef JSON object.
 */
Zapatec.Tabs.prototype.addTab = function(objTabDef) {
	// Check arguments
	if ((true != this.noTabBar && !this.config.tabBar) || !this.config.tabs) {
		return;
	}
	if (!objTabDef) {
		return;
	}
	// Parse source
	// If no tabType is set to this tab
	if (!objTabDef.tabType) {
		// Use config option from Tabs
		objTabDef.tabType = this.config.tabType;
	}
	objTabDef.tabParent = this.config.tabs;
	objTabDef.changeUrl = this.config.changeUrl;
	if (!objTabDef.lang) {
		// Use config option from Tabs
		objTabDef.lang = this.config.lang;
	}
	if (!objTabDef.mouseOverChangesTab) {
		// Use config option from Tabs
		objTabDef.mouseOverChangesTab = this.config.mouseOverChangesTab;
	}
	if (!objTabDef.refreshOnTabChange) {
		// Use config option from Tabs
		objTabDef.refreshOnTabChange = this.config.refreshOnTabChange;
	}
	if (!objTabDef.shouldLoadOnInit) {
		// Use config option from Tabs
		objTabDef.shouldLoadOnInit = this.config.shouldLoadOnInit;
	}
	if (!objTabDef.overflow && '' != objTabDef.overflow) {
		// Use config option from Tabs
		objTabDef.overflow = this.config.overflow;
	}
	if (!objTabDef.closable) {
		// Use config option from Tabs
		objTabDef.closable = this.config.closable;
	}
	if (!objTabDef.closeAction) {
		// Use config option from Tabs
		objTabDef.closeAction = this.config.closeAction;
	}
	objTabDef.theme = this.config.theme;
	objTabDef.themePath = this.config.themePath;
	objTabDef.tabsId = this.id;
	// Create tab
	var objTab = this.newTab(objTabDef);

	// Return id of the tab through objTabDef
	if (objTab.id) {
		objTabDef.id = objTab.id;
	}

	var index = objTabDef.index;

	this.attachTab(objTab);

	this.addToTabBar(objTab);

	return objTab;
};

/**
 * Remove a tab from tabs.
 *
 * @private
 *
 * @param {string} strTabId Id of tab to remove
 */
Zapatec.Tabs.prototype.removeTab = function(strTabId) {
	var objTab = this.getTab(strTabId);

	if (!objTab) {
		return;
	}

	// Determine if we are removing the currently active tab
	var isRemovingCurrent = objTab.index == this.currentIndex;

	var newCurrentIndex;
	if (objTab.index <= this.currentIndex) {
		newCurrentIndex = this.currentIndex - 1;
		if (newCurrentIndex < 0) {
			newCurrentIndex = 0;
		}
	}

	// Remove tab link from tab bar
	this.config.tabBar.removeChild(objTab.linkNode);

	this.tabs[strTabId] = null;

	// Create a new tabsArray without the tab being removed
	var newTabsArray = [];

	var toIndex = 0;

	for (var fromIndex = 0; fromIndex < this.tabsArray.length;
	     ++fromIndex) {
		var fromTab = this.tabsArray[fromIndex];
		if (objTab.index != fromTab.index) {
			fromTab.index = toIndex;
			newTabsArray[toIndex] = fromTab;
			++toIndex;
		}
	}

	this.tabsArray = newTabsArray;

	// Remove content pane
	var container = objTab.container.getContainer();
	objTab.container.getContainer().parentNode.removeChild(objTab.container.getContainer());

	// if we are removing the currently active tab
	if (isRemovingCurrent) {
		this.currentIndex = -1;
		if (newCurrentIndex < this.tabsArray.length) {
			// Change tab
			//this.changeTab(this.tabsArray[newCurrentIndex].id);
		}
	}

	if (newCurrentIndex) {
		// Set new currently active tab index
		this.currentIndex = newCurrentIndex;
	}
}

/**
 * Display a new tab. If onBeforeTabChange() returns false, the operation is
 * cancelled.
 *
 * @param {string} strNewTabId id of the new tab.
 */
Zapatec.Tabs.prototype.changeTab = function(strNewTabId) {
	var strCurrTabId = null;
	var objTab = null;
	if (this.tabsArray[this.currentIndex]) {
		strCurrTabId = this.tabsArray[this.currentIndex].id;
		objTab = this.tabsArray[this.currentIndex];
	}
	if (strCurrTabId != strNewTabId) {
		// Check if callback function allows to change tab
		var boolChangeTab = true;
		if (typeof this.config.onBeforeTabChange == 'function') {
			boolChangeTab = this.config.onBeforeTabChange({
				oldTabId: strCurrTabId,
				newTabId: strNewTabId
			});
		}
		if (!boolChangeTab) {
			// Return focus back
			if (objTab && objTab.linkNode.focus) {
				// Need to focus on tab first because FF 1.5 seems to have separate
				// focus for links
				objTab.linkNode.focus();
				// Focus on content (in separate thread to let it focus on tab first)
				setTimeout(function() {
					if (objTab.focusOn && objTab.focusOn.focus) {
						objTab.focusOn.focus();
					}
				}, 0);
			}
			return;
		}

		if (this.config.scrolls)
		{
			// If you're moving out of the visible tabs, scroll accordingly
			var _newTab = this.tabsArray[this.tabs[strNewTabId].index].linkNode;
			if (this.tabsArray[this.currentIndex])
			{
				var _curTab = this.tabsArray[this.currentIndex].linkNode;
			}
			else
			{
				var _curTab = this.tabsArray[0].linkNode;
			}
			if (_curTab.arrayPosition < _newTab.arrayPosition)
			{
				if (this.config.scrollMultiple)
				{
					while (_newTab.style.display == 'none')
					{
						this.scrollTabsLeft(false);
					}
				}
				else
				{
					while (_newTab.style.display == 'none')
					{
						this.scrollOneTabLeft(false);
					}
				}
			}
			else if (_curTab.arrayPosition > _newTab.arrayPosition)
			{
				if (this.config.scrollMultiple)
				{
					while (_newTab.style.display == 'none')
					{
						this.scrollTabsRight(false);
					}
				}
				else
				{
					while (_newTab.style.display == 'none')
					{
						this.scrollOneTabRight(false);
					}
				}
			}
		}

		// Change tab
		if (objTab) {
			// Hide old tab
			objTab.container.hide();
			Zapatec.Utils.removeClass(objTab.linkNode, 'zpTabsActive');
		}
		objTab = this.tabs[strNewTabId];
		// Show new tab
		if (!this.config.showEffect) {
			objTab.container.show();
		}
		else {
			Zapatec.Effects.init(objTab.container.getContainer(),
							true, this.config.showEffectSpeed, this.config.showEffect);
		}

		// Refresh iframe element?
		if (this.config.refreshOnTabChange)
		{
			var iframes = objTab.container.getContainer().getElementsByTagName('iframe');

			for (var i = 0; i < iframes.length; i++)
			{
				window.parent.frames[iframes[i].name].location.reload(true)
			}
		}

		if (!objTab.config.visible) {
			objTab.setVisible(true);
		}

		Zapatec.Utils.addClass(objTab.linkNode, 'zpTabsActive');
		this.currentIndex = objTab.index;

		// Reload tab if needed and call onTabChange
		this.refreshTab(objTab, strCurrTabId, strNewTabId);
	}
};

/**
 * Reloads tab content if needed and calls onTabChange event after that.
 *
 * @private
 * @param {object} objTab tab to refresh
 * @param {string} strCurrTabId old tab id
 * @param {string} strNewTabId new tab id
 */
Zapatec.Tabs.prototype.refreshTab = function(objTab, strCurrTabId, strNewTabId) {
	var url = null;
	if (objTab.config.contentType == "html/url") {
		url = objTab.config.content;
	}
	else {
		url = objTab.config.url;
	}

	// If onTabChange handler is specified
	if (typeof this.config.onTabChange == 'function') {
		var self = this;
		// Mark that onTabChange is to be called
		objTab.pendingOnTabChange = function() {
			// Call onTabChange event handler
			self.config.onTabChange({
				oldTabId: strCurrTabId,
				newTabId: strNewTabId
			});
		}
	}

	// If tab is configured to load content from external source and
	// tab is still empty or it is configured to refresh on every tab change
	if (url) {
		var isIframeDiffSrc = objTab.config.tabType == 'iframe' &&
		                      objTab.lastUrlSet != url;
		// If tab is a div and it's an empty one
		var isEmptyDiv = objTab.config.tabType == 'div' &&
		                 !objTab.container.getContainer().childNodes.length;
		// If Internet Explorer
		if (Zapatec.is_ie) {
			// If tab is a div and it only contains a WCH
			var tabContents = objTab.container.getContainer().childNodes;
			if (objTab.config.tabType == 'div' &&
			    1 == tabContents.length &&
			    tabContents[0].id.match(/wch/gi)) {
				// Mark tab as empty
				isEmptyDiv = true;
			}
		}
		if (isEmptyDiv || isIframeDiffSrc || objTab.config.refreshOnTabChange) {
			// Load remote url inside tab
			objTab.setPaneContent(url, 'html/url');
			return;
		}
	}
	else {
		if (!url) {
			// Update tab content
			objTab.setPaneContent();
		}
	}

	if (objTab.pendingOnTabChange) {
		// Call onTabChange event handler
		objTab.pendingOnTabChange(self);
		objTab.pendingOnTabChange = null;
	}
}

/**
 * Gets a tab with a given id
 *
 * @param {string} tabId id of the tab to get
 */
Zapatec.Tabs.prototype.getTab = function(tabId) {
	var objTab = this.tabs[tabId];
	return objTab;
};

/**
 * Gets a tab with a given index
 *
 * @param {number} tabIndex index of the tab to get
 */
Zapatec.Tabs.prototype.getTabByIndex = function(tabIndex) {
	var objTab = this.tabsArray[tabIndex];
	return objTab;
};

/**
 * Get next visible/hidden tab.
 *
 * @param {boolean} isVisible type of tab to get
 * @param {number} tabIndex index of the pivot tab
 */
Zapatec.Tabs.prototype.getNextTab = function(isVisible, tabIndex) {
	var nextTabIndex = tabIndex + 1;
	while (nextTabIndex < this.tabsArray.length) {
		var objTab = this.tabsArray[nextTabIndex];
		if (objTab.config.visible == isVisible) {
			return objTab;
		}
		++nextTabIndex;
	}
	return null;
}

/**
 * Get previous visible/hidden tab.
 *
 * @param {boolean} isVisible type of tab to get
 * @param {number} tabIndex index of the pivot tab
 */
Zapatec.Tabs.prototype.getPreviousTab = function(isVisible, tabIndex) {
	var previousTabIndex = tabIndex - 1;
	while (0 <= previousTabIndex) {
		var objTab = this.tabsArray[previousTabIndex];
		if (objTab.config.visible == isVisible) {
			return objTab;
		}
		--previousTabIndex;
	}
	return null;
}

/**
 * Moves to the next tab.
 */
Zapatec.Tabs.prototype.nextTab = function() {

	var nextTab = this.getNextTab(true, this.currentIndex);
	if (nextTab) {
		//this.changeTab(nextTab.id);
	}
	else {
		// Get first visible tab
		nextTab = this.getNextTab(true, -1);
		if (nextTab) {
			//this.changeTab(nextTab.id);
		}
	}
};

/**
 * Moves to the previous tab.
 */
Zapatec.Tabs.prototype.prevTab = function() {
	// Get first visible tab before the current one
	var previousTab = this.getPreviousTab(true, this.currentIndex);
	if (previousTab) {
		//this.changeTab(previousTab.id);
	}
	else {
		// Get last visible tab
		previousTab = this.getPreviousTab(true, this.tabsArray.length);
		if (previousTab) {
			//this.changeTab(previousTab.id);
		}

	}
};

/**
 * Moves to the first tab.
 */
Zapatec.Tabs.prototype.firstTab = function() {
	//this.changeTab(this.tabsArray[0].id);
};

/**
 * Moves to the last tab.
 */
Zapatec.Tabs.prototype.lastTab = function() {
	//this.changeTab(this.tabsArray[this.tabsArray.length - 1].id);
};

/**
 * Indicates if current tab is first.
 *
 * @return {boolean} true if we are at the first tab, false otherwise.
 */
Zapatec.Tabs.prototype.isFirstTab = function() {
	return this.currentIndex == 0;
};

/**
 * Indicates if current tab is last.
 *
 * @return {boolean} true if we are at the last tab, false otherwise.
 */
Zapatec.Tabs.prototype.isLastTab = function() {
	return this.currentIndex == this.tabsArray.length - 1;
};

/**
 * Gets the tab that is to become active after Tabs initialization
 *
 * @private
 */
Zapatec.Tabs.prototype.getInitialActiveTabId = function() {
	var strId = null;
	if (this.config.activeTabId) {
		strId = this.config.activeTabId;
	}
	else {
		// Default to first tab
		strId = this.tabsArray[0].id;
	}

	// If url shows tab to be made active
	if (!this.config.ignoreUrl) {
		// Parse url param for a tab with a matching id
		var str = this.config.tabs.id + "=";
		var url = document.URL;
		var pos = url.lastIndexOf(str);
		// If such param exists
		if (-1 != pos) {
			pos += str.length;
			str = url.substring(pos);
			// Tab id ends where next param begins
			var tabId = str.split("&")[0];
			// If a tab exists for tabId stated in the URL
			if (this.tabs[tabId]) {
				strId = tabId;
			}
		}
	}

	return strId;
};
