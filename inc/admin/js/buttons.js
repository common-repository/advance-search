(function() {
    if (typeof(wpas_mce_button_menu)!="undefined") {
      tinymce.PluginManager.add('wasp_mce_button', function( editor, url ) {
          eval("var asp_menus = [" + wpas_mce_button_menu + "]");
          editor.addButton( 'wasp_mce_button', {
              text: 'Advanced Search',
              icon: false,
              type: 'menubutton',
              menu: [
                  {
                      text: 'Search box',
                      menu: asp_menus
                  }
              ]
          });
      });
    }
})();