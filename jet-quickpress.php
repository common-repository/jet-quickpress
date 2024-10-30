<?php

//REQUIRED
require_once ( BP_QUICKPRESS_PLUGIN_DIR . '/jet-quickpress-templatetags.php' );
if (!class_exists('TreeSet'))
	require_once ( BP_QUICKPRESS_PLUGIN_DIR . '/jet-quickpress-classes.php' );

//NAV SETUP
function bp_quickpress_setup_nav() {
	global $bp;
	if (!bp_quickpress_user_can('edit_posts')) return false;
	$blogs_link = $bp->loggedin_user->domain . $bp->blogs->slug . '/';
	bp_core_new_subnav_item( array( 'name' => __( 'QuickPress','jet-quickpress-slug' ), 'slug' => __( 'quickpress', 'jet-quickpress-slugs' ), 'parent_url' => $blogs_link, 'parent_slug' => $bp->blogs->slug, 'screen_function' => 'bp_quickpress_screen_write_a_post', 'position' => 15 ) );
	do_action( 'bp_quickpress_setup_nav' );
}



//JS
function bp_quickpress_scripts() {
	global $bp;
	if (( $bp->current_component == $bp->blogs->slug ) && ($bp->current_action==__( 'quickpress', 'jet-quickpress-slugs' ))) {
	wp_enqueue_script( 'jet-quickpress-expandable-tree-js', apply_filters('bp_quickpress_enqueue_url',get_stylesheet_directory_uri() . '/quickpress/_inc/js/expandableTree.js'),array('jquery'), '1.0' );
	wp_enqueue_script( 'jquery.autocomplete', apply_filters('bp_quickpress_enqueue_url',get_stylesheet_directory_uri() . '/quickpress/_inc/js/jquery-autocomplete/jquery.autocomplete.pack.js'),array('jquery'), '1.0' );
	}
}
//CSS
function bp_quickpress_css() {
	global $bp;

	if (( $bp->current_component == $bp->blogs->slug ) && ($bp->current_action==__( 'quickpress', 'jet-quickpress-slugs' )))
		wp_enqueue_style( 'jet-quickpress-screen', apply_filters('bp_quickpress_enqueue_url',get_stylesheet_directory_uri() . '/quickpress/style.css') );
		wp_enqueue_style( 'jquery.autocomplete', apply_filters('bp_quickpress_enqueue_url',get_stylesheet_directory_uri() . '/quickpress/_inc/js/jquery-autocomplete/jquery.autocomplete.css') );
}


/**THEMING FUNCTIONS & FILTERS|START**/

//those filters & functions will load the defaults plugin themes (included inside the plugin directory) in there is no specific templates files into the current theme.

function bp_quickpress_located_template( $located_template, $template_name ) {

	if ( !empty( $located_template ) )
		return $located_template;

	if (!(( $bp->current_component == $bp->blogs->slug ) && ($bp->current_action==__( 'quickpress', 'jet-quickpress-slugs' )))) return false;

	$template_path = BP_QUICKPRESS_PLUGIN_DIR."/theme/$template_name[0]";
	
	if ( file_exists( $template_path ) ) {
		
		return $template_path;
	}

	
	return false;
}
add_filter( 'bp_located_template', 'bp_quickpress_located_template', 10, 2 );

function bp_quickpress_load_template_files($file) {

	$theme_path = STYLESHEETPATH . '/quickpress';
	$theme_url = get_stylesheet_directory_uri() . '/quickpress';
	
	if ( file_exists( $theme_path.'/'.$file ) ) {
		return $theme_url.'/'.$file;
	}else {
		return BP_QUICKPRESS_PLUGIN_URL.'/theme/quickpress/'.$file;
	}
	

}
add_filter('bp_quickpress_locate_js','bp_quickpress_load_template_files');
add_filter('bp_quickpress_locate_css','bp_quickpress_load_template_files');

/**THEMING FUNCTIONS & FILTERS|END**/

//HEAD
function bp_quickpress_head() {
	?>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function() {
		jQuery('#quickpress #categories>ul').expandableTree();
		
		
		jQuery('#quickpress #tags').autocomplete("<?php echo get_blog_option($_REQUEST['blog_id'],'siteurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {
			width: jQuery(this).width,
			multiple: true,
			matchContains: true,
			minChars: 3,
		});
	});

	//]]>
	</script>
	<?php
}

//INIT
function bp_quickpress_start() {
	global $bp;
	if (( $bp->current_component == $bp->blogs->slug ) && ($bp->current_action==__( 'quickpress', 'jet-quickpress-slugs' ))) {
		add_action( 'wp_head', 'bp_quickpress_head');
		add_action( 'wp_print_styles', 'bp_quickpress_css' );
		add_action( 'wp_print_scripts', 'bp_quickpress_scripts');
	}
}

function bp_quickpress_capability() {
?>
	<div id="message" class="error">
		<p><?php echo apply_filters('bp_quickpress_no_capability_msg',__( 'You do not have the capability to do this', 'jet-quickpress-slug' )); ?></p>
	</div>
<?php
}


//SCREENS


//creation
function bp_quickpress_screen_write_a_post() {
	global $bp;

	if (!(( $bp->current_component == $bp->blogs->slug ) && (!$bp->action_variables[0]))) return false;
	
	if (!bp_quickpress_user_can('edit_posts')) add_action('bp_quickpress_after_creation_form','bp_quickpress_capability');

	do_action( 'bp_quickpress_screen_write_a_post' );

	bp_core_load_template( apply_filters( 'bp_quickpress_template_write', 'quickpress/create' ) );

}
function bp_quickpress_get_quickpress_post() {
	global $quickpress_post;
	return $quickpress_post;
}
//edition
function bp_quickpress_screen_edit_a_post() {
	global $bp;
	global $quickpress_post;

	if (!(( $bp->current_component == $bp->blogs->slug ) && ($bp->action_variables[0]==__( 'edit', 'jet-quickpress-slugs' )))) return false;

	if ($_REQUEST['post_id']) {
		switch_to_blog($_REQUEST['blog_id']);
		query_posts('p='.$_REQUEST['post_id']);
		restore_current_blog();
		
	}

	global $wp_query;
	global $wpdb;

	$quickpress_post=$wp_query->posts[0];

	if ($quickpress_post)
		if ($bp->loggedin_user->id==$quickpress_post->post_author) $is_author=true;

	if ((!$quickpress_post) || (($is_author) && ((!bp_quickpress_user_can('edit_posts')))) || ((!$is_author) && ((!bp_quickpress_user_can('edit_others_posts'))))) {
		$creation_url = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__( 'quickpress', 'jet-quickpress-slugs' ).'/';

		bp_core_redirect($creation_url);

	}else {	
		do_action( 'bp_quickpress_screen_edit_a_post' );
		bp_core_load_template( apply_filters( 'quickpress_template_edit','quickpress/edit'));
		
	}
	

}


//SAVE POST
//check permissions (you can add filters)
function bp_quickpress_user_can($action) {
	
	if (current_user_can($action)) {
		$can = true;
	}else {
		$can = false;
	}
	$can = apply_filters( 'bp_quickpress_user_can', $can,$action );
	
	return $can;

}

//checks if user can publish a post
function bp_quickpress_post_status_publish() {

	if (bp_quickpress_user_can('publish_posts')) {
		$status='publish';
	}else {
		$status='pending';
	}
	return apply_filters( 'bp_quickpress_post_status_complete', $status );
}

//save
function bp_quickpress_screen_save($creation_url=false,$edition_url=false,$published_url=false,$redirect=true) {
	global $bp;
	global $blog_id;
	global $wpdb;
	
	
	
	if (!(($bp->current_component == $bp->blogs->slug ) && ($bp->current_action==__( 'quickpress', 'jet-quickpress-slugs' )))) return false;
	
	if (!$creation_url)
		$creation_url = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__( 'quickpress', 'jet-quickpress-slugs' ).'/';
		
	if (($bp->action_variables[0]=='switch-blog') && ($_POST['action']=='quickpress-switch-blog')){
	
		$switchblog_url=$creation_url.'?blog_id='.$_POST['blog_id'];
	
		bp_core_redirect($switchblog_url);
	}
	
	if ($bp->action_variables[0]!=__( 'save', 'jet-quickpress-slugs' )) return false;
	
	if (!bp_quickpress_user_can('edit_posts')) {
		bp_core_add_message(__( 'You do not have the capability to do this', 'jet-quickpress-slug' ));
	}
	
	if (($_POST['action']!='quickpress-new') || (!bp_quickpress_user_can('edit_posts'))) {
		bp_core_redirect($creation_url);				
	}

	if (!$edition_url) 
		$edition_url = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__( 'quickpress', 'jet-quickpress-slugs' ).'/'.__( 'edit', 'jet-quickpress-slugs' ).'/';
		
	check_admin_referer( 'jet-quickpress-new-post' );

	$post_content	= $_POST['posttext'];
	$tags			= $_POST['tags'];
	$post_title		= $_POST['title'];
	
	if ((!$post_content) || (!$post_title))  {
		bp_core_add_message(__( 'Please fill the required fields (title,description).', 'jet-quickpress-slug' ));
		bp_core_redirect($creation_url);				
	}
	
	//CHECK DATA
	if (($_POST['action']!='quickpress-new') || (!bp_quickpress_user_can('edit_posts'))) {
		bp_core_redirect($creation_url);				
	}
	
	$post_categories	= $_POST['categories'];
	
	$post = array(
		'post_author'	=> $bp->loggedin_user->id,
		'post_title'	=> $post_title,
		'post_content'	=> $post_content,
		'tags_input'	=> $tags,
		'post_category'	=> $post_categories
	);
	
	//POST ID
	if ($_POST['post_id']) { //existing post
		$post['ID']=$_POST['post_id'];
	}
	//POST STATUS
	if ($_POST['post_id']) { //existing post
		$post['post_status']=bp_quickpress_post_status_publish();
	}else {
		$post['post_status']=apply_filters( 'bp_quickpress_post_status_creation', 'draft' );
	}
	
	$post = apply_filters('bp_quickpress_save_content',$post);
	
	switch_to_blog($_REQUEST['blog_id']);
	
	if (!$post['ID']) { //new post
		$post_id = wp_insert_post($post);
	}else {
		$post_id = wp_update_post( $post );
	}


	
	if (!$post_id) {
		bp_core_add_message( __('Sorry, there has been an error.'), 'error' );
		bp_core_redirect($creation_url);				
	}else {
		if (!$post['ID']) {
			bp_core_add_message( __( 'Post draft saved !  Please check if everything is okay then publish it !','jet-quickpress-slug' ));

			
			$edition_url.='?post_id='.$post_id;


			if ($_REQUEST['blog_id']) {
				$edition_url.='&blog_id='.$_REQUEST['blog_id'];
			}

			bp_core_redirect($edition_url);
			
		}else { //existing post

			if ($post['post_status']!='publish') {
				bp_core_add_message( __( 'Your post has been saved, now wait for the administrator to publish it.','jet-quickpress-slug' ));
			}else {
				bp_core_add_message( __('Post published.', 'jet-quickpress-slug'));
			}
			if ($redirect) {
				if (!$published_url) {

					$post = get_post($post['ID']);

					$published_url = $post->guid;

				}
				//TO FIX bad redirection ?
				bp_core_redirect($published_url);
			}
			
		}
	}
	restore_current_blog();
	
}


//GET BLOG ID
//to fix : any core function instead of this ?
function bp_quickpress_blog_loop_id(){
	global $blogs_template, $bp;

	$blog =& $blogs_template->blog;
	
	return $blog->blog_id;
}

//get blog categories (default=false=current blog)
function bp_quickpress_get_taxonomy($taxonomy,$new_blog_id=false,$parent=false) {
	global $wpdb, $bp,$blog_id;

	if ($new_blog_id) $blog_id = $new_blog_id;
	
	$table_prefix = $wpdb->base_prefix .$blog_id.'_';

	$table_terms = $table_prefix.'terms';
	$table_term_taxonomy = $table_prefix.'term_taxonomy';
	
	if ($parent)
		$more_parent = 'tt.parent='.$parent.' AND ';
	
	$children = $wpdb->prepare("SELECT t.term_id as ID,t.name,t.slug,tt.description,tt.parent,tt.count FROM {$table_terms} t"
	." LEFT JOIN {$table_term_taxonomy} tt ON t.term_id = tt.term_id"
	." WHERE {$more_parent} tt.taxonomy='{$taxonomy}' ORDER BY t.name ASC");

	return $wpdb->get_results( $children );
}

add_action('plugins_loaded','bp_quickpress_screen_save');
add_action( 'bp_setup_nav', 'bp_quickpress_setup_nav' );
add_action( 'plugins_loaded', 'bp_quickpress_start');
add_action('bp_init','bp_quickpress_screen_edit_a_post');

?>