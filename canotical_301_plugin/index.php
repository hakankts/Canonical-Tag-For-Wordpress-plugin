<?php 
/**
 * @package CanonicalTag_301
 * @version 1.2
 */
/*
Plugin Name: Canonical_301
Description: Wordpress allows you to flexibly add Conanical tag or 301 redirect to your content. you can easily update a lot of content in this process.    
Author: Hakan Karataş
Author URI: hakankaratash.com
Version : 1.2
*/

function canonical_seo_menu_hk() {
	global $wpdb;
	?>	

		<form action="<?php the_permalink(); ?>" id="posts-filter" method="post">
		<table width="90%" class="wp-list-table widefat fixed striped posts">
		<tr>
		<td>
			<div style="float:left;">	
				<input type="text" name="s1" value="<?php echo $s1; ?>">	
			</div>	
			<div style="float:left;">			
				<select name="sel1">
					<option value="and">ve</option>
					<option value="or">veya</option>
					<option value="and not">içermesin</option>
				</select>
			</div>
			<div style="float:left;">				
				<input type="text" name="s2" value="<?php echo $s2; ?>">					
			</div>
			<div style="float:left;">				
			<select name="sel2">
				<option value="and">ve</option>
				<option value="or">veya</option>
				<option value="and not">içermesin</option>
			</select>
			</div>
			<div style="float:left;">
			<input type="text" name="s3" value="<?php echo $s3; ?>">					
			</div>
			<div style="float:left;">
				<input type="submit" class="button button-primary save alignright" value="<?php _e( 'Search' ); ?>"></input>
			</div>	
		</td>
		</tr>			
		</table>
		</form>
		<form action="<?php the_permalink(); ?>" id="posts-filter" method="post">		
		<table width="60%" class="wp-list-table widefat fixed striped posts">
		<tr>
			<td style="width: 60px;">
			<div style="width: 60px;" class="SelectBox"><?php _e( 'Select' );?></div> 			
			</td>
			<td><?php _e( 'Tag' );?></td>
			<td><?php _e( 'Title' );?></td>
			<td><?php _e( 'Date' );?></td>
			<td><?php _e( 'Date' ); echo "-";  _e( 'Update' ); ?></td>
		</tr>					
		<?php				
			$s1 	=  $_POST['s1'];
			$s2 	=  $_POST['s2'];
			$s3 	=  $_POST['s3'];
			$sel1 	=  $_POST['sel1'];
			$sel2 	=  $_POST['sel2'];			
			if($s1 && $s2 && $s3)
			{					
				$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type='post' and				 
					post_title LIKE (%s) $sel1 
					(post_title) LIKE (%s) $sel2 
					(post_title) LIKE (%s)", 
					"%".$s1."%", "%".$s2."%" , "%".$s3."%" 
				);	
																			
			}
			elseif ($s1 && $s2 ) {
				$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type='post' and				 
					post_title LIKE (%s) $sel1 
					(post_title) LIKE (%s)", 
					"%".$s1."%", "%".$s2."%"
				);	
			}
			elseif ($s1) {
				$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type='post' and post_title LIKE %s;",
					 '%' .like_escape($s1). '%');			
			}
			else
			{
				die;
			}					
			$search_results = $wpdb->get_results( $sql , OBJECT);											

				foreach( $search_results as $row ) {

				$sql_tag = $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id='".$row->ID."' and (meta_key='_canonical_tag' or meta_key='_rew301')", $offset, 10 );										
				$c_results = $wpdb->get_results( $sql_tag , OBJECT);
				
				$tag ="";
				$tag_value="";	
				 foreach( $c_results as $row_tag ) {
				 	$tag = $row_tag->meta_value;
				 	$tag_value = $row_tag->meta_key;					 	
				 }				    						  							
			?>				
		<tr>		
		<td style="width:10px;">
		<input type="checkbox" id="<?php echo $row->ID;?>" name="check_list[]" class="check_list" value="<?php echo $row->ID;?>" />
		</td>
		<td> <a href="<?php echo $tag;?>"> <?php echo $tag; ?></a> <?php echo $tag_value; ?></td>
		<td> <?php echo $row->post_title;?>	</td>		
		<td> <?php echo date('d/m/Y H:i:s',strtotime($row->post_date));?>	</td>
		<td> <?php echo date('d/m/Y H:i:s',strtotime($row->post_modified));?>	</td>
		
		</tr>
	<?php } ?>
		</table>
		<table width="60%" class="wp-list-table widefat fixed striped posts">	
			<tr>
				<td style="width:10%;">	
				Canonical Tag 
				</td>			
				<td style="width:50%;">
					
					<?php 
						global $post;
						$meta_value = esc_attr( get_post_meta( $post->ID, '_canonical_tag', true ) );
						echo '<input style="width:100%;" type="text" name="canonical_tag" value="'.$meta_value.'">';
					?>

				</td>
				<td style="width:30%;">
					<select name="tag_select">
						<option value="_canonical_tag"><?php _e( 'Conanical Tag' );?></option>
						<option value="_rew301"><?php _e( '301 Redirect' );?></option>						
					</select>
				</td>
			</tr>	
			</table>
		<input style="width:100%; float: left;" id="chk" type="submit" class="button button-primary save alignright" value="<?php _e( 'Add' ); ?>"></input>
		</form>		
		<?php				
				if(!empty($_POST['check_list'])){				
				foreach($_POST['check_list'] as $post_id){
				$meta_key 	= $_POST['tag_select'];
				$veri 		= $_POST['canonical_tag'];				
				$meta_value = esc_attr( get_post_meta( $post_id, $meta_key, true ) );

				//echo $post_id." - ".$meta_key." - ".$veri;
				
				if ( $veri && '' == $meta_value )
				add_post_meta( $post_id, $meta_key, $veri, true );

				if ( $veri != "" && $veri != $meta_value )

				update_post_meta( $post_id, $meta_key, $veri );

				if ( $veri=="" )
				delete_post_meta( $post_id, $meta_key, $meta_value );
				}
				}								
		?>
	</div>
		<script type="text/javascript">
  		(function($) {
    		$(document).ready(function(){
        	$('.SelectBox').click(function(){        					           
		            $( ".check_list" ).prop( "checked", true );		            		            
		    })
		    $('.SelectBox2').click(function(){        					           
		            $( ".check_list" ).prop( "checked", false );		            		            
		    })
	    	});
		})(jQuery);
		</script>
	<?php
}

function canonical_seo_menu() {
	add_options_page( 'Canonical Tag', 'Canonical Tag', 'manage_options', 'canonical_tag', 'canonical_seo_menu_hk' );
}

add_action( 'admin_menu', 'canonical_seo_menu' );


function meta_box_fonksiyonu()
{
	global $post;
	$meta_value = esc_attr( get_post_meta( $post->ID, '_canonical_tag', true ) );
	echo '<input style="width:100%;" type="text" name="canonical_tag" value="'.$meta_value.'">';
}

function canonical_meta_box( $post ) {
	add_meta_box( 
	'meta_box_namesi',
	__( 'Canonical Key Box' ),
	'meta_box_fonksiyonu',
	'post',
	'normal',
	'default'
	);
}

add_action( 'add_meta_boxes_post', 'canonical_meta_box' );

function canonical_save_post()
{
	global $post_id;
	$meta_key = '_canonical_tag';
	$veri = $_POST["canonical_tag"];

	$meta_value = get_post_meta( $post_id, 'canonical_tag', true );

	//echo $post_id." - ".$meta_key." - ".$veri; die;

	if ( $veri && '' == $meta_value )
	add_post_meta( $post_id, $meta_key, $veri, true );

	if ( $veri != "" && $veri != $meta_value )

	update_post_meta( $post_id, $meta_key, $veri );

	if ( $veri=="" )
	delete_post_meta( $post_id, $meta_key, $meta_value );
}

function canonical_data_view(){
	global $wpdb;
	$post_id = get_queried_object_id(); 	
	if($post_id)
	{
		$sql = $wpdb->prepare( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE post_id = '".$post_id."' and meta_key = '_canonical_tag'", $offset, $limit );
			$canonical_results = $wpdb->get_results( $sql );
		
			  foreach( $canonical_results as $canonical_tag ) {
			    $filtered_url = ($canonical_tag->meta_value);
			  }
			// Ensure we're using an absolute URL.
			$current_url  = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			// wp_redirect( $filtered_url ,301); 
			?>
			<link rel="canonical" href="<?php echo esc_url( $filtered_url ); ?>" />												
			<?php
	}	
}

function rew301_view(){
	global $wpdb;	
	$post_id = get_queried_object_id();	 
	//echo $post_id;	
	if($post_id)
	{
		$sql = $wpdb->prepare( "SELECT post_id, meta_key,meta_value FROM $wpdb->postmeta WHERE post_id = '".$post_id."' and meta_key = '_rew301'", $offset, $limit );		
			$canonical_results = $wpdb->get_results( $sql );			
			  foreach( $canonical_results as $canonical_tag ) {
			    $filtered_url = ($canonical_tag->meta_value);			    
			    ($canonical_tag->meta_key);				    				     
			  }						  	
			$current_url  = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			//echo $filtered_url; 			
			if($filtered_url){
			 wp_redirect( $filtered_url ,301); 
			 }					
	}	
}
	
	add_action( 'save_post', 'canonical_save_post', 10, 2 );

	add_action('wp_head', 'canonical_data_view');	 	
	
	add_action( 'template_redirect', 'rew301_view' );
?>
<?php
/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */
 
/**
 * custom option and settings
 */
function canonical_settings_init() {
 register_setting( 'canonical', 'canonical_options' );
 
 // register a new section in the "canonical" page
 add_settings_section(
 'canonical_section_developers',
 __( 'Caninical Tag and 301 rewrite. Wordpress allows you to flexibly add Conanical tag or 301 redirect to your content. you can easily update a lot of content in this process.', 'canonical' ),
 'canonical_section_developers_cb',
 'canonical'
 );
 
}
 
/**
 * register our canonical_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'canonical_settings_init' );
 
/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function canonical_section_developers_cb( $args ) {
 ?>
 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Questions for hakan_kts@yahoo.com', 'canonical' ); ?></p>
 <?php
}
 
// pill field cb
 
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function canonical_field_pill_cb( $args ) {
 
}
 
/**
 * top level menu
 */
function canonical_options_page() {
 // add top level menu page
 add_menu_page(
 'Canonical Tag 301',
 'Canonical 301 Options',
 'manage_options',
 'canonical',
 'canonical_options_page_html'
 );
}
 
/**
 * register our canonical_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'canonical_options_page' );
 
/**
 * top level menu:
 * callback functions
 */
function canonical_options_page_html() {
 // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }
 
 // add error/update messages
 
 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( 'canonical_messages', 'canonical_message', __( 'Settings Saved', 'canonical' ), 'updated' );
 }
 
 // show error/update messages
 settings_errors( 'canonical_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
 // output security fields for the registered setting "canonical"
 settings_fields( 'canonical' );
 // output setting sections and their fields
 // (sections are registered for "canonical", each field is registered to a specific section)
 do_settings_sections( 'canonical' );
 // output save settings button
 //submit_button( 'Save Settings' );
 ?>
 </form>
 </div>
 <?php
}