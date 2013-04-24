<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">

	<meta name="description" content=" This demo uses the Zapatec AJAX Transport layer to transfer the tab contents dynamically. The benefits of this approach are: Contents downloaded on demand. This optimizes data transfer. The HTML for the tab contents is transferred only when the tab is accessed. Reusable code. You can put the HTML tab contents in a file and reuse the code. Pure data driven tab construction. You can construct the tabs without any HTML, allowing an easy, dynamic, database-driven application. The presentation layer is the same as the Basic demo, yet the construction of the Zapatec Tabs is different. This offers the flexibility of using either method for tab construction. The following link illustrates how to add a tab at runtime The following link illustrates how to remove a tab at runtime ">
	<meta name="keywords" content="dhtml tools,javascript,DHTML Tools,Javascript,ajax,AJAX,Ajax,ajax tools,AJAX Tools,tools controls,simple javascript tools">
	<title>Zapatec DHTML Tabs Widget - AJAX Tabs</title>

	<!-- Common JS files -->
	<script type='text/javascript' src='utils/zapatec.js'></script>

	<!-- Custom includes -->	
	<script type="text/javascript" src="zptabs/src/zptabs.js"></script>

	<!-- ALL demos need these css -->
	<link href="website/css/zpcal.css" rel="stylesheet" type="text/css">
	<link href="website/css/template.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		body {
			width: 778px;
		}
	</style>
	<link rel="SHORTCUT ICON" href="http://www.zapatec.com/website/main/favicon.ico">
	


</head>
<body>	<div class='zpCalSubHeader' style='text-align:center;'>AJAX Tabs</div>

		<div class='zpCalDemoText'>
		<ul>
		
		
 
<li>The presentation layer is the same as the Basic demo, yet the construction of the Zapatec Tabs is different.
	This offers the flexibility of using either method for tab construction. </li>
  <li>The following link illustrates how to <a href="javascript:void(addTab())">add a tab</a> at runtime</li>
  <li>The following link illustrates how to <a href="javascript:void(removeTab())">remove a tab</a> at runtime</li>

		</ul>
		</div>



<div id="tabTopBar" style="width: 400px"></div>
<div id="tabInfo" style="height: 200px"></div>

<div style="padding: 10px; text-align: right">
<button onclick="objTabs.prevTab()" accesskey="p">&laquo; <u>P</u>rev.</button>
<button onclick="objTabs.nextTab()" accesskey="n"><u>N</u>ext &raquo;</button>
</div>

<script type="text/javascript">

var objSource = {
  tabs: [
    {
      id: 'game',
      linkInnerHTML: '<u>G</u>ame',
      accessKey: 'g',
      title: 'Game',
      url: 'content-game.html'
    },
    {
      id: 'photo',
      linkInnerHTML: 'P<u>h</u>oto',
      accessKey: 'h',
      title: 'Photo',
      url: 'content-photo.html'
    },
    {
      id: 'music',
      linkInnerHTML: '<u>M</u>usic',
      accessKey: 'm',
      title: 'Music',
      url: 'content-music.html'
    },
    {
      id: 'chat',
      linkInnerHTML: '<u>C</u>hat',
      accessKey: 'c',
      title: 'Chat',
      url: 'content-chat.html'
    }
  ]
};

var objTabs = new Zapatec.Tabs({
  tabBar: 'tabTopBar',
  tabs: 'tabInfo',
  source: objSource,
  sourceType: 'json',
  theme: 'default',
  themePath: 'zptabs/themes'
});

var newTabId = 0;
// This is an example how to add tabs dynamically
function addTab() {
  objTabs.addTab({
    id: 'new' + (++newTabId),
    linkInnerHTML: '<u>N</u>ew' + newTabId,
    accessKey: 'n',
    title: 'New' + newTabId,
    url: 'content-game.html',
    index: 4
  });
}

var removeTabId = 0;
// This is an example how to remove tabs dynamically
function removeTab() {
  if (newTabId <= removeTabId) {
    return;
  }
  objTabs.removeTab('new' + (++removeTabId));
}

</script>


	<noscript>
		<br/>
		This page uses an <a href='http://www.zapatec.com/website/main/products/suite/'>
		AJAX Component</a> - Zapatec DHTML Tabs Widget, but your browser does not support Javascript.
		<br/>
		<br/>
	</noscript>      
		<br/><br/><br/>
		<div class="footer" style='width: 778px; text-align:center; margin-top:2em'>
		&copy; 2004-2007 <strong> <a href='http://www.zapatec.com/'>Zapatec, Inc.</a> </strong>
		</div>
</body>
</html>