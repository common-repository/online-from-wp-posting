<?php
function upidg_install()
{
  $option = get_option('upidg_plugin');
  if (false === $option) {
    # setup options:
    $option = array();
    $option['version'] = "2.2"; # plugin version
    $option['dbtable_name'] = "online"; # db table name
    $option['uninstall'] = false;
    add_option('upidg_plugin', $option);
    global $wpdb;
    $table = $wpdb->prefix.$option['dbtable_name'];
    $structure = "CREATE TABLE IF NOT EXISTS $table ( 
                  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                  post_id INT(11) NOT NULL,
                  date_added DATETIME NOT NULL 
                  );";
	
    $wpdb->query($structure);
    $sql = "INSERT INTO wp_online (id) VALUES (1)";
    $wpdb->query($sql);
  }
}

function upidg_uninstaller() {
  $option = get_option('upidg_plugin');
  global $wpdb;
  $table = $wpdb->prefix.$option['dbtable_name'];
  $wpdb->query("DROP TABLE " . $table);
  delete_option('upidg_plugin');
}
?>