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
	$plot = new WordpressPlot($atts);
	$content=preg_replace('/<br \/>/','',$content);
	$content=preg_replace('/&#822(0|1);/','"',$content);
	$layout = $plot->render($content);
	return $layout;
}

function wpPlotScript() {
	wp_enqueue_script( 'loadplot', "/wp-content/plugins/wp-plot/loadplot.js", ['jquery'], '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'wpPlotScript' );
add_shortcode('plot','plotRender');
