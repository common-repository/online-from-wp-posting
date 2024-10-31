<?php
class hcode
{
	function hcode()
	{
		add_option('hcode', '');
		if (function_exists ('add_shortcode') )
		{
			add_action('admin_menu',  array (&$this, 'admin') );
		}
	}
	function admin ()
	{
		if ( function_exists('add_options_page') ) 
		{
			add_options_page( 'Online from wp', 'Online from wp', 8, basename(__FILE__), array (&$this, 'admin_form') );
		}
	}
	
	function admin_form()
	{
		$userhcode = get_option('hcode');
		if ( isset($_POST['submit']) ) 
		{	
		   if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			  die ( _e('Hacker?', 'hcode') );
			
			if (function_exists ('check_admin_referer') )
			{
				check_admin_referer('hcode_form');
			}
			$userhcode = $_POST['userhcode'];
			update_option('hcode', $userhcode);
			$data = array(
			'hash' => $userhcode,
			'title' => 'enable',
			'domain' => "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]
			);
			send($data);
			
		}
		?>
		<div class='wrap'>
			<h2>Online from wp plugin</h2>
			<form name="hcode" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=hcode.php&amp;updated=true">
				<?php 
					if (function_exists ('wp_nonce_field') )
					{
						wp_nonce_field('hcode_form'); 
					}
				?>
				<table class="form-table">
				<tr>
					<td colspan="2">
						Этот плагин позволяет настроить автоматический кросс-пост записей из Wordpress в блог пользователя Online.ua - на стену в социальной сети "Украинцы онлайн". Для этого необходимо быть зарегистрированным пользователем портала Online.ua и в настройках профайла указать адрес Вашего Wordpress блога. После этого Вам будет передан код активации, который нужно разместить в поле "Код активации" в настройках плагина "Online.ua From WP Posting" вашего блога.
						<br>
						Когда код активации указан и сохранён, Вам необходимо перейти в настройки профайла пользователя Online.ua и инициировать активацию Вашего блога. После активации все новые записи будут автоматически публиковаться на стену в социальной сети "Украинцы онлайн".
					</td>
				</tr>
					<tr valign="top">
						<th scope="row">Код активации:</th>
						<td>
							<input type="text" name="userhcode" size="45" value="<?php echo $userhcode; ?>" />
						</td>
					</tr>
				</table>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="hcode" />
				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?
	}
}

$hcode = new hcode();

?>