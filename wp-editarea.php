<?php
/*
Plugin Name: WP Editarea
Plugin URI: http://takien.com/606/wp-editarea-wordpress-plugin.php
Description: Turn your Oldschool textarea code editor in Wordpress Dashboard (plugin/theme editor) into a fancy realtime highlighted code editor using <a target="_blank" href="http://www.cdolivet.com/index.php?page=editArea" title="EditArea, a free javascript editor for source code">Editarea</a>.
Author: Takien
Author URI: http://takien.com
Version: 0.1
*/

add_action('admin_init', 'wp_editarea_register_setting'); 
add_action('admin_menu', 'wp_editarea_add_page');

if(get_option('wp_editarea_enable') == 'yes') {
	if(detect_page()) {
		add_action('admin_footer','wp_editarea');
	}
}

function wp_editarea_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=wp-editarea">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'wp_editarea_settings_link' );

$wp_editarea_options = Array(
		'start_highlight' 	=> 'true,false',
		'allow_toggle' 		=> 'true,false',
		'word_wrap' 		=> 'true,false',
		'language' 			=> 'en,bg,cs,de,dk,eo,es,fi,fr,hr,id,it,ja,mk,nl,pl,pt,ru,sk,zh',
		'display' 			=> 'later,onload'
		);

function detect_page() {
	$allowpage 	= Array('plugin-editor.php','theme-editor.php','options-general.php?page=wp-editarea');
	
	$page 		= $_SERVER["REQUEST_URI"];
	
	$i = -1;
	foreach($allowpage as $allow) {
		$i++;
		$pos[$i] = explode($allow,$page);
		if(array_key_exists(1,$pos[$i])) {
		return $allow;
		}
	}
}

function file_to_edit() {
	$allowfile = Array('css','html','js','php','xml');
	
	if($_GET['file']) {
		$file 	= $_GET['file'];
		$ext 	= end(explode('.',$file));
		if(in_array($ext,$allowfile)) {
		$return = $ext;
		}
	}
	if((detect_page() == 'theme-editor.php') && (!$_GET['file'])) {
		$return = 'css';
	}
	else {
		$return = 'php';
	}
return $return;
}

//register setting
function wp_editarea_register_setting() {
global $wp_editarea_options;
	foreach($wp_editarea_options as $key=>$value) {
	  register_setting( 'wp_editarea_option', 'wp_editarea_'.$key);
	}
	register_setting('wp_editarea_option','wp_editarea_enable');
}

//add page
function wp_editarea_add_page() {
    add_options_page('WP Editarea', 'WP Editarea', 8, 'wp-editarea', 'wp_editarea_page');
}

//the page
function wp_editarea_page() {
global $wp_editarea_options;
?>

<div class="wrap">
<h2>WP Editarea</h2>
<p>Turn your Oldschool textarea code editor in Wordpress Dashboard (plugin/theme editor) into a fancy realtime highlighted code editor.</p>

<form method="post" action="options.php">
<table class="widefat"width="100%">
<thead>
<tr><th width="300">Setting</th><th>Preview</th></tr>
</thead>
<tr><td valign="top">
<?php 
wp_nonce_field('update-options'); 
settings_fields('wp_editarea_option'); 

echo '<table width="280" cellpadding="0" cellspacing="0">';
echo '<tr><td height="50"><label for="wp_editarea_enable">WP Editarea Enable</label></td><td>: <input id="wp_editarea_enable" '.((get_option('wp_editarea_enable') == 'yes') ? 'checked="checked" ' : '').'name="wp_editarea_enable" value="yes" type="checkbox" />';
foreach($wp_editarea_options as $key=>$value) {
echo '<tr><td>
<label for="wp_editarea_'.$key.'">'.ucwords(str_replace('_',' ',$key)).'</label>
</td><td>: <select id="wp_editarea_'.$key.'" name="wp_editarea_'.$key.'">';
foreach(explode(',',$value) as $option) {
	echo '<option '.((get_option('wp_editarea_'.$key) == $option) ? 'selected="selected" ' : '').'value="'.$option.'">'.$option.'</option>';
}
echo '</select>
</td></tr>
';
}
echo '</table>';
?>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="wp_editarea_option" value="<?php 
foreach($wp_editarea_options as $key=>$value) {
echo 'wp_editarea_'.$key.',';
} ?>wp_editarea_enable" />

<p class="submit"><input type="submit" value="<?php _e('Update Settings') ?>" /></p>
</form>

</td>
<td>
<textarea style="width:100%" cols="70" rows="18" name="newcontent" id="newcontent" tabindex="1">
&lt;?php
//I hate using strpos();

$mystring = 'abc';
$findme   = 'a';
$pos = strpos($mystring, $findme);

// Note our use of ===.  Simply == would not work as expected
// because the position of 'a' was the 0th (first) character.
if ($pos === false) {
    echo "The string '$findme' was not found in the string '$mystring'";
} else {
    echo "The string '$findme' was found in the string '$mystring'";
    echo " and exists at position $pos";
}
?&gt;</textarea>
</td>
</tr>
</table>
<h3>Cooking instruction:</h3>
<ol>
<li><strong>WP Editarea Enable</strong>	: Toggle enable/disable Editarea</li>
<li><strong>Start Highlight</strong> 	: <em>True</em>: Start in highlight mode, <em>False</em>: You have to turn it manually from the toolbar.</li>
<li><strong>Allow Toggle</strong> 	: <em>True</em>: Display toggle enable/disable in the bottom of textarea, <em>False:</em> Not display</li>
<li><strong>Word Wrap</strong> 	: <em>True</em>: Wrap long lines, <em>False:</em> Long lines will not wrapped.</li>
<li><strong>Language</strong> 	: List of available language, ie. en = English (default)</li>
<li><strong>Display</strong>: <em>Onload</em>: Turn on highlighting immediately on page load, <em>Later</em>: You have to turn it on from the bottom of the textarea (<em>Allow toggle</em> must be true).</li>
</ol>
<hr />
<p><a target="_blank" title="Plugin homepage" href="http://takien.com">WP Editarea 0.1</a> powered by <a href="http://www.cdolivet.com/index.php?page=editArea" target="_blank" title="Editarea homepage">Editarea 0.8.2</a> &copy; Christophe Dolivet 2007-2010
</p>
</div>
<?php
}

function wp_editarea() {
echo '<script src="'.WP_PLUGIN_URL.'/wp-editarea/editarea/edit_area_full.js" type="text/javascript"></script>';
?>

<script language="Javascript" type="text/javascript">
// initialisation
		editAreaLoader.init({
		id: "newcontent"	
		,start_highlight: <?php echo get_option('wp_editarea_start_highlight'); ?>	
		,allow_toggle: <?php echo get_option('wp_editarea_allow_toggle'); ?>
		,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,syntax_selection_allow: "css,html,js,php,xml"
		,word_wrap: <?php echo get_option('wp_editarea_allow_toggle'); ?>
		,language: "<?php echo get_option('wp_editarea_language'); ?>"
		,syntax: "<?php echo file_to_edit();?>" //css etc
		,display: "<?php echo get_option('wp_editarea_display'); ?>" //later
		
		});

</script>

<?php
}

