<?php
/*
Plugin Name: Search &amp; Replace
Plugin URI: http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114
Description: A simple search for find strings in your database and replace the string. Use in <a href="admin.php?page=searchandreplace/searchandreplace.php">Manage -> Search/Replace</a>. 
Author: <a href='http://thedeadone.net/'>Mark Cunningham</a> and <a href="http://bueltge.de" >Frank Bueltge</a>
Version: 1.9
*/

/*
Um dieses Plugin zu nutzen, musst du das File in den 
Plugin-Ordner deines WP kopieren und aktivieren.
Es fuegt einen neuen Tab im Bereich "Verwalten" hinzu.
Dort koennen Strings dann gesucht und ersetzt werden.
*/

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('searchandreplace', 'wp-content/plugins/searchandreplace');

if ( !is_plugin_page() ) {

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

	// some basic security with nonce
	if ( !function_exists('wp_nonce_field') ) {
		function searchandreplace_nonce_field($action = -1) { return; }
		$searchandreplace_nonce = -1;
	} else {
		function searchandreplace_nonce_field($action = -1) { return wp_nonce_field($action); }
		$searchandreplace_nonce = 'searchandreplace-update-key';
	}


	/* this does the important stuff! */
	function tdo_do_searchandreplace($search_text,
																	$replace_text,
																	$content              = TRUE,
																	$title                = TRUE,
																	$excerpt              = TRUE,
																	$comment_content      = TRUE,
																	$comment_author       = TRUE,
																	$comment_author_email = TRUE,
																	$comment_author_url   = TRUE,
																	$cat_description      = TRUE,
																	$tag                  = TRUE
																	) {
		global $wpdb;
		
		// slug string
		$search_slug  = strtolower($search_text);
		$replace_slug = strtolower($replace_text);
		
		if (!$content && !$title && !$excerpt && !$comment_content && !$comment_author && !$comment_author_email && !$comment_author_url && !$cat_description && !$tag) {
			return __('<p><strong>Keine Aktion (Checkbox) gew&auml;hlt um zu ersetzen!</strong></p>', 'searchandreplace');
		}
		
		echo '<div class="updated">' . "\n" . '<ul>';
		
		// post content
		if ($content) {
			echo '<li>' . __('Suche nach Beitr&auml;gen', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_content = ";
			$query .= "REPLACE(post_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// post title
		if ($title) {
			echo '<li>' . __('Suche nach Titeln', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_title = ";
			$query .= "REPLACE(post_title, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// post excerpt
		if ($excerpt) {
			echo '<li>' . __('Suche nach Ausz&uuml;gen', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_excerpt = ";
			$query .= "REPLACE(post_excerpt, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment content
		if ($comment_content) {
			echo '<li>' . __('Suche nach Kommentarbetr&auml;gen', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_content = ";
			$query .= "REPLACE(comment_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author
		if ($comment_author) {
			echo '<li>' . __('Suche nach Kommentarautor', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author = ";
			$query .= "REPLACE(comment_author, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author_email
		if ($comment_author_email) {
			echo '<li>' . __('Suche nach Kommentarautoren-E-Mails', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_email = ";
			$query .= "REPLACE(comment_author_email, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author_url
		if ($comment_author_url) {
			echo '<li>' . __('Suche nach Kommentarautor-URLs', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_url = ";
			$query .= "REPLACE(comment_author_url, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}

		// category description
		if ($cat_description) {
			echo '<li>' . __('Suche nach Kategorie-Beschreibungen', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->term_taxonomy ";
			$query .= "SET description = ";
			$query .= "REPLACE(description, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// tags and category
		if ($tag) {
			echo '<li>' . __('Suche nach Tags', 'searchandreplace') . ' ...</li>';
			$query = "UPDATE $wpdb->terms ";
			$query .= "SET name = ";
			$query .= "REPLACE(name, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
			
			$query = "UPDATE $wpdb->terms ";
			$query .= "SET slug = ";
			$query .= "REPLACE(slug, \"$search_slug\", \"$replace_slug\") ";
			$wpdb->get_results($query);
		}

		echo "\n" . '</ul>';	
		return '';
	}
	?>
	
	<div class="wrap" id="top">
		<script type="text/javascript" language="JavaScript">
			//<![CDATA[
			function selectcb(thisobj,var1){
				var o = document.forms[thisobj].elements;
				if(o){
					for (i=0; i<o.length; i++){
						if (o[i].type == 'checkbox'){
							o[i].checked = var1;
						}
					}
				}
			}
			//]]>
		</script>

	<?php if ( isset($_POST['submitted']) ) {
					if ( function_exists('current_user_can') && current_user_can('edit_plugins') ) {
						check_admin_referer($searchandreplace_nonce);
			
						if (empty($_POST['search_text'])) { ?>
							<div class="error"><p><strong><?php _e('&raquo; Du musst Text spezifizieren, um Text zu ersetzen!', 'searchandreplace'); ?></strong></p></div>
			<?php } else { ?>
							<div class="updated">
								<p><strong><?php _e('&raquo; Versuche die Suche duchzuf&uuml;hren und zu ersetzen ...', 'searchandreplace'); ?></strong></p>
								<p>&raquo; <?php _e('Suche nach', 'searchandreplace'); ?> <code><?php echo $_POST['search_text']; ?></code> ... <?php _e('und ersetze mit', 'searchandreplace'); ?> <code><?php echo $_POST['replace_text']; ?></code></p>
							</div>
	
				<?php $error = tdo_do_searchandreplace(
												$_POST['search_text'],
												$_POST['replace_text'],
												isset($_POST['content']),
												isset($_POST['title']),
												isset($_POST['excerpt']),
												isset($_POST['comment_content']),
												isset($_POST['comment_author']),
												isset($_POST['comment_author_email']),
												isset($_POST['comment_author_url']),
												isset($_POST['cat_description']),
												isset($_POST['tag'])
											);
											
							if ($error != '') { ?>
								<div class="error"><p><?php _e('Es gab eine St&ouml;rung!', 'searchandreplace'); ?></p>
								<p><code><?php echo $error; ?></code></p></div>
				<?php } else { ?>
								<p><?php _e('Erfolgreich durchgef&uuml;hrt!', 'searchandreplace'); ?></p></div>
				<?php }
						} //end wp_nonce
					}
				} ?>

						<h2><?php _e('Search &amp; Replace', 'searchandreplace') ?></h2>
						<h3><?php _e('Hinweise Suchen &amp; Ersetzen', 'searchandreplace') ?></h3>
						<p><?php _e('Dieses Plugin arbeitet mit einer Standard SQL Abfrage und ver&auml;ndert deine Datenbank direkt!<br /><strong>Achtung: </strong>Du <strong>kannst nichts</strong> r&uuml;ckg&auml;ngig machen mit diesem Plugin. Wenn du dir nicht sicher bist, fertige eine Sicherung deiner Datenbank im Vorfeld an.', 'searchandreplace'); ?></p>
						<p><?php _e('<strong>Aktiviere</strong> das Plugin <strong>nur</strong>, wenn es ben&ouml;tigt wird!', 'searchandreplace'); ?></p>
						<p><?php _e('Die Textsuche ist sensitiv und besitzt keine passende Abstimmungsbef&auml;higung.<br />Du kannst folgende Eintr&auml;ge bearbeiten: Beitrag (content), Titel (titles), Auszug (excerpt), Kommentarbeitr&auml;ge (comment_content), Kommentarautor (comment_author), Kommentar-URL (comment_author_url), Kategorie-Beschreibung (description) und Tags/ Kategorie-Namen (name und slug).<br />Die Funktion arbeitet stringbasierend und kann somit auch HTML-Tags ersetzen.', 'searchandreplace'); ?></p>

						<div class="tablenav">
							<br style="clear: both;" />
						</div>

						<h3><?php _e('Suche in', 'searchandreplace') ?></h3>
						<form name="replace" action="" method="post">
							<?php searchandreplace_nonce_field($searchandreplace_nonce) ?>
							<table summary="config">
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='content' id='content_label' /></td>
									<td><label for="content_label"><?php _e('Beitr&auml;gen', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='title' id='title_label' /></td>
									<td><label for="title_label"><?php _e('Titeln', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='excerpt' id='excerpt_label' /></td>
									<td><label for="excerpt_label"><?php _e('Ausz&uuml;gen', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_content' id='comment_content_label' /></td>
									<td><label for="comment_content_label"><?php _e('Kommentarbeitr&auml;gen', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author' id='comment_author_label' /></td>
									<td><label for="comment_author_label"><?php _e('Kommentarautoren', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author_email' id='comment_author_email_label' /></td>
									<td><label for="comment_author_email_label"><?php _e('Kommentarautoren-E-Mail', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author_url' id='comment_author_url_label' /></td>
									<td><label for="comment_author_url_label"><?php _e('Kommentarautoren-URLs', 'searchandreplace'); ?></label></td>
								</tr>
								<?php if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$wpdb->prefix . 'terms'."'") ) == 1) { ?>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='cat_description' id='cat_description_label' /></td>
									<td><label for="cat_description_label"><?php _e('Kategorie-Beschreibung', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='tag' id='tag_label' /></td>
									<td><label for="tag_label"><?php _e('Tags &amp; Kategorien', 'searchandreplace'); ?></label></td>
								</tr>
								<?php } ?>
								<tr>
									<td colspan="2" style="text-align: center;">&nbsp;&nbsp; <a href="javascript:selectcb('replace', true);" title="<?php _e('Checkboxen markieren', 'searchandreplace'); ?>"><?php _e('alle', 'searchandreplace'); ?></a> | <a href="javascript:selectcb('replace', false);" title="<?php _e('Checkboxen demarkieren', 'searchandreplace'); ?>"><?php _e('keine', 'searchandreplace'); ?></a></td>
									<td>&nbsp;</td>
								</tr>
							</table>
							<br/>
							<table summary="submit" class="form-table">
								<tr>
									<th><?php _e('Ersetze', 'searchandreplace'); ?></th>
									<td><input class="code" type="text" name="search_text" value="" size="80" /></td>
								</tr>
								<tr>
									<th><?php _e('mit', 'searchandreplace'); ?></th>
									<td><input class="code" type="text" name="replace_text" value="" size="80" /></td>
								</tr>
							</table>
							<p class="submit">
								<input class="button" type="submit" value="<?php _e('Ausf&uuml;hren', 'searchandreplace'); ?> &raquo;" />
								<input type="hidden" name="submitted" />
							</p>
						</form>

						<div class="tablenav">
							<br style="clear: both;" />
						</div>

						<h3><?php _e('Hinweise zum Plugin', 'searchandreplace') ?></h3>
						<p><small><?php _e('&quot;Search and Replace&quot; Originalplugin (en) ist von <a href=\'http://thedeadone.net/\'>Mark Cunningham</a> und wurde erweitert (Kommentarbeitr&auml;ge, Kommentarautor) durch <a href=\'http://www.gonahkar.com\'>Gonahkar</a>.<br />&quot;Suchen &amp; Ersetzen&quot; wurde erweitert und gepflegt in der aktuellen Version durch <a href=\'http://bueltge.de\'>Frank Bueltge</a>.', 'searchandreplace'); ?></small></p>
						<p><small><?php _e('Weitere Informationen: Besuche die <a href=\'http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114\'>plugin homepage</a> f&uuml;r weitere Informationen oder nutze die letzte Version des Plugins.', 'searchandreplace'); ?><br />&copy; Copyright 2008 <a href="http://bueltge.de">Frank B&uuml;ltge</a> | <?php _e('Du willst Danke sagen? Besuche meine <a href=\'http://bueltge.de/wunschliste\'>Wunschliste</a>.', 'searchandreplace'); ?></small></p>
	</div>

<?php } ?>