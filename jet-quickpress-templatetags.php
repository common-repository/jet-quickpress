<?php

//choose blog form
function bp_quickpress_switch_blog_form() {
		global $bp;
		global $blog_id;
		
		$form_action = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__('quickpress', 'jet-quickpress-slugs' ).'/switch-blog';
		$form_action = apply_filters('bp_quickpress_form_switchblog_url',$form_action);

?>
	<form id="bp_quickpress_blog_select" name="bp_quickpress_new_post" method="post" action="<?php echo $form_action; ?>">
		<p>
			<label for="blog"><h3><?php _e('Blog','jet-quickpress-slug'); ?></h3></label>
			<?php if ( bp_has_blogs(array('user_id'=>$bp->loggedin_user->id)) ) :
					
			if ($_GET['blog_id']) $blog_id = $_GET['blog_id'];
			
			?>
				<select name="blog_id" id="blog_id" onchange="this.form.submit()">
					<?php 
					
					while ( bp_blogs() ) : bp_the_blog();
							
						unset($selected);
						$thisblogid=bp_quickpress_blog_loop_id();
						if ($thisblogid==$blog_id) $selected=" SELECTED";
					echo'<option value="'.$thisblogid.'" '.$selected.'>'.bp_get_blog_name().'</option>';

					endwhile; ?>
				</select>
			<?php endif; ?>
		</p>
		<input type="hidden" name="action" value="quickpress-switch-blog"/>
	</form>
<?php
}

//creation post
function bp_quickpress_creation_form() {
	global $bp;
	
	if (!bp_quickpress_user_can('edit_posts')) return false;
	
	$form_action = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__('quickpress', 'jet-quickpress-slugs' ).'/'.__( 'save', 'jet-quickpress-slugs' );
	$form_action = apply_filters('bp_quickpress_form_creation_url',$form_action);
?>

	<form id="bp_quickpress_new_post" name="bp_quickpress_new_post" class="standard-form" method="post" action="<?php echo $form_action; ?>">
		<p>
			<?php bp_quickpress_creation_form_content($_REQUEST['blog_id']);?>
			
			<?php do_action('bp_quickpress_creation_form');?>
			
			<?php wp_nonce_field( 'jet-quickpress-new-post' ); ?>
			<input type="hidden" name="action" value="quickpress-new"/>
			<?php
				if ($_REQUEST['blog_id'])
					echo'<input type="hidden" name="blog_id" value="'.$_REQUEST['blog_id'].'"/>';
			?>
			<input id="save" type="submit" value="<?php _e('Save','jet-quickpress-slug');?>"/>
		</p>
		
	</form>

<?php

}


	function bp_quickpress_creation_form_content($blog_id,$tags=true,$cats=true) {
	?>
		<p>
			<label for="title"><h3><?php _e('Title','jet-quickpress-slug');?></h3></label>
			<input type="text" name="title" id="title"/>
		</p>
		<p>
			<label for="posttext"><?php _e('Description','jet-quickpress-slug');?></label>
			<textarea name="posttext" id="posttext" rows="10" style="width:650px;"></textarea>
		</p>
		
		<?php
		
		if ($tags)
			bp_quickpress_tags($blog_id);
		if ($cats)
			bp_quickpress_categories($blog_id);
		
		do_action('bp_quickpress_creation_form_content');?>
		
	<?php
	}


//edition post
function bp_quickpress_edition_form() {
	global $bp;
	global $blog_id;

	if (!bp_quickpress_user_can('edit_posts')) return false;
	
	$form_action = $bp->loggedin_user->domain . $bp->blogs->slug . '/'.__( 'quickpress', 'jet-quickpress-slugs' ).'/'.__( 'save', 'jet-quickpress-slugs' );
	$form_action = apply_filters('bp_quickpress_form_creation_url',$form_action);
?>
	<form id="bp_quickpress_edit_post" class="standard-form" name="bp_quickpress_edit_post" method="post" action="<?php echo $form_action; ?>">
		<p>
			<label for="blog_id"><h3><?php _e('Blog', 'jet-quickpress-slug');?></h3></label><br/>
			<em>
			<?php
			
			if ($blog_id!=$_REQUEST['blog_id']) {
				$name = get_blog_option($_REQUEST['blog_id'],'blogname');
			}else {
				$name = get_blog_option($blog_id,'blogname');
			}
			echo $name;
			?>
			</em>
		</h3>
		</p>
		<?php bp_quickpress_edition_form_content($_REQUEST['blog_id']);?>
		<p>
		
			<?php wp_nonce_field( 'jet-quickpress-new-post' ); ?>
			<input type="hidden" name="post_id" value="<?php the_ID(); ?>"/>
			<input type="hidden" name="action" value="quickpress-new"/>
			<?php
				if ($_REQUEST['blog_id'])
					echo'<input type="hidden" name="blog_id" value="'.$_REQUEST['blog_id'].'"/>';
			?>
			<?php
		if (!bp_quickpress_user_can('publish_posts')) {
			$submit_text = __('Submit for Review');
		}else {
			$submit_text = __('Publish');
		}
		?>
			<input id="publish" type="submit" value="<?php echo $submit_text;?>"/>
		</p>
	</form>
<?php
}

	function bp_quickpress_edition_form_content($blog_id,$tags=true,$cats=true) {
	
	?>
	
		<p>
		<label for="title"><h3><?php _e('Title','jet-quickpress-slug');?></h3></label>
		<input type="text" name="title" id="title" value="<?php the_title(); ?>"/>
		</p>
		<p>
		<label for="posttext"><h3><?php _e('Description', 'jet-quickpress-slug');?></h3></label>
		<textarea name="posttext" id="posttext" rows="3"><?php echo esc_html(get_the_content()); ?></textarea>
		</p>
		<?php 
		if ($tags)
			bp_quickpress_tags($blog_id);
		if ($cats)
			bp_quickpress_categories($blog_id);
			
		do_action('bp_quickpress_edition_form_content');
			
		?>
	<?php
	}



//TAGS
function bp_quickpress_get_post_tags($blog_id=false) {
	global $post;
	switch_to_blog($blog_id);
	$post_tags = wp_get_post_tags($post->ID);
	restore_current_blog();
	
	return $post_tags;
}

//display tags
function bp_quickpress_tags($blog_id=false) {
	$post_tags = bp_quickpress_get_post_tags($blog_id);
	
	if ($post_tags) {
		foreach($post_tags as $tag) {
			$taglist[]=$tag->name;
		}
		$tags_str = implode(',',$taglist);
	}
	
	$output.='<p>';
	$output.='<label for="tags">'.__('Tags','jet-quickpress-slug').'</label>';
	$output.='<input type="text" name="tags" id="tags" value="'.$tags_str.'"/>';
	
	echo $output;
?>
	
<?php
}

//CATS
function bp_quickpress_get_post_cats($blog_id=false) {
	global $post;
	switch_to_blog($blog_id);
	$post_cats = wp_get_post_categories($post->ID);
	restore_current_blog();
	
	return $post_cats;
}

function bp_quickpress_categories($blog_id=false) {
	global $post;
	
	$post_cats = bp_quickpress_get_post_cats($blog_id);
	
	$array_categories = bp_quickpress_get_taxonomy('category',$blog_id);
	
	$treeset = new TreeSet();
	
	
	$categories_tree = $treeset -> drawTree($treeset -> buildTree($array_categories),$post_cats,'bp_quickpress_format_category');

	$output.='<p>';
	$output.='<label for="categories">'.__('Categories', 'jet-quickpress-slug').'</label>';
	$output.='<div id="categories">';
	$output.=$categories_tree;
	$output.='</div>';
	$output.='</p>';

	echo $output;
?>
	
<?php
}


//format category item
function bp_quickpress_format_category($cat) {

	global $checked_branches;

	if ( (is_array($checked_branches)) &&in_array($cat->ID,$checked_branches) ) {
		$checked=' CHECKED';
	}elseif (($cat->ID==1) && (empty($post_cats))){ //default
		$checked=' CHECKED';
	}


	$html='<li class="category" id="cat-'.$cat->ID.'"><div class="folder icon"></div><input type="checkbox" name="categories[]" value="'.$cat->ID.'"'.$checked.'> <span class="text">'.$cat->name.'</span>';
	
	return $html;
}

//////////////TEMPLATES////////////////////


function bp_quickpress_enqueue_url($file){
	// split template name at the slashes
	
	$stylesheet_path = get_stylesheet_directory_uri();
	$suffix = explode($stylesheet_path,$file);	
	
	$suffix_str=$suffix[1];
	
	$file_path_to_check = BP_QUICKPRESS_PLUGIN_DIR . '/theme'.$suffix_str;
	$file_url_to_return = BP_QUICKPRESS_PLUGIN_URL . '/theme'.$suffix_str;

	if ( file_exists($file)) {
		return $file;
	}elseif ( file_exists($file_path_to_check)) {
		return $file_url_to_return;
	}
}
add_filter( 'bp_quickpress_enqueue_url', 'bp_quickpress_enqueue_url' );

/**
 * Check if template exists in style path, then check custom plugin location (code snippet from MrMaz)
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */
function bp_quickpress_locate_template( $template_names, $load = false ) {

	if ( !is_array( $template_names ) )
		return '';

	$located = '';
	foreach($template_names as $template_name) {

		// split template name at the slashes
		$paths = explode( '/', $template_name );
		
		// only filter templates names that match our unique starting path
		if ( !empty( $paths[0] ) && 'quickpress' == $paths[0] ) {


			$style_path = STYLESHEETPATH . '/' . $template_name;
			$plugin_path = BP_QUICKPRESS_PLUGIN_DIR . "/theme/{$template_name}";

			if ( file_exists( $style_path )) {
				$located = $style_path;
				break;
			} else if ( file_exists( $plugin_path ) ) {
				$located = $plugin_path;
				break;
			}
		}
	}

	if ($load && '' != $located)
		load_template($located);

	return $located;
}

/**
 * Filter located BP template (code snippet from MrMaz)
 *
 * @see bp_core_load_template()
 * @param string $located_template
 * @param array $template_names
 * @return string
 */
function bp_quickpress_filter_template( $located_template, $template_names ) {

	// template already located, skip
	if ( !empty( $located_template ) )
		return $located_template;

	// only filter for our component
	if ( $bp->current_component == $bp->quickpress->slug ) {
		return bp_quickpress_locate_template( $template_names );
	}

	return '';
}
add_filter( 'bp_located_template', 'bp_quickpress_filter_template', 10, 2 );

/**
 * Use this only inside of screen functions, etc (code snippet from MrMaz)
 *
 * @param string $template
 */
function bp_quickpress_load_template( $template ) {
	bp_core_load_template( $template );
}

?>