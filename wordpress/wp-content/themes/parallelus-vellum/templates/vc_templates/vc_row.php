<?php
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = '';

extract(shortcode_atts(array(
    'el_class'        => '',
    'bg_image'        => '',
    'bg_color'        => '',
    'bg_image_repeat' => 'cover',
    'font_color'      => '',
    'padding'         => '',
    'margin_bottom'   => '',
    'bg_maps'         => '',       /* theme custom */
    'bg_maps_height'  => '',       /* theme custom */
    // 'bg_fill'         => 'cover',  /* theme custom */
    'bg_parallax'     => '',       /* theme custom */
    'inertia'         => '0.2',    /* theme custom */
), $atts));

wp_enqueue_style( 'js_composer_front' );
wp_enqueue_script( 'wpb_composer_front_js' );
wp_enqueue_style('js_composer_custom_css');

$el_class = $this->getExtraClass($el_class);

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_row '.get_row_css_class().$el_class, $this->settings['base']);

$style = $this->buildStyle('', '', '', $font_color, $padding, $margin_bottom);

$wrapper_class = 'vc_section_wrapper';
$section_wrapper_style = '';
$bg_layer_style = '';

if ($bg_parallax) {
	$wrapper_class .= ' parallax-section';
}
if ($bg_color) {
	$wrapper_class  .= ' has_bg_color';
	$bg_layer_style .= 'background-color:'. $bg_color .';';
}
if ($bg_image) {
	$media = wp_get_attachment_image_src($bg_image, 'full');
	$wrapper_class  .= ' has_bg_img';
	if ($bg_image_repeat == 'cover') {
		$wrapper_class  .= ' cover_all';		
	} else if ($bg_image_repeat == 'no-repeat') {
		$bg_layer_style .= 'background-repeat:no-repeat;';
	} else if ($bg_image_repeat == '') {
		$bg_layer_style .= 'background-repeat:repeat;';
	}
	$bg_layer_style .= 'background-image: url('.$media[0].');';
}
if ($bg_maps) {
        $wrapper_class .= ' wpb_map-section-full';
        $height = !$bg_maps_height ? 200 : $bg_maps_height;
        $section_wrapper_style = ' style="height: '.$height.'px"';
}


$output .= '<section class="'.$wrapper_class.'"'.$section_wrapper_style.'>';
if ($bg_layer_style) {
	$output .= '<div class="bg-layer" style="'. $bg_layer_style .'" data-inertia="'. $inertia .'"></div>';
}

if ($bg_maps) {
    $output .= '<div class="bg-layer cover_all" style="height: '.$height.'px;"><iframe width="100%" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$bg_maps.'&amp;t=m&amp;z=14&amp;output=embed"></iframe></div>';
}
$output .= '<div class="'.$css_class.'"'.$style.'>'. wpb_js_remove_wpautop($content). '</div>'.$this->endBlockComment('row');
$output .= '</section>';

echo $output;