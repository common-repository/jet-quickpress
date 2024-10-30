<?php get_header() ?>

<!-- TinyMCE -->

<?php 	/* if ( 'en' != $language )
		include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php'); */ ?>
<script type="text/javascript" src="/wp-includes/js/tinymce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "specific_textareas",
		theme : "advanced",
skin:"wp_theme",
		width : "620",
		height: "300",
		auto_resize : true,
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,advhr,|,ltr,rtl",
<?php /*		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft", */?>
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false,

		// Example content CSS (should be your site CSS)
		/* content_css : "css/content.css",*/

		// Drop lists for link/image/media/template dialogs
<? /* 		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js", */ ?>

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
function toggleEditor(id) {
if (!tinyMCE.get(id))
tinyMCE.execCommand('mceAddControl', false, id);
else
tinyMCE.execCommand('mceRemoveControl', false, id);
}	
</script>

<?php
	if ( $concatenate_scripts )
		echo "<script type='text/javascript' src='$baseurl/wp-tinymce.php?c=$zip&amp;$version'></script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/tiny_mce.js?$version'></script>\n";

	if ( 'en' != $language && isset($lang) )
		echo "<script type='text/javascript'>\n$lang\n</script>\n";
	else
		echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
?>

<!-- /TinyMCE -->

	<div id="content">
		<div class="padder">
			<h2><?php _e('Add New Post','jet-quickpress-slug'); ?></h2>
			<?php do_action( 'template_notices' ) // (error/success feedback) ?>
			<p><?php _e('Use this form to quickly add a post to one of your blogs.','jet-quickpress-slug');?><br/>
			<?php _e('If you want more control, add or edit posts using your blog\'s Dashboard !','jet-quickpress-slug');?>
			</p>
			<div id="quickpress">
				<?php do_action( 'bp_quickpress_before_creation_form' ) ?>
				<?php bp_quickpress_switch_blog_form();?>
				<?php bp_quickpress_creation_form();?>
				<?php do_action( 'bp_quickpress_after_creation_form' ) ?>
			</div>
<p style="font-size: 9px;" align="right">Redesign by <a href="http://milordk.ru/">Jettochkin</a></p>			
		</div>

		
	</div>
<?php get_footer() ?>