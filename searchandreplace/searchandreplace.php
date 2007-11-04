<?php
/*
Plugin Name: Search and Replace
Plugin URI: http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114
Description: A simple search for find strings in your database and replace the string. Use in <a href="admin.php?page=searchandreplace/searchandreplace.php">Manage -> Search/Replace</a> by <a href='http://thedeadone.net/'>Mark Cunningham</a> and <a href="http://bueltge.de" >Frank Bueltge</a>
Version: 1.71
*/

/* 
Um dieses Plugin zu nutzen, musst du das File in den 
Plugin-Ordner deines WP kopieren und aktivieren.
Es fuegt einen neuen Tab im Bereich "Verwalten" hinzu.
Dort koennen Strings dann gesucht und ersetzt werden.
*/

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('searchandreplace','wp-content/plugins/searchandreplace');

if (!is_plugin_page()) {

	function tdo_searchandreplace_hook(){
		if (function_exists('add_management_page')) {
			add_management_page(__('Suchen &amp; Ersetzen', 'searchandreplace'),
													__('Suchen &amp; Ersetzen', 'searchandreplace'),
													10, /* only admins */
													__FILE__,
													'tdo_searchandreplace_hook');
		}
	}
	
	add_action('admin_head', 'tdo_searchandreplace_hook');
} else {

	/* this does the important stuff! */
	function tdo_do_searchandreplace($search_text,
																	$replace_text,
																	$content = TRUE,
																	$title = TRUE,
																	$excerpt = TRUE,
																	$comment_content = TRUE,
																	$comment_author = TRUE,
																	$comment_author_email = TRUE,
																	$comment_author_url = TRUE
																	){
		global $wpdb;
	
		if (!$content && !$title && !$excerpt && !$comment_content && !$comment_author && !$comment_author_email && !$comment_author_url){
			return __('Keine Aktion (Checkbox) gew&auml;hlt um zu ersetzen!', 'searchandreplace');
		}
	
		if ($content) {
			echo '<p>&raquo; ' . __('Suche nach Beitr&auml;gen', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_content = ";
			$query .= "REPLACE(post_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($title) {
			echo '<p>&raquo; ' . __('Suche nach Titeln', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_title = ";
			$query .= "REPLACE(post_title, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($excerpt) {
			echo '<p>&raquo; ' . __('Suche nach Ausz&uuml;gen', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_excerpt = ";
			$query .= "REPLACE(post_excerpt, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($comment_content) {
			echo '<p>&raquo; ' . __('Suche nach Kommentarbetr&auml;gen', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_content = ";
			$query .= "REPLACE(comment_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($comment_author) {
			echo '<p>&raquo; ' . __('Suche nach Kommentarautor', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author = ";
			$query .= "REPLACE(comment_author, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($comment_author_email) {
			echo '<p>&raquo; ' . __('Suche nach Kommentarautoren-E-Mails', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_email = ";
			$query .= "REPLACE(comment_author_email, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		if ($comment_author_url) {
			echo '<p>&raquo; ' . __('Suche nach Kommentarautor-URLs', 'searchandreplace') . ' ...</p>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_url = ";
			$query .= "REPLACE(comment_author_url, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
	
		return '';
	}
	
	?>
	
	<div class="wrap">
	<h2><?php _e('Suchen &amp; Ersetzen', 'searchandreplace'); ?></h2>
	
	<?php if (isset($_POST['submitted'])) {
				if (empty($_POST['search_text'])) { ?>
					<p><strong><?php _e('&raquo; Du musst Text spezifizieren, um Text zu ersetzen!', 'searchandreplace'); ?></strong></p>
	<?php } else { ?>
					<p><strong><?php _e('&raquo; Versuche die Suche duchzuf&uuml;hren und zu ersetzen ...', 'searchandreplace'); ?></strong></p>
					<p>&raquo; <?php _e('Suche nach', 'searchandreplace'); ?> <code><?php echo $_POST['search_text']; ?></code>...</p>
	
	<?php $error = tdo_do_searchandreplace(
									$_POST['search_text'],
									$_POST['replace_text'],
									isset($_POST['content']),
									isset($_POST['title']),
									isset($_POST['excerpt']),
									isset($_POST['comment_content']),
									isset($_POST['comment_author']),
									isset($_POST['comment_author_email']),
									isset($_POST['comment_author_url'])
								);
					if ($error != '') { ?>
						<div class="updated"><p><?php _e('Es gab eine St&ouml;rung!', 'searchandreplace'); ?></p>
					<p><code><?php echo $error; ?></code></p></div>
	<?php			} else { ?>
					<div class="updated"><p><?php _e('Erfolgreich durchgef&uuml;hrt!', 'searchandreplace'); ?></p></div>
	<?php			}
				}
		} ?>
	
	<p><?php _e('Dieses Plugin arbeitet mit einer Standard SQL Abfrage und ver&auml;ndert deine Datenbank direkt!<br 	/><strong>Achtung: </strong>Du <strong>kannst nichts</strong> r&uuml;ckg&auml;ngig machen mit diesem Plugin. Wenn du dir nicht sicher bist, fertige eine Sicherung deiner Datenbank im Vorfeld an.', 'searchandreplace'); ?></p>
	<p><?php _e('Die Textsuche ist sensitiv und besitzt keine passende Abstimmungsbef&auml;higung.<br />Du kannst folgende Eintr&auml;ge bearbeiten: Beitrag (content), Titel (titles), Auszug (excerpt), Kommentarbeitr&auml;ge (comment_content), Kommentarautor (comment_author) und Kommentar-URL (comment_author_url).<br />Die Funktion arbeitet stringbasierend und kann somit auch HTML-Tags ersetzen.', 'searchandreplace'); ?></p>
	
	<form name="replace" action="" method="post">
	<fieldset>
	<legend><strong><?php _e('Suche in', 'searchandreplace'); ?></strong></legend>
	<table>
		<tr>
			<td colspan=2><input type='checkbox' name='content' id='content_label' value='1' checked='checked' /><label for="content_label"> <?php _e('Beitr&auml;gen', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='title' id='title_label' value='1' checked='checked' /><label for="title_label"> <?php _e('Titeln', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='excerpt' id='excerpt_label' value='1' checked='checked' /><label for="excerpt_label"> <?php _e('Ausz&uuml;gen', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='comment_content' id='comment_content_label' value='1' checked='checked' /><label for="comment_content_label"> <?php _e('Kommentarbeitr&auml;gen', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='comment_author' id='comment_author_label' value='1' checked='checked' /><label for="comment_author_label"> <?php _e('Kommentarautoren', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='comment_author_email' id='comment_author_email_label' value='1' checked='checked' /><label for="comment_author_email_label"> <?php _e('Kommentarautoren-E-Mail', 'searchandreplace'); ?></label></td>
		</tr>
		<tr>
			<td colspan=2><input type='checkbox' name='comment_author_url' id='comment_author_url_label' value='1' checked='checked' /><label for="comment_author_url_label"> <?php _e('Kommentarautoren-URLs', 'searchandreplace'); ?></label></td>
		</tr>
	</table>
	</fieldset>
	<br/>
	<fieldset>
	<table>
		<tr>
			<td><?php _e('Ersetze', 'searchandreplace'); ?></td>
			<td><input class="code" type="text" name="search_text" value="" size="80" /></td>
		</tr>
		<tr>
			<td><?php _e('mit', 'searchandreplace'); ?></td>
			<td><input class="code" type="text" name="replace_text" value="" size="80" /></td>
		</tr>
	</table>
	<p class="submit"><input class="submit" type="submit" value="<?php _e('Ausf&uuml;hren', 'searchandreplace'); ?> &raquo;" /></p>
	<input type="hidden" name="submitted" />
	</fieldset>
	</form>
	<hr />
	<p><small><?php _e('&quot;Search and Replace&quot; Originalplugin (en) ist von <a href=\'http://thedeadone.net/\'>Mark Cunningham</a> und wurde erweitert (Kommentarbeitr&auml;ge, Kommentarautor) durch <a href=\'http://www.gonahkar.com\'>Gonahkar</a>.<br />&quot;Suchen &amp; Ersetzen&quot; wurde erweitert und gepflegt in der aktuellen Version durch <a href=\'http://bueltge.de\'>Frank Bueltge</a>.', 'searchandreplace'); ?></small></p>
	<p><small><?php _e('Weitere Informationen: Besuche die <a href=\'http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114\'>plugin homepage</a> f&uuml;r weitere Informationen oder nutze die letzte Version des Plugins.', 'searchandreplace'); ?><br />&copy; Copyright 2007 <a href="http://bueltge.de">Frank B&uuml;ltge</a> | <?php _e('Du willst Danke sagen? Besuche meine <a href=\'http://bueltge.de/wunschliste\'>Wunschliste</a>.', 'searchandreplace'); ?></small></p>
	</div>

<?php } ?>
