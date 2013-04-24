// $Id: zptabs.js 7561 2007-07-16 21:06:11Z vkulov $
/**
 * @fileoverview Zapatec Tabs widget. Include this file in your HTML page.
 * Includes base Zapatec Tabs modules and required auxiliary modules.
 * To extend Tabs with other features like Wizard include respective modules
 * manually in your HTML page.
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
 * Path to Zapatec Tabs scripts.
 * @private
 */
Zapatec.tabsPath = Zapatec.getPath('Zapatec.TabsWidget');

// Include required scripts
Zapatec.Transport.include(Zapatec.tabsPath  + '../lang/eng.js');
Zapatec.Transport.include(Zapatec.zapatecPath + 'indicator.js', "Zapatec.Indicator");
Zapatec.Transport.include(Zapatec.zapatecPath + 'pane.js', "Zapatec.Pane");
Zapatec.Transport.include(Zapatec.tabsPath + 'zptabs-core.js', "Zapatec.Tabs");
