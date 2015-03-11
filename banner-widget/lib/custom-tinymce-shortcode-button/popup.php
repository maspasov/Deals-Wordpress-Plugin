<?php
    include_once('../../../../../wp-load.php');
?>
<!doctype html>
<html lang="en">
<head>
	<title>Banners &amp; Deals</title>
    <meta charset="UTF-8">
    <script src="tiny_mce_popup.js" type="text/javascript"></script>
    <style type="text/css" media="screen">
        body { margin: 20px 10px; }
    	#offers-field { width: 270px; font-size:15px; }
        #submit-offer { font-size:15px; }
        #shortcode-info { font-size:13px;line-height:16px;font-style:italic; }
    </style>
</head>
<body>

    <p id="shortcode-info">This shortcode acts as a wrapper to any custom html.<br>Just remember to add <b>content</b> inside the shortcode...</p>

	<?php 

		$available_offers = get_posts('post_type=banners_and_deals&posts_per_page=-1');
		echo '<select id="offers-field" name="offer">';
		foreach ($available_offers as $offer) {
			echo '<option value="'.$offer->ID.'">'.$offer->post_title.'</option>';
		}
		echo '</select>';

	?>
	<input type="button" id="submit-offer" value="Ok" />
    <script>
        document.getElementById( 'submit-offer' ).onclick = function(){
            var e = document.getElementById("offers-field");
			var selected_offer_id = e.options[e.selectedIndex].value;

            var shortcode_code = '[deals_link id="' + selected_offer_id + '"]<br><i>...insert content here...</i><br>[/deals_link]';

            tinyMCEPopup.execCommand( 'mceInsertContent', 0, shortcode_code );
            tinyMCEPopup.close();
        };
    </script>
</body>
</html>