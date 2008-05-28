<?php
/*
Plugin Name: Search &amp; Replace
Plugin URI: http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114
Description: A simple search for find strings in your database and replace the string. Use in <a href="admin.php?page=search-and-replace/search-and-replace.php">Manage -> Search/Replace</a>. 
Author: <a href='http://thedeadone.net/'>Mark Cunningham</a> and <a href="http://bueltge.de" >Frank Bueltge</a>
Version: 2.4.1
*/

/**
Um dieses Plugin zu nutzen, musst du das File in den 
Plugin-Ordner deines WP kopieren und aktivieren.
Es fuegt einen neuen Tab im Bereich "Verwalten" hinzu.
Dort koennen Strings dann gesucht und ersetzt werden.
*/

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('searchandreplace', str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');

if ( !is_plugin_page() ) {

	function tdo_searchandreplace_hook() {
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
																	$guid                 = TRUE,
																	$id                   = TRUE,
																	$title                = TRUE,
																	$excerpt              = TRUE,
																	$meta_value           = TRUE,
																	$comment_content      = TRUE,
																	$comment_author       = TRUE,
																	$comment_author_email = TRUE,
																	$comment_author_url   = TRUE,
																	$comment_count        = TRUE,
																	$cat_description      = TRUE,
																	$tag                  = TRUE,
																	$user_id              = TRUE,
																	$user_login           = TRUE
																	) {
		global $wpdb;

		// slug string
		$search_slug  = strtolower($search_text);
		$replace_slug = strtolower($replace_text);
		
		if (!$content && !$id && !$guid && !$title && !$excerpt && !$meta_value && !$comment_content && !$comment_author && !$comment_author_email && !$comment_author_url && !$comment_count && !$cat_description && !$tag && !$user_id && !$user_login) {
			return __('<p><strong>Keine Aktion (Checkbox) gew&auml;hlt um zu ersetzen!</strong></p>', 'searchandreplace');
		}

		echo '<br /><div class="updated">' . "\n" . '<ul>';
		
		// post content
		if ($content) {
			echo "\n" . '<li>' . __('Suche nach Beitr&auml;gen', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('post_content', 'posts');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_content = ";
			$query .= "REPLACE(post_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}

		// post id
		if ($id) {
			echo "\n" . '<li>' . __('Suche nach ID', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('ID', 'posts');
			fb_sql_results('post_parent', 'posts');
			fb_sql_results('post_id', 'postmeta');
			fb_sql_results('object_id', 'term_relationships');
			fb_sql_results('comment_post_ID', 'comments');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET ID = ";
			$query .= "REPLACE(ID, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
			
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_parent = ";
			$query .= "REPLACE(post_parent, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
	
			$query = "UPDATE $wpdb->postmeta ";
			$query .= "SET post_id = ";
			$query .= "REPLACE(post_id, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
	
			$query = "UPDATE $wpdb->term_relationships ";
			$query .= "SET object_id = ";
			$query .= "REPLACE(object_id, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);

			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_post_ID = ";
			$query .= "REPLACE(comment_post_ID, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// post guid
		if ($guid) {
			echo "\n" . '<li>' . __('Suche nach GUID', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('guid', 'posts');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET guid = ";
			$query .= "REPLACE(guid, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// post title
		if ($title) {
			echo "\n" . '<li>' . __('Suche nach Titeln', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('post_title', 'posts');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_title = ";
			$query .= "REPLACE(post_title, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// post excerpt
		if ($excerpt) {
			echo "\n" . '<li>' . __('Suche nach Ausz&uuml;gen', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('post_excerpt', 'posts');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_excerpt = ";
			$query .= "REPLACE(post_excerpt, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// meta_value
		if ($meta_value) {
			echo "\n" . '<li>' . __('Suche nach Meta Daten', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('meta_value', 'postmeta');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->postmeta ";
			$query .= "SET meta_value = ";
			$query .= "REPLACE(meta_value, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment content
		if ($comment_content) {
			echo "\n" . '<li>' . __('Suche nach Kommentarbetr&auml;gen', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('comment_content', 'comments');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_content = ";
			$query .= "REPLACE(comment_content, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author
		if ($comment_author) {
			echo "\n" . '<li>' . __('Suche nach Kommentarautor', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('comment_author', 'comments');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author = ";
			$query .= "REPLACE(comment_author, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author_email
		if ($comment_author_email) {
			echo "\n" . '<li>' . __('Suche nach Kommentarautoren-E-Mails', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('comment_author_email', 'comments');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_email = ";
			$query .= "REPLACE(comment_author_email, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// comment_author_url
		if ($comment_author_url) {
			echo "\n" . '<li>' . __('Suche nach Kommentarautor-URLs', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('comment_author_url', 'comments');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->comments ";
			$query .= "SET comment_author_url = ";
			$query .= "REPLACE(comment_author_url, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}

		// comment_count
		if ($comment_count) {
			echo "\n" . '<li>' . __('Suche nach Kommentar-Counter', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('comment_count', 'posts');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET comment_count = ";
			$query .= "REPLACE(comment_count, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}

		// category description
		if ($cat_description) {
			echo "\n" . '<li>' . __('Suche nach Kategorie-Beschreibungen', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('description', 'term_taxonomy');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->term_taxonomy ";
			$query .= "SET description = ";
			$query .= "REPLACE(description, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}
		
		// tags and category
		if ($tag) {
			echo "\n" . '<li>' . __('Suche nach Tags', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('name', 'terms');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->terms ";
			$query .= "SET name = ";
			$query .= "REPLACE(name, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
			
			$query = "UPDATE $wpdb->terms ";
			$query .= "SET slug = ";
			$query .= "REPLACE(slug, \"$search_slug\", \"$replace_slug\") ";
			$wpdb->get_results($query);
		}

		// user_id
		if ($user_id) {
			echo "\n" . '<li>' . __('Suche nach User-ID', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('ID', 'users');
			fb_sql_results('user_id', 'usermeta');
			fb_sql_results('post_author', 'posts');
			fb_sql_results('link_owner', 'links');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->users ";
			$query .= "SET ID = ";
			$query .= "REPLACE(ID, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
			
			$query = "UPDATE $wpdb->usermeta ";
			$query .= "SET user_id = ";
			$query .= "REPLACE(user_id, \"$search_slug\", \"$replace_slug\") ";
			$wpdb->get_results($query);
			
			$query = "UPDATE $wpdb->posts ";
			$query .= "SET post_author = ";
			$query .= "REPLACE(post_author, \"$search_slug\", \"$replace_slug\") ";
			$wpdb->get_results($query);
			
			$query = "UPDATE $wpdb->links ";
			$query .= "SET link_owner = ";
			$query .= "REPLACE(link_owner, \"$search_slug\", \"$replace_slug\") ";
			$wpdb->get_results($query);
		}

		// user_login
		if ($user_login) {
			echo "\n" . '<li>' . __('Suche nach User Login', 'searchandreplace') . ' ...';
			
			echo "\n" . '<ul>' . "\n";
			fb_sql_results('user_login', 'users');
			echo "\n" . '</ul>' . "\n" . '</li>' . "\n";
			
			$query = "UPDATE $wpdb->users ";
			$query .= "SET user_login = ";
			$query .= "REPLACE(user_login, \"$search_text\", \"$replace_text\") ";
			$wpdb->get_results($query);
		}

		echo "\n" . '</ul>' . "\n";
		return '';
	}

	/**
	 * View results
	 * @var: $field, $tabel
	 */
	function fb_sql_results($field, $table) {
		global $wpdb;
		
		$results == '';
		$search_text = $_POST['search_text'];

		echo "\n" . '<li>';
		$results = "SELECT $field FROM " . $wpdb->$table . " WHERE $field = \"$search_text\"";
		//echo $results . '<br />';
		_e('... in Tabelle ', 'searchandreplace');
		echo '<code>' . $table . '</code>: ';
		$results = mysql_query($results);
		
		if (!$results) {
			_e('Die Anfrage konnte nicht ausgef&uuml;hrt werden : ', 'searchandreplace') . mysql_error();
		} else {
		
			if (mysql_num_rows($results) == 0) {
				_e('Keine Eintr&auml;ge gefunden.', 'searchandreplace');
			} else {
				
				while ( $row = mysql_fetch_assoc($results) ) {
					//echo $row[$field] . "\n";
					echo '|';
				}
				$result = mysql_num_rows($results);
				echo ' - <strong>' . $result . '</strong>';
				echo '</li>' . "\n";

			}
			
		}
	}
	?>
	
	<div class="wrap" id="top">
						<h2><?php _e('Search &amp; Replace', 'searchandreplace') ?></h2>
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
												isset($_POST['guid']),
												isset($_POST['id']),
												isset($_POST['title']),
												isset($_POST['excerpt']),
												isset($_POST['meta_value']),
												isset($_POST['comment_content']),
												isset($_POST['comment_author']),
												isset($_POST['comment_author_email']),
												isset($_POST['comment_author_url']),
												isset($_POST['comment_count']),
												isset($_POST['cat_description']),
												isset($_POST['tag']),
												isset($_POST['user_id']),
												isset($_POST['user_login'])
											);
											
							if ($error != '') { ?>
								<div class="error"><p><?php _e('Es gab eine St&ouml;rung!', 'searchandreplace'); ?></p>
								<p><code><?php echo $error; ?></code></p></div>
				<?php } else { ?>
								<p><?php _e('Erfolgreich durchgef&uuml;hrt!', 'searchandreplace'); ?></p></div>
				<?php }
						}
					} else {
						wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this blog.').'</p>');
					}
				} ?>

						<h3><?php _e('Hinweise Suchen &amp; Ersetzen', 'searchandreplace') ?></h3>
						<p><?php _e('Dieses Plugin arbeitet mit einer Standard SQL Abfrage und ver&auml;ndert deine Datenbank direkt!<br /><strong>Achtung: </strong>Du <strong>kannst nichts</strong> r&uuml;ckg&auml;ngig machen mit diesem Plugin. <strong>Fertige eine <a href="http://bueltge.de/wp-datenbank-backup-mit-phpmyadmin/97/" title="Klick zum Tutorial">Sicherung deiner Datenbank</a> im Vorfeld an.</strong> Keine Rechtsanspr&uuml;che an den Autor des Plugins! <strong>Aktiviere</strong> das Plugin <strong>nur</strong>, wenn es ben&ouml;tigt wird!', 'searchandreplace'); ?></p>
						<p><?php _e('Die Textsuche ist sensitiv und besitzt keine passende Abstimmungsbef&auml;higung. Die Funktion arbeitet stringbasierend und kann somit auch HTML-Tags ersetzen.', 'searchandreplace'); ?></p>

						<div class="tablenav">
							<br style="clear: both;" />
						</div>

						<h3><?php _e('Suche in', 'searchandreplace') ?></h3>
						<form name="replace" action="" method="post">
							<?php searchandreplace_nonce_field($searchandreplace_nonce) ?>
							<table summary="config" class="widefat">
								<tr>
									<th><label for="content_label"><?php _e('Beitr&auml;gen', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='content' id='content_label' /></td>
									<td><label for="content_label"><?php _e('Feld: <code>post_content</code> Tabelle: <code>_posts</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<?php if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$wpdb->prefix . 'terms'."'") ) == 1) { ?>
								<tr class="form-invalid">
									<th><label for="id_label"><?php _e('ID', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='id' id='id_label' /></td>
									<td><label for="id_label"><?php _e('Feld: <code>ID</code>, <code>post_parent</code>, <code>post_id</code>, <code>object_id</code> und <code>comments</code><br />Tabelle: <code>_posts</code>, <code>_postmeta</code>, <code>_term_relationships</code> und <code>_comment_post_ID</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<?php } ?>
								<tr>
									<th><label for="guid_label"><?php _e('GUID', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='guid' id='guid_label' /></td>
									<td><label for="guid_label"><?php _e('Feld: <code>guid</code> Tabelle: <code>_posts</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr class="form-invalid">
									<th><label for="title_label"><?php _e('Titeln', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='title' id='title_label' /></td>
									<td><label for="title_label"><?php _e('Feld: <code>post_tilte</code> Tabelle: <code>_posts</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="excerpt_label"><?php _e('Ausz&uuml;gen', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='excerpt' id='excerpt_label' /></td>
									<td><label for="excerpt_label"><?php _e('Feld: <code>post_excerpt</code> Tabelle: <code>_posts</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr class="form-invalid">
									<th><label for="meta_value_label"><?php _e('Meta Daten', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='meta_value' id='meta_value_label' /></td>
									<td><label for="meta_value_label"><?php _e('Feld: <code>meta_value</code> Tabelle: <code>_postmeta</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="comment_content_label"><?php _e('Kommentarbeitr&auml;gen', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_content' id='comment_content_label' /></td>
									<td><label for="comment_content_label"><?php _e('Feld: <code>comment_content</code> Tabelle: <code>_comments</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr class="form-invalid">
									<th><label for="comment_author_label"><?php _e('Kommentarautoren', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author' id='comment_author_label' /></td>
									<td><label for="comment_author_label"><?php _e('Feld: <code>comment_author</code> Tabelle: <code>_comments</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="comment_author_email_label"><?php _e('Kommentarautoren-E-Mail', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author_email' id='comment_author_email_label' /></td>
									<td><label for="comment_author_email_label"><?php _e('Feld: <code>comment_author_email</code> Tabelle: <code>_comments</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr class="form-invalid">
									<th><label for="comment_author_url_label"><?php _e('Kommentarautoren-URLs', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_author_url' id='comment_author_url_label' /></td>
									<td><label for="comment_author_url_label"><?php _e('Feld: <code>comment_author_url</code> Tabelle: <code>_comments</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="comment_count_label"><?php _e('Kommentar-Counter', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='comment_count' id='comment_count_label' /></td>
									<td><label for="comment_count_label"><?php _e('Feld: <code>comment_count</code> Tabelle: <code>_posts</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<?php if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$wpdb->prefix . 'terms'."'") ) == 1) { ?>
								<tr class="form-invalid">
									<th><label for="cat_description_label"><?php _e('Kategorie-Beschreibung', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='cat_description' id='cat_description_label' /></td>
									<td><label for="cat_description_label"><?php _e('Feld: <code>description</code> Tabelle: <code>_term_taxonomy</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="tag_label"><?php _e('Tags &amp; Kategorien', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='tag' id='tag_label' /></td>
									<td><label for="tag_label"><?php _e('Feld: <code>name</code> und <code>slug</code> Tabelle: <code>_terms</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<?php } ?>
								<tr class="form-invalid">
									<th><label for="user_id_label"><?php _e('User-ID', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='user_id' id='user_id_label' /></td>
									<td><label for="user_id_label"><?php _e('Feld: <code>ID</code>, <code>user_id</code>, <code>post_author</code> und <code>link_owner</code><br />Tabelle: <code>_users</code>, <code>_usermeta</code>, <code>_posts</code> und <code>_links</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr>
									<th><label for="user_login_label"><?php _e('User-Login', 'searchandreplace'); ?></label></th>
									<td colspan="2" style="text-align: center;"><input type='checkbox' name='user_login' id='user_login_label' /></td>
									<td><label for="user_login_label"><?php _e('Feld: <code>user_login</code> Tabelle: <code>_users</code>', 'searchandreplace'); ?></label></td>
								</tr>
								<tr class="form-invalid">
									<th>&nbsp;</th>
									<td colspan="2" style="text-align: center;">&nbsp;&nbsp; <a href="javascript:selectcb('replace', true);" title="<?php _e('Checkboxen markieren', 'searchandreplace'); ?>"><?php _e('alle', 'searchandreplace'); ?></a> | <a href="javascript:selectcb('replace', false);" title="<?php _e('Checkboxen demarkieren', 'searchandreplace'); ?>"><?php _e('keine', 'searchandreplace'); ?></a></td>
									<td>&nbsp;</td>
								</tr>
							</table>

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
						<p><small><?php _e('Weitere Informationen: Besuche die <a href=\'http://bueltge.de/wp-suchen-und-ersetzen-de-plugin/114\'>plugin homepage</a> f&uuml;r weitere Informationen oder nutze die letzte Version des Plugins.', 'searchandreplace'); ?><br />&copy; Copyright 2006 - <?php echo date("Y"); ?> <a href="http://bueltge.de">Frank B&uuml;ltge</a> | <?php _e('Du willst Danke sagen? Besuche meine <a href=\'http://bueltge.de/wunschliste\'>Wunschliste</a>.', 'searchandreplace'); ?></small></p>
	</div>

<?php } ?>