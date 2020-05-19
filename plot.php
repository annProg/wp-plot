<?php
/*
* Plugin Name: wp-plot
* Plugin URI: http://www.annhe.net/
* Description: Text to Chart For Wordpress
* Author: annhe
* Version: 1.0
* Author URI: http://www.annhe.net/
* */

class WordpressPlot {
	private $attr;
	private $api = "//api.annhe.net/api.php";
	private $cdn = "//attcdn.tecbbs.com";

	function __construct($attr) {
		$default = array(
			'cht' => 'gv:dot',
			'align' => 'center',
			'chof' => 'png',
			'width' => '',
			'height' => '',
			'caption' => 'Wordpress Text to Chart',
		);
		$this->attr = array_merge($default, $attr);
	}

	function cal_file_name($content) {
		$path = rtrim($this->cdn,"/") . "/" . "cache/images/";
		$flag = str_replace(":", "_", $this->attr['cht']);
		$content=preg_replace('/&lt;/','<',$content);
		$content=preg_replace('/&gt;/','>',$content);
		// 删除开头换行符，否则文件名计算会不正确
		$content = ltrim($content);
		return $path . md5(str_replace("\n", "\r\n", $content)) . "-" . $flag . "-" . $this->attr['width'] . "x" . $this->attr['height'] . "." . $this->attr['chof'];
	}

	/**
	 * Create output
	 */
	function render($content) {
		$id = $this->getGUID();
		$cht = $this->attr['cht'];
		$chof = $this->attr['chof'];
		$align = $this->attr['align'];
		$caption = $this->attr['caption'];

		$tpl='<div style="display:none" class="zxsq_mindmap_form">' .
			'<form accept-charset="utf-8" name="' . $id . '" id="' . $id . 
			'" method="post" action="' . $this->api . '" enctype="application/x-www-form-urlencoded">'.
			'<input type="hidden" name="cht" value="' . $cht . '" id="cht_' . $id . '">' .
			'<input type="hidden" name="chof" value="' . $this->attr['chof'] . '" id="chof_' . $id . '">' .
			'<textarea name="chl" id="chl_' . $id . '">' . $content . '</textarea></form></div>' .
			'<figure ' . 'align="' . $align . '"><img style="margin-left:auto; margin-right:auto" id="img_' . $id . '" src="' . $this->cal_file_name($content) . 
			'" alt="' . $caption . '" title="' . $caption . '"';
		$w = $this->attr['width'];
		$h = $this->attr['height'];
		if ($w) $tpl .= ' width="' . $w . 'px"';
		if ($h) $tpl .= ' height="' . $h . 'px"';
		$tpl .= '/>';
		$tpl .= '<figcaption style="text-align: center">' . $caption . '</figcaption></figure>';

		return $tpl;
	}

	function getGUID(){  
		$charid = strtoupper(md5(uniqid(rand(), true)));  
		$hyphen = chr(45);// "-"  
		$uuid = "zxsq_mindmap_form-"  
			.substr($charid, 0, 8).$hyphen  
			.substr($charid, 8, 4).$hyphen  
			.substr($charid,12, 4).$hyphen  
			.substr($charid,16, 4).$hyphen  
			.substr($charid,20,12); 
		return $uuid;  
	} 
}


function plotRender($atts, $content=null) {
	if ($atts == "") {
		$atts = ['chof' => 'png'];
	}
	$plot = new WordpressPlot($atts);
	$content=preg_replace('/<br\s?\/>/','',$content);
	$content=preg_replace('/<\/p>\n<p>/',"\n\n",$content);
	$content=preg_replace('/&#822(0|1);/','"',$content);
	$content=preg_replace('/&#8243;/','"',$content);
	$content=preg_replace('/&#8212;/','--',$content);
	$layout = $plot->render($content);
	return $layout;
}

function wpPlotScript() {
	wp_enqueue_script( 'loadplot', "/wp-content/plugins/wp-plot/loadplot.js", ['jquery'], '1.0', true );
}

function wp_plot_register_block() {
 
    // automatically load dependencies and version
    //$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
 
    wp_register_script(
        'wp-plot',
		plugins_url( 'block.js', __FILE__ ),
		array('wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n'),
		'1.0.0'
	);
 
    register_block_type( 'wp-plot/chart', array(
        'editor_script' => 'wp-plot',
    ) );
 
}

add_action( 'init', 'wp_plot_register_block' );

// https://wordpress.stackexchange.com/questions/257253/problem-in-wordpress-with dash like `-` `--`
add_filter( 'run_wptexturize', '__return_false' );

add_action( 'wp_enqueue_scripts', 'wpPlotScript' );
add_shortcode('plot','plotRender');
