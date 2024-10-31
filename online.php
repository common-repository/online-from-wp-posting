<?php
/* 
Plugin Name: Online.ua From WP Posting 
Plugin URI: http://www.uol.ua/wordpress/
Description: Автоматический кросс-пост записей из Wordpress в блог пользователя Online.ua - на стену в социальной сети "Украинцы онлайн".
Version: 2.2 
Author: Online.ua
Author URI: http://www.online.ua/ 
License: GPL2 
*/

function send($data){
	list($header, $content) = PostRequest("http://api.online.ua/online_from_wp.php","localhost",$data);
//	print $content;
}
//postinuol();
require ('hcode.php');
function postinuol($post_ID) {
	global  $wpdb;
	$sql = "SELECT ID, object_id, post_title, post_date, post_content
		FROM wp_term_relationships, wp_posts
		WHERE post_type = 'post' AND ID= $post_ID LIMIT 1";
	$result = $wpdb->get_results($sql); 
	$id = $result[0]->ID; 
    $post_date = $result[0]->post_date;
	$sql = "SELECT post_id FROM wp_online WHERE id=1";
	$online_id = $wpdb->get_results($sql);
	$hcode  = get_option ('hcode');
	if ($online_id[0]->post_id != $id && !empty($hcode)){
		$sql = "UPDATE wp_online SET post_id=$id, date_added= '$post_date' WHERE id=1";
		$wpdb->query($sql);
		$title_text  = $result[0]->post_title;
		$content_text = $result[0]->post_content;
		$sql = "SELECT hash FROM wp_online WHERE id=1";
		$online_hash = $wpdb->get_results($sql);
			$data = array(
			'hash' => $hcode,
			'title' => $title_text,
			'content' => htmlentities(urlencode($content_text)),
			'domain' => "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]
		);
	send($data);
	}
}
add_action ('publish_post', 'postinuol');


function PostRequest($url, $referer, $_data) {
	$data = array(); 
	while(list($n,$v) = each($_data)){
		$data[] = "$n=$v";
	}
	$data = implode('&', $data);
	$url = parse_url($url);
	if ($url['scheme'] != 'http') { 
		die('Only HTTP request are supported !');
	}
	$host = $url['host'];
	$path = $url['path'];

	$fp = fsockopen($host, 80);

	fputs($fp, "POST $path HTTP/1.1\r\n");
	fputs($fp, "Host: $host\r\n");
	fputs($fp, "Referer: $referer\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ". strlen($data) ."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $data);
	$result = ''; 
	while(!feof($fp)) {
		$result .= fgets($fp, 128);
	}

	fclose($fp);

	$result = explode("\r\n\r\n", $result, 2);
	$header = isset($result[0]) ? $result[0] : '';
	$content = isset($result[1]) ? $result[1] : '';

	return array($header, $content);
}

?>