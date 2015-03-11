(function() {
    tinymce.create('tinymce.plugins.Offers', {
        init : function(ed, url) {
 
            ed.addButton('showoffers', {
                title : 'Insert Banners & Deals',
                cmd : 'showoffers',
                image : sc_img,
                onPostRender: function() {
				    this.addClass('btn-deals');
				}
            });

	        ed.addCommand('showoffers', function() {

	        	ed.windowManager.open({
	                file   : sc_popup,
	                // popup_css: "bootstrap.css",
	                width  : 350,
	                height : 110,
	                inline : 1,
	                 buttons: [{
	                    text: 'Close',
	                    onclick: 'close'
	                }]
	            });

	        });
        }
    });
    // Register plugin
    tinymce.PluginManager.add( 'offers', tinymce.plugins.Offers );
})();