// $Id: zptabs-accordion.js 7376 2007-06-08 11:59:17Z vkulov $
/**
 * @fileoverview Accordion style tabs extension. Extends Tabs class (zptabs.js)
 * adding accordion style tabs.
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
 * Zapatec.AccordionTab constructor. Creates a new accordion tab object with
 * given parameters. Configuration options are the same as in Zapatec.Tab
 *
 * @constructor
 * @extends Zapatec.Widget
 * @param {object} objArgs Tab configuration
 */
Zapatec.AccordionTab = function(objArgs) {
	if (arguments.length == 0) {
		objArgs = {};
	}

	// Call constructor of superclass
	Zapatec.AccordionTab.SUPERconstructor.call(this, objArgs);
};

/**
 * Unique static id of the widget class. Gives ability for Zapatec#inherit to
 * determine and store path to this file correctly when it is included using
 * Zapatec#include. When this file is included using Zapatec#include or path
 * to this file is gotten using Zapatec#getPath, this value must be specified
 * as script id.
 * @private
 */
Zapatec.AccordionTab.id = 'Zapatec.AccordionTab';

// Inherit Tab
Zapatec.inherit(Zapatec.AccordionTab, Zapatec.Tab);

/**
 * Creates elements needed for the new tab
 */
Zapatec.AccordionTab.prototype.createTab = function()
{
	this.config.closable = false;

	var tabParent = this.config.tabParent;

	// Create a container for this tab
	this.tabContainer = document.createElement('div');

	// Add tab container to tabs
	tabParent.appendChild(this.tabContainer);
	this.config.tabParent = this.tabContainer;

	// Call parent init
	Zapatec.AccordionTab.SUPERclass.createTab.call(this);

	// Show content pane
	this.container.getContainer().style.display = 'block';
	this.container.getContainer().style.width = '';

	this.tabContainer.tabId = this.id;

	// Keyboard navigation support
	this.linkNode.tabIndex = Zapatec.AccordionTab.tabIndex;

	if (this.config.collapseOnClick) {
		// Reset onfocus handler in this mode
		this.linkNode.onfocus = null;
	}

	if (!this.tab2tab)
	{
		// Next tabIndex is reserved for tab content
		Zapatec.AccordionTab.tabIndex += 2;
	}

	// Add title bar to tab
	this.chooser = Zapatec.Utils.createElement('div');
	this.chooser.className = 'tabChooser';
	this.chooser.onclick = this.onActivate;
	this.tabContainer.insertBefore(this.chooser,
					this.tabContainer.childNodes[0]);

	this.chooser.appendChild(this.linkNode);

	if (this.config.tabType != "iframe") {
		// Create WCH
		this.wch = Zapatec.Utils.createWCH(this.container.getContainer());
		// Put WCH under container
		if (this.wch) {
			this.wch.style.zIndex = -1;
		}
	}
};

/**
 * Configures the widget. Gets called from init and reconfigure methods of
 * superclass.
 *
 * @private
 * @param {object} objArgs User configuration
 */
Zapatec.AccordionTab.prototype.configure = function(objArgs) {
	// Define config options
	// Id of the tab
	this.defineConfigOption('collapseOnClick', null);
	this.defineConfigOption('visibleHeight', -1);
	// Call parent method
	Zapatec.AccordionTab.SUPERclass.configure.call(this, objArgs);

	if (this.config.content && 1 == this.config.content.nodeType) {
		// Get tab height as specified in html
		this.config.visibleHeight = parseInt(this.config.content.style.height);
		this.config.content.style.height = "";
  }
}

/**
 * Determines child node of the container which gets focus first.
 * Needed for keyboard navigation.
 * @private
 */
Zapatec.AccordionTab.prototype.getFocusOn = function ()
{
	// Remove old value
	this.focusOn = null;

	// Check keyboard navigation type
	if (this.tab2tab)
	{
		return;
	}

	// Put it in separate process to speed up initialization
	var self = this;

	setTimeout(function()
	{
		// Flag to determine lower tabIndex
		var iTabIndex = 0;

		// Gets element with lower tabIndex
		function parse(objNode)
		{
			var objChild = objNode.firstChild;

			while (objChild)
			{
				if (objChild.nodeType == 1)
				{
					// ELEMENT_NODE
					var strTag = objChild.tagName.toLowerCase();

					if (strTag == 'a' || strTag == 'input' || strTag == 'select' ||
					    strTag == 'textarea' || strTag == 'button')	// Element may obtain focus
					{
						if (!self.focusOn)
						{
							self.focusOn = objChild;
						}
						else if (objChild.tabIndex && objChild.tabIndex > 0 &&
						         (!iTabIndex || iTabIndex > objChild.tabIndex))
						{
							self.focusOn = objChild;
							iTabIndex = objChild.tabIndex;
						}

						if (!objChild.tabIndex)
						{
							objChild.tabIndex = self.linkNode.tabIndex + 1;
						}
					}

					parse(objChild);
				}

				objChild = objChild.nextSibling;
			}
		};

		// Parse tab content
		parse(self.container.getContainer());
	}, 0);
};


/**
 * Zapatec.AccordionTabs constructor. Creates a new accordion tabs object
 * with given parameters.
 *
 * @constructor
 * @extends Zapatec.Widget
 * @param {object} objArgs Tab configuration
 *
 * See Zapatec.Tabs for a list of recognized properties of the config object
 */
Zapatec.AccordionTabs = function(objArgs) {
	Zapatec.AccordionTabs.SUPERconstructor.call(this, objArgs);
};

/**
 * Unique static id of the widget class. Gives ability for Zapatec#inherit to
 * determine and store path to this file correctly when it is included using
 * Zapatec#include. When this file is included using Zapatec#include or path
 * to this file is gotten using Zapatec#getPath, this value must be specified
 * as script id.
 * @private
 */
Zapatec.AccordionTabs.id = 'Zapatec.AccordionTabs';

// Inherit Tabs
Zapatec.inherit(Zapatec.AccordionTabs, Zapatec.Tabs);

/**
 * Configures the widget. Gets called from init and reconfigure methods of
 * superclass.
 *
 * @private
 * @param {object} objArgs User configuration
 */
Zapatec.AccordionTabs.prototype.configure = function(objArgs) {
	// Define config options
	// If tabs are to collapse on click when they are active
	this.defineConfigOption('collapseOnClick', false);
	// If page is to scroll with tabs as they slide.
	// This option is true by default for IE6 and Mozilla
	this.defineConfigOption('scrollPageOnSlide',
					(Zapatec.is_ie && !Zapatec.is_ie7) || Zapatec.is_gecko);
	// Call parent method
	Zapatec.AccordionTabs.SUPERclass.configure.call(this, objArgs);
}

/**
 * Initializes object.
 *
 * @param {object} objArgs User configuration
 */
Zapatec.AccordionTabs.prototype.init = function(objArgs, i)
{
	// Reference to this
	var self = this;

	// Patch onInit handler
	var funcOnInit = objArgs.onInit;

	objArgs.onInit = function()
	{
		// Setup tabs
		//var _tabContainer = Zapatec.Widget.getElementById(self.config.tabs);
		var _tabContainer = self.config.tabs;
		var items = _tabContainer.childNodes;
		for (var i = items.length - 1; i >= 0; i--)
		{
			var tagName = items[i].tagName;
			if (tagName) {
				tagName = tagName.toLowerCase();
			}
			if ('div' == tagName || 'iframe' == tagName) {
				self.config._tabArray.push(items[i]);
			}
		}
		topPos = self.config._tabArray[self.config._tabArray.length - 1].offsetTop;

		// Position tabs
		// You need the number of tabs and their order
		// then, you can calculate where they should start and stop.
		////
		// Each tab has a visible area of:
		//	tabContainerHeight - (numOfTabs-1 * heightOfTabTitle)
		//
		// Each tab has a unique viewing position, derived from its place among the tabs as follows:
		//	tabTop = tabContainerTop + (tabIndex-1 * heightOfTabTitle)
		//
		// Each tab has a unique hidden position, derived from its place among the tabs as follows:
		//	tabTop = tabContainerTop + tabContainerHeight - (tabIndex * heightOfTabTitle)
		//
		// The tabs are layerd on top of each other in the order in which they appear in the HTML.
		// The first tab appears as the default.
		// In opening a tab, the chosen tab will carry all tabs below it along with it.
		// In closing a tab, the chosen tab will carry all tabs above it along with it.

		var _tabZIndex = 100;

		for (var i = 0; i < self.config._tabArray.length; i++)
		{
			var tab = self.getTabByIndex(i);
			var tabContainer = tab.tabContainer;
			var contentContainer = tab.container.getContainer();

			var visibleHeight = tab.config.visibleHeight;
			if (tab.config.tabType.toLowerCase() == "iframe") {
				tab.container.getContainer().style.width = '100%';
			}

			// Set tab content container height to 1 pixel
			contentContainer.style.height = '1px';
			Zapatec.Utils.addClass(contentContainer, "zpTabsNoOverflow");

			// Subract two to account for border
			tabContainer.style.width = _tabContainer.style.width;

			// Layer the tabs
			tabContainer.style.zIndex = _tabZIndex--;

			// This tab's array position
			tabContainer.arrayPosition = i;

			// This tab's viewing position
			tabContainer.viewingPosition = topPos + ((self.config._tabArray.length - 1 - i) * self.config.tabBarHeight);

			// This tab's hidden position
			tabContainer.hiddenPosition = topPos + parseInt(_tabContainer.style.height) - ((i + 1) * self.config.tabBarHeight);

			// This tab's final viewing height
			if (visibleHeight && 0 < visibleHeight) {
				tabContainer.viewingHeight = visibleHeight;
			}
			else {
				var tabContainerHeight = parseInt(_tabContainer.style.height);
				if (tabContainerHeight && 0 < tabContainerHeight) {
					// Calculate tab visible height by tabContainer total height and tab count
					tabContainer.viewingHeight = tabContainerHeight -
					                             ((self.config._tabArray.length/*-1*/) * self.config.tabBarHeight);
				}
				else {
					// Use default height if one can't be computed
					tabContainer.viewingHeight = 100;
				}
			}

			// This tab's hidden height
			tabContainer.hiddenHeight = self.config.tabBarHeight;
		}

		// Get tab id to make active
		var activeTabId = self.getInitialActiveTabId();
		if (-1 != activeTabId) {
			// Get tab to make active
			var activeTab = self.getTab(activeTabId);
			var tabContainer = activeTab.tabContainer;
			var contentContainer = activeTab.container.getContainer();
			// Activate tab
			contentContainer.style.height = tabContainer.viewingHeight + 'px';
			var setOverflowFunc = function() {
				Zapatec.Utils.removeClass(contentContainer, "zpTabsNoOverflow");
			};
			if (Zapatec.is_ie) {
				setTimeout(setOverflowFunc, 0);
			}
			else {
				setOverflowFunc();
			}
			self.currentIndex = activeTab.index;
			// Reload tab if needed and call onTabChange
			self.refreshTab(activeTab, null, activeTabId);
		}
		// Patch onBeforeTabChange handler
		var funcOnBeforeTabChange = self.config.onBeforeTabChange;

		self.config.onBeforeTabChange = function(objArgs)
		{
			// Call original function
			if (typeof funcOnBeforeTabChange == 'function')
			{
				return funcOnBeforeTabChange(objArgs);
			}
			return true;
		};

		// Patch onTabChange handler
		var funcOnTabChange = self.config.onTabChange;

		self.config.onTabChange = function(objArgs)
		{
			// Get new tab id
			var strNewTabId = objArgs.newTabId;

			// Get new tab
			var objNewTab = self.tabs[strNewTabId];

			if (!objNewTab)
			{
				return;
			}

			// Call original function
			if (typeof funcOnTabChange == 'function')
			{
				funcOnTabChange(objArgs);
			}
		};

		// Call original function
		if (typeof funcOnInit == 'function')
		{
			funcOnInit(objArgs);
		}
	};


	// Disable tab bar
	this.noTabBar = true;

	// Define config options
	this.config.windowOnLoad = null;

	// Tab controllers
	this.config._tabArray = new Array();
	this.config.IN_MOTION = false;
	this.config.tabBarHeight = 24;
	this.config.topPos = null;
	this.config.indexOfWidget = i;

	this.tabsThemeSuffix = 'AccordionContent';

	// Call parent init
	Zapatec.AccordionTabs.SUPERclass.init.call(this, objArgs);

}

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
Zapatec.AccordionTabs.prototype.addTab = function(objTabDef) {

	if (!objTabDef.collapseOnClick) {
		// Use config option from Tabs
		objTabDef.collapseOnClick = this.config.collapseOnClick;
	}

	// Call parent addTab
	var objTab = Zapatec.AccordionTabs.SUPERclass.addTab.call(this, objTabDef);

	// Store index of tab container within tabs
	objTab.tabContainer.index = objTab.index;

	return objTab;
}

/**
 * Create a new tab instance
 *
 * @param {object} objArgs tab configuration
 */
Zapatec.AccordionTabs.prototype.newTab = function(objArgs) {
	var objTab = new Zapatec.AccordionTab(objArgs);
	return objTab;
}

/**
 * Display a new accordion tab. If onBeforeTabChange() returns false, the
 * operation is cancelled.
 *
 * @param {string} strNewTabId id of the new tab.
 */
Zapatec.AccordionTabs.prototype.changeTab = function(strNewTabId) {
	var strCurrTabId = null;
	var objTab = null;

	if (this.tabsArray[this.currentIndex])
	{
		strCurrTabId = this.tabsArray[this.currentIndex].id;
		objTab = this.tabsArray[this.currentIndex];
	}

	if (strCurrTabId != strNewTabId && !this.config.IN_MOTION)
	{
		// Check if callback function allows to change tab
		var boolChangeTab = true;
		if (typeof this.config.onBeforeTabChange == 'function') {
			boolChangeTab = this.config.onBeforeTabChange({
				oldTabId: strCurrTabId,
				newTabId: strNewTabId
			});
		}
		if (!boolChangeTab) {
			return;
		}
		// Change tab
		if (objTab)
		{
			Zapatec.Utils.removeClass(objTab.linkNode, 'zpTabsActive');
		}

		objTab = this.getTab(strNewTabId);

		var oOffset = Zapatec.Utils.getElementOffset(this.config.tabs);
		// Setup WCH
		Zapatec.Utils.setupWCH(objTab.wch, 0, 0, oOffset.width, oOffset.height);

		Zapatec.Utils.addClass(objTab.linkNode, 'zpTabsActive');
		this.currentIndex = objTab.index;

		// Initiate sliding:
		this.slide(objTab.tabContainer.arrayPosition, 5, 10);

		// Reload tab if needed and call onTabChange
		this.refreshTab(objTab, strCurrTabId, strNewTabId);
	}
	else {
		// If collapseOnClick config option is set and
		// the current tab is being re-activated
		if (this.config.collapseOnClick && strCurrTabId == strNewTabId &&
		    !this.config.IN_MOTION) {
			// Collapse active tab
			this.collapseTab();
		}
	}
};

/**
 * Scrolls tabs to the left. Moves tab(s) by pxInc every timeInc
 *
 * @private
 * @param {boolean} setTab Whether the function should set the selected tab
 * on its own.
 */
Zapatec.AccordionTabs.prototype.slide = function(index, pxInc, timeInc) {
	if (false == this.config.IN_MOTION) {
		var date = new Date();
		this.moveStartTime = date.getTime();
		this.lastTime = this.moveStartTime - timeInc;
		this.isDecreaseHeight = true;
	}

	// Test for index validity
	if (isNaN(index) || index < -1 || index >= this.config._tabArray.length)
	{
		// Allow switching again
		this.config.IN_MOTION = false;

		return;
	}

	var date = new Date();
	var time = date.getTime();
	// Calculate time elapsed since last frame
	var diffTime = time - this.lastTime;
	// Calculate pixels difference for elapsed time
	var inc = Math.round((diffTime / timeInc) * pxInc);
	if (0 == inc) {
		var self = this;
		// Go again
		setTimeout(function() {
			self.slide(index, pxInc, timeInc);
		}, timeInc);
		return;
	}
	this.lastTime = time;

	var resizeTabs = {decreaseDif: 0, increaseDif: 0};

	// Flags if any tab has been resized
	var isAdjust = false;
	// Find 1 tab to increase and 1 tab to decrease
	for (var tries = 0; tries < 2; tries++) {
		for (var i = 0; i < this.config._tabArray.length; i++)
		{
			var tab = this.getTabByIndex(i);
			var tabContainer = tab.tabContainer;
			var contentContainer = tab.container.getContainer();
			var newHeight = -1;

			var oldHeight = parseInt(contentContainer.style.height);
			var isCurrent = tab.index == this.currentIndex;
			if (!isCurrent && this.isDecreaseHeight) {
				newHeight = oldHeight - inc;

				// Check for lower limit
				if (newHeight < 1) {
					newHeight = 1;
				}
			}
			else if (isCurrent && !this.isDecreaseHeight) {
				newHeight = oldHeight + inc;

				// Watch out for upper limit
				if (tabContainer.viewingHeight <= newHeight) {
					newHeight = tabContainer.viewingHeight;
				}
			}
			// If a new height is determined
			if (-1 != newHeight && oldHeight != newHeight) {

				if (this.isDecreaseHeight) {
					resizeTabs.decreaseTab = contentContainer;
					resizeTabs.decreaseDif = oldHeight - newHeight;
				}
				else {
					resizeTabs.increaseTab = contentContainer;
					resizeTabs.increaseDif = newHeight - oldHeight;
				}

				isAdjust = true;

				break;
			}
		}
		this.isDecreaseHeight = !this.isDecreaseHeight;

	}

	var dif;
	if (resizeTabs.increaseTab && resizeTabs.decreaseTab) {
		dif = Math.min(resizeTabs.decreaseDif, resizeTabs.increaseDif);
	}
	else if (!resizeTabs.increaseTab) {
		dif = resizeTabs.decreaseDif;
	}
	else {
		dif = resizeTabs.increaseDif;
	}

	var incTab = resizeTabs.increaseTab;
	var decTab = resizeTabs.decreaseTab;
	if (incTab) {
		var incOldHeight = parseInt(incTab.style.height);
		incTab.style.height = (incOldHeight + dif) + 'px';

		if (this.isLoadedHtml && incTab.tagName &&
		    incTab.tagName.toLowerCase() != 'iframe') {
			var tabContentDiv = Zapatec.Utils.getFirstChild(incTab, "div");
			Zapatec.Utils.addClass(tabContentDiv, "zpTabsNoOverflow");
		}

		Zapatec.Utils.addClass(incTab, "zpTabsNoOverflow");
	}
	if (decTab) {
		var decOldHeight = parseInt(decTab.style.height);
		if (!incTab && this.config.scrollPageOnSlide) {
			var scrollY = Zapatec.Utils.getPageScrollY();

			var winSize = Zapatec.Utils.getWindowSize();

			var decTabPos = Zapatec.Utils.getAbsolutePos(decTab);
			var scrollPosBottom = scrollY + winSize.height - document.body.clientHeight;
			// If vertical scroller is at the bottom
			if (0 <= scrollPosBottom && scrollPosBottom < 20) {
				var y = decTabPos.y + decOldHeight - winSize.height;
				if (y < 0) {
					y = 0;
				}
				window.scrollTo(0, y);
			}
			// If decreased tab goes up from browser viewport
			if (decTabPos.y + decOldHeight - dif < scrollY) {
				var y = decTabPos.y + decOldHeight - winSize.height;
				if (y < 0) {
					y = 0;
				}
				window.scrollTo(0, y);
			}
		}
		decTab.style.height = (decOldHeight - dif) + 'px';

		if (this.isLoadedHtml && decTab.tagName &&
		    decTab.tagName.toLowerCase() != 'iframe') {
			var tabContentDiv = Zapatec.Utils.getFirstChild(decTab, "div");
			Zapatec.Utils.addClass(tabContentDiv, "zpTabsNoOverflow");
		}

		Zapatec.Utils.addClass(decTab, "zpTabsNoOverflow");
	}

	if (!isAdjust) {
		// Allow switching again
		this.config.IN_MOTION = false;

		if (-1 != this.currentIndex) {
			var currentTab = this.getTabByIndex(this.currentIndex);

			var stoppedTabContainer = currentTab.container.getContainer();
			Zapatec.Utils.removeClass(stoppedTabContainer, "zpTabsNoOverflow");
		}

		return;
	}

	// Stop switching while this process is running
	this.config.IN_MOTION = true;

	var self = this;
	// Go again
	setTimeout(function() {
		self.slide(index, pxInc, timeInc);
	}, timeInc);
}

/**
 * Collapses the currently active tab
 *
 * @private
 */
Zapatec.AccordionTabs.prototype.collapseTab = function() {
	if (-1 != this.currentIndex) {
		var currentTab = this.getTabByIndex(this.currentIndex);
		Zapatec.Utils.removeClass(currentTab.linkNode, 'zpTabsActive');
	}

	this.currentIndex = -1;

	// Initiate sliding:
	this.slide(-1, 5, 10);

}