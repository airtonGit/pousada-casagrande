<?php

#-----------------------------------------------------------------
# Include Custom JavaScript in Theme Header
#-----------------------------------------------------------------


// Add scripts to header
//-----------------------------------------------------------------
function theme_options_custom_js() {
	// Custom Scripts from Theme Options
	echo '<script type="text/javascript">';
	theme_custom_scripts();
	echo '</script> ';
}
// Add hook for front-end <head></head>
add_action('wp_head', 'theme_options_custom_js', 102); // low priority to get it near the end


// Get custom JavaScript from theme options
//-----------------------------------------------------------------
if ( ! function_exists( 'theme_custom_scripts' ) ) :
function theme_custom_scripts() {

	// Custom layout specific settings (other/design options)
	$layout_data = get_layout_options('other_options');
	$layout_options = (isset($layout_data)) ? $layout_data : false;

	// Top banner dock
	$layoutStyle = (isset($layout_options['layout-style']) && !empty($layout_options['layout-style'])) ? $layout_options['layout-style'] : get_options_data('options-page', 'layout-style', 'boxed'); 
	$scrollDock  = get_options_data('options-page', 'dock-on-scroll');
	if ( $scrollDock == 'true' && !( empty($layoutStyle) || $layoutStyle == 'boxed-left' || $layoutStyle == 'full-width-left' || $layoutStyle == 'boxed-right' || $layoutStyle == 'full-width-right' ) ) {
		echo 'var dock_topBanner=true;';
	} else {
		echo 'var dock_topBanner=false;';
	}

	// Styled scrollbars
	$smoothScroll = get_options_data('options-page', 'smooth-scrolling', '');
	if ( isset($smoothScroll) && $smoothScroll == 'custom-scrollbars' ) {
		echo 'var theme_smoothScroll="custom";';
	} elseif ( isset($smoothScroll) && $smoothScroll == 'custom-scrollbars-no-ff' ) {
		echo 'var theme_smoothScroll="custom-no-ff";';
	} elseif ( isset($smoothScroll) && $smoothScroll == 'smooth-chrome' ) {
		echo 'var theme_smoothScroll="chrome";';
	} else {
		echo 'var theme_smoothScroll=false;';		
	}

	// Custom JavaScript
	$customJS = stripslashes(htmlspecialchars_decode(get_options_data('options-page', 'custom-js'),ENT_QUOTES));
	if (!empty($customJS)) {
		echo $customJS;
	}

	// Special feature for logged in users. Shows edit link: Ctrl + Click
	if ( is_user_logged_in() ) {
		echo 'jQuery(function($) { $("body").click(function(e) { if(e.ctrlKey) { editLink = $(".edit-link"); if (editLink.length) { editLink.toggle(); } } }); });';
	}

}
endif; ?>