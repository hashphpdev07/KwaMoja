function ShutAllMenus() {
	var i;
	all_menus=document.getElementsByName('item_menu');
	for (i = 0; i < all_menus.length; i++) {
		all_menus[i].style.display = 'none';
	}
}

function BlackAllModules() {
	all_links=document.getElementsByName('module_link');
	var i;
	for (i = 0; i < all_links.length; i++) {
		all_links[i].style.backgroundColor = "#1A1A1A";
	}
}

function SetClickedModuleLink(id) {

	BlackAllModules();
	modulelink=document.getElementById(id);
	modulelink.style.backgroundColor = "#555";
	ShutAllMenus();
	menu=document.getElementById('item_'+id);
	menu.style.display='inline';
}

function CloseMenu() {
	ShutAllMenus();
	BlackAllModules();
}

function InactivateAllTabs(module) {
	all_links=document.getElementsByName(module+'tab_button');
	var i;
	for (i = 0; i < all_links.length; i++) {
		all_links[i].className = 'menu_button_inactive';
	}
}

function ChangeTab(module, section) {
	InactivateAllTabs(module);
	SelectedTab=document.getElementById(section);
	SelectedTab.className = 'menu_button_active';
	all_tabs=document.getElementsByName(module+'menu_container');
	for (i = 0; i < all_tabs.length; i++) {
		all_tabs[i].style.display = "none";
	}
	SelectedMenu=document.getElementById(section+'menu_container');
	SelectedMenu.style.display='inline-block';
}