<?php
/*
Plugin Name: Post Tiles
Plugin URI: http://www.posttiles.com
Description: This plugin displays recent posts as tiles. Posts can choose categories by id and numbeer of posts to display. Example shortcode: [post-tiles] or [post-tiles categories='1,2,4,10' posts='8' excerpt='18'].
Author: Ethan Hackett
Version: 1.3.7
Author URI: http://www.posttiles.com

Copyright (C) 2012 Ethan Hackett

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
// Checks to See Wordpress Version
global $wp_version;

// If it's not compatable with wordpress version 3.0 die and display message.
if ( !version_compare($wp_version,"3.0",">=") )
{
	die("<h2>You need at lease version 3.0 of Wordpress to use the TinCrate plugin.</h2><a href='http:www/wordpress.org' target='_blank'>Visit Wordpress.org to upgrade your current version of Wordpress.");
}

// Adds the function for the admin menu
add_action('admin_menu', 'tc_post_tiles_menu');

// Creates the admin page and title
function tc_post_tiles_menu() {
	add_options_page('Post Tiles', 'Post Tiles', 'manage_options', 'post-tiles-identifier', 'tc_post_tiles_options');
}

// Adds function for declaring options
add_action( 'admin_init', 'register_post_tiles_options' );

function register_post_tiles_options(){
	
	// Registers Non Category Related Settings (it's outside the categories loop)
	register_setting( 'tc-plugin-settings', 'category-key');
	register_setting( 'tc-plugin-settings', 'category-key-jquery');
	register_setting( 'tc-plugin-settings', 'featured-images');
	register_setting( 'tc-plugin-settings', 'responsive-key');
	register_setting( 'tc-plugin-settings', 'animation-style');
	register_setting( 'tc-plugin-settings', 'posttiles-width');
	register_setting( 'tc-plugin-settings', 'posttiles-height');
	register_setting( 'tc-plugin-settings', 'featured-image-width');
	register_setting( 'tc-plugin-settings', 'featured-image-height');
	register_setting( 'tc-plugin-settings', 'excerpt-length');
	register_setting( 'tc-plugin-settings', 'pagination-key');
	
	// Get all Post Categories
	$categories = get_categories();     
	
	//Loop Through Each Category
	foreach($categories as $category) {
		// Get category slug incase of special characters
		$cat_slug = "cat_".$category->slug;
		// Creates a registered setting for each category.
		register_setting('tc-plugin-settings', $cat_slug);
	}
}

// Adds the color picker
add_action('init', 'load_theme_scripts');
function load_theme_scripts() {
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );
}

function tc_post_tiles_options() {
	
	// Checks to make sure the viewer is the admin
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	// Start the page output
	// Get the plugin Url to attatch CSS and images etc. 
	$plugin_url = plugins_url()."/post-tiles";
	wp_enqueue_style( 'post-tiles', $plugin_url.'/post-tiles.css' );
	wp_enqueue_script('post-tiles', $plugin_url.'/post-tiles.js' );
	
	echo "<div class='wrap'>";
	echo "<div id='icon-options-general' class='icon32'></div>
	<h2>Post Tiles:</h2>
	<h3>How to use Post Tiles:</h3>
		<ul>
			<li>1.) To add posts tiles to a page or post copy and paste the following shortcode. Example: <strong>[post-tiles]</strong></li> 
			<li>2.) By default 8 posts are displayed. To change the amount of posts to display, use the <strong>posts=' '</strong> attribute in the shortcode. Example: <strong>[post-tiles posts='10']</strong></li> 
			<li>3.) By default all post categories are called for the tiles. To specify the categories use the <strong>categories=' '</strong> attribute separating them by commas. Example: <strong>[post-tiles categories='1,2,4']</strong></li>
			<li><div class='tc-note'><em><strong>NOTE:</strong> The category id numbers are listed below, next to the category names. You can use both the categories and posts attributes Example: <strong>[post-tiles categories='1,2,4' posts='8']</strong></em></div></li>
			</ul>
	<div id='color-container'>		
	<div id='color-picker-example'>
		<div id='picker'></div>
		<div id='example-post'><div id='example-tile'><big>Example Post Title Here</big></div>Example post excerpt displayed below the title.</div>
	</div>
	<form method='POST' action='options.php'><div id='category-list'><h3>Pick Your Cutom Tile Colors:</h3><em>1.) Click inside each of the category inputs. <br/>2.) Next use the color picker on the left to select the color for that categories tiles.<br/>3.) Save Your Changes</em><ul>";
	
	
	// Define the settings for recording
	settings_fields('tc-plugin-settings');
	
	// Retrieve additional options before the categories loop
	$show_key = get_option('category-key');
	if(empty($show_key)){
		$show_key = "false";
	} 
	$show_key_jquery = get_option('category-key-jquery');
	if(empty($show_key_jquery)){
		$show_key_jquery = "true";
	} 
	$pagination_key = get_option('pagination-key');
	if(empty($pagination_key)){
		$pagination_key = "bottom";
	} 
	$featured_images_key = get_option('featured-images');
	if(empty($featured_images_key)){
		$featured_images_key = "false";
	}
	$animation_style = get_option('animation-style');
	if(empty($animation_style)){
		$animation_style = "up";
	}  
	$responsive_key = get_option('responsive-key');
	if(empty($responsive_key)){
		$responsive_key = "false";
	}
	if ($responsive_key == "true") {
		echo "<style media='screen' type='text/css'>
				#box-dimentions { opacity: 0.5;}
				#responsive-note { display:block !important;}			
			  </style>";
	}
	$posttiles_width = get_option('posttiles-width');
	if(empty($posttiles_width)){
		$posttiles_width = "175";
	}
	$posttiles_height = get_option('posttiles-height');
	if(empty($posttiles_height)){
		$posttiles_height = "175";
	}
	$featured_image_width = get_option('featured-image-width');
	if(empty($featured_image_width)){
		$featured_image_width = "250";
	}
	$featured_image_height = get_option('featured-image-height');
	if(empty($featured_image_height)){
		$featured_image_height = "250";
	}
	$excerpt_length = get_option('excerpt-length');
	if(empty($excerpt_length)){
		$excerpt_length = "12";
	} 
		
	// Get all Post Categories
	$categories = get_categories();  
  
		
	//Loops through each category and displays color inputs.
	foreach($categories as $category) {
		$cat_var = "cat_".$category->name;
		$cat_slug = "cat_".$category->slug;
		// Make lowercase
		$cat_var = strtolower($cat_var);
		$cat_var = str_replace(" ", "_", $cat_var);
		$cat = $category->name;
		$id = $category->cat_ID;
		// Retrieves option value
		$cat_var_value = get_option($cat_slug);
		// Checks the value to see if it's empty. If it is use default.
		if (empty($cat_var_value)){
			$cat_var_value = "#afe2f8";
		}
		
		// Echo out each list Category Name > Id > Input
		echo "<li><strong>".$cat." </strong> <em>(".$id.")</em>
		<input type='text' class='colorwell' name='".$cat_slug."' value='".$cat_var_value."' />
		</li>";
	}
	
	?>
	<li class='category-key-list-item'>
	  <strong><big>Layout</big></strong>
	  
	  <div id="box-dimentions" style="margin-top: 10px; margin-bottom: 10px;">
		  <strong>Set Tile Dimensions</strong> 
		  <br/>
		  <small><em>The default fixed dimensions are 175 by 175.</em></small><br/>
		  <input type="text" name="posttiles-width" value="<?php echo $posttiles_width; ?>" class="small-input" /> <input type="text" name="posttiles-height" value="<?php echo $posttiles_height; ?>" class="small-input" />
	  </div>
	  
	  <div id="box-dimentions" style="margin-top: 10px; margin-bottom: 10px;">
	  	  <strong>Set Featured Image Dimensions</strong> 
	  	  <br/>
	  	  <small><em>It can help if the images are larger than the tiles.</em></small><br/>
	  	  <input type="text" name="featured-image-width" value="<?php echo $featured_image_width; ?>" class="small-input" /> <input type="text" name="featured-image-height" value="<?php echo $featured_image_height; ?>" class="small-input" />
	  </div>
	  
	  <div class='category-key-radio'>
		  <input type="radio" name="responsive-key" <?php if($responsive_key == 'true') echo 'checked="checked"'; ?> value="true" /> On &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="radio" name="responsive-key" <?php if($responsive_key == 'false') echo 'checked="checked"'; ?> value="false" /> Off&nbsp;&nbsp;&nbsp;
	  </div>
	  <div class='category-key-admin'>
		  <strong>Make Post Tiles Responsive</strong> 
		  <br/>
		  <small><em>What is responsive? <a href="http://en.wikipedia.org/wiki/Responsive_web_design" target="_blank">Wikipedia</a></em></small>
	  </div>
	  <div style='clear: both;'></div>
	  <div id='responsive-note' style='display: none; clear: both;' class='tc-note'><em><strong>NOTE:</strong> Turn responsive off to return to fixed tile dimensions.</em></div>
	  
	  
	  <div class='category-key-radio' style='padding-top: 10px;'>
	  	  <input type='text' name='excerpt-length' class='small-input' value='<?php echo $excerpt_length; ?>' />
	  </div>
	  <div class='category-key-admin' style='padding-top: 10px;'>
	  	  <strong>Set Excerpt Length</strong> 
	  	  <br/>
	  	  <small><em>Excerpt is uses word count. Default is 12.</a></em></small>
	  </div>
	</li>
	
	<li class='category-key-list-item'>
	  <div class='category-key-radio'>
		  <input type="radio" name="category-key-jquery" <?php if($show_key_jquery == 'true') echo 'checked="checked"'; ?> value="true" /> On &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="radio" name="category-key-jquery" <?php if($show_key_jquery == 'false') echo 'checked="checked"'; ?> value="false" /> Off&nbsp;&nbsp;&nbsp;
	  </div>
	  <div class='category-key-admin'>
		  <strong>jQuery Category Sorting & Animation</strong> 
		  <br/>
		  <small><em>Turns the jQuery features on or off</em></small>
	  </div>
	</li>
	
	<li class='category-key-list-item'>
	  <div class='category-key-radio'>
		  <input type="radio" name="category-key" <?php if($show_key == 'true') echo 'checked="checked"'; ?> value="true" /> Show &nbsp;&nbsp;
		  <input type="radio" name="category-key" <?php if($show_key == 'false') echo 'checked="checked"'; ?> value="false" /> Hide
	  </div>
	  <div class='category-key-admin'>
		  <strong>Display the Category Key</strong> 
		  <br/>
		  <small><em>Show category names and colors</em></small>
		  <!-- An alert that this feature requires jQuery be turned on -->
		  <?php if($show_key_jquery == 'false') echo '<br/><small class="attention"><em><strong>ATTENTION:</strong> This featured requires jQuery be turned on</em></small>'; ?>
	  </div>
	</li>
	
	<li class='category-key-list-item'>
	  <div class='category-key-radio'>
		  <select name="pagination-key" id="animation-style">
		    <option value="none" <?php if($pagination_key == 'none') echo 'selected="selected"'; ?>>None</option>
		    <option value="top" <?php if($pagination_key == 'top') echo 'selected="selected"'; ?>>Top</option>
		    <option value="bottom" <?php if($pagination_key == 'bottom') echo 'selected="selected"'; ?>>Bottom</option>
		    <option value="both" <?php if($pagination_key == 'both') echo 'selected="selected"'; ?>>Both</option>
		  </select>
	  </div>
	  <div class='category-key-admin'>
		  <strong>Display Pagination For Tiles</strong> 
		  <br/>
		  <small><em>Pagination enables the viewing of overflow tiles.</em></small>
	  </div>
	</li>
	
	<li class='category-key-list-item'>
	  <div class='category-key-radio'>
		  <input type="radio" name="featured-images" <?php if($featured_images_key == 'true') echo 'checked="checked"'; ?> value="true" /> On &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  <input type="radio" name="featured-images" <?php if($featured_images_key == 'false') echo 'checked="checked"'; ?> value="false" /> Off&nbsp;&nbsp;&nbsp;
	  </div>
	  <div class='category-key-admin'>
		  <strong>Use Featured Images For Tiles</strong> 
		  <br/>
		  <small><em>Uses the posts assigned featured image.</em></small>
		  <!-- An alert that this feature requires jQuery be turned on -->
		  <?php if($show_key_jquery == 'false') echo '<br/><small class="attention"><em><strong>ATTENTION:</strong> This featured requires jQuery be turned on</em></small>'; ?>
	  </div>
	</li>
	
	<li class='category-key-list-item'>
	  <div class='category-key-radio'>
		  <select name="animation-style" id="animation-style">
		    <option value="up" <?php if($animation_style == 'up') echo 'selected="selected"'; ?>>Slide Up</option>
		    <option value="down" <?php if($animation_style == 'down') echo 'selected="selected"'; ?>>Slide Down</option>
		    <option value="left" <?php if($animation_style == 'left') echo 'selected="selected"'; ?>>Slide Left</option>
		    <option value="right" <?php if($animation_style == 'right') echo 'selected="selected"'; ?>>Slide Right</option>
		    <option value="fade" <?php if($animation_style == 'fade') echo 'selected="selected"'; ?>>Fade Toggle</option>
		  </select>
	  </div>
	  <div class='category-key-admin'>
		  <strong>Feature Image Animation Type</strong> 
		  <br/>
		  <small><em>Pick from one of the preselected animations</em></small>
		  <!-- An alert that this feature requires jQuery be turned on -->
		  <?php if($show_key_jquery == 'false') echo '<br/><small class="attention"><em><strong>ATTENTION:</strong> This featured requires jQuery be turned on</em></small>'; ?>
	  </div>
	</li>
	
		  
	<li id="submit-button"><input type='submit' class='button-primary' value='<?php _e('Save Changes') ?>'/></li></ul>
	</div></form></div>
	<div id='donate-box'>
		<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
		<input type='hidden' name='cmd' value='_s-xclick'>
		<input type='hidden' name='hosted_button_id' value='HH7L4BWHALHLA'>
		<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
		<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
		</form>
		<h3>Consider Donating</h3>
		<em>Consider buying me a cup of coffee for the development of this plugin.</em>
	</div>
	<div id='tc-footer'>The Post Tiles Wordpress plugin was created by <a href="http://www.ethanhackett.com" target="_blank" title="Designed and Developed by Ethan Hackett www.ethanhackett.com">Ethan Hackett</a> as part of <a href="http://www.tincrate.com/plugins" target="_blank">TinCrate</a>.</div></div>
	<?php
}
// End Admin Page




/* ***************************** */
// Start Frontend Code
/* ***************************** */
// Sets the "tc_post_tiles" function as a shortcode [tc_post_tiles]
add_shortcode('post-tiles','tc_post_tiles');

// Runs Output
function tc_post_tiles($atts,$content=null)
{
	shortcode_atts( array('categories'=>'', 'posts'=>'', 'excerpt'=>''), $atts);
	// EXAMPLE USAGE: [post-tiles categories="1,2,3,4" posts="8"]
	   
	   // Defaults
	   extract(shortcode_atts(array(
	      "categories" => '', 
	      "posts" => '',	
	      "excerpt" => ''
	   ), $atts));
	   
	   // Get and set the excerpt length
	    $excerpt_shortcode = $atts['excerpt'];
	    if (isset($excerpt_shortcode)) {
	    	$excerpt_length =  $excerpt_shortcode;
	    } else {
	    	$excerpt_length = get_option('excerpt-length');
	    	if(empty($excerpt_length)){
	    		  $excerpt_length = "12";
	    	}	
	    }
	   
	   // Pagination starting point
	   if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
       elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
       else { $paged = 1; }
	   
	   // Configure Categories For Query
	   $cat_num = $atts['categories'];		
	   if (isset($cat_num)) {
	       $cat_query = '&cat='.$atts['categories'];
	   }
	 
	   // Configure Number of Posts For Query if empty use 8.
	   $posts = $atts['posts'];		
	   if (isset($posts)) {
	       $posts_query = 'posts_per_page='.$atts['posts'];
	   }
	   else {
		   $posts_query = 'posts_per_page=4';
	   }
	   
	   // Configure Both Categories and Number of Posts For Query
	   $the_query = $posts_query.$cat_query.'&paged='.$paged;
	   
	   // Run the query              
	   query_posts($the_query);
	   
	   // Reset and setup variables
	   $output = '';
	   $temp_title = '';
	   $temp_link = '';
	   
	   // Retrives The On/Off for jQuery
	   $show_key_jquery = get_option('category-key-jquery');
	   
	   // Retrives The On/Off for jQuery
	   $responsive_key = get_option('responsive-key');
	   
	   // Retrieve Animation Style
	   $animation_style = get_option('animation-style');
	   if(empty($animation_style)){
	   	  $animation_style = "up";
	   }
	   
	   // Retrieves Tile Width
	   $posttiles_li_width = get_option('posttiles-width');
	   if(empty($posttiles_li_width)){
	   	  $posttiles_li_width = "175";
	   }
	   $posttiles_width = $posttiles_li_width-20;
	   
	   // Retrieves Tile Height
	   $posttiles_li_height = get_option('posttiles-height');
	   if(empty($posttiles_li_height)){
	   	  $posttiles_height = "175";
	   }
	   $posttiles_height = $posttiles_li_height-20;
	   
	   // Retrieve Featured Image Height
	   $featured_image_height = get_option('featured-image-height');
	   if(empty($featured_image_height)){
	   	  $featured_image_height = "250";
	   }
	   // Retrieves Featured Image Width
	   $featured_image_width = get_option('featured-image-width');
	   if(empty($featured_image_width)){
	   	  $featured_image_width = "250";
	   }
	   
	   
	   
	   ?>
	   <style media="screen" type="text/css">
	   			ul#post-tiles li {
	   				width: <?php echo $posttiles_li_width; ?>px;
	   				height: <?php echo $posttiles_li_height; ?>px;
	   			}
	   			ul#post-tiles li a {
	   				width: <?php echo $posttiles_width; ?>px;
	   				height: <?php echo $posttiles_height; ?>px;
	   			}		
	   </style>
	   <?php 
	   
	   // If jquery is on(true) then output the following
	   if ($show_key_jquery == "true"){
	   	   // Get Plugin Url 
	   	   $plugin_url = plugins_url()."/post-tiles";
	   	   // Attatch Javascript
	   	   echo "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>";
	   	   echo "<script type='text/javascript' src='".$plugin_url."/post-tiles-frontend.js'></script>";  
	   	   // Add CSS
	   	   ?>
	   	   <style media="screen" type="text/css">
	   	   			ul#post-tiles li, #category-key {
	   	   				opacity: 0;
	   	   			}		
	   	   </style><?php 
	   } 
	   
	   // Retrieve Pagination
	   $pagination_key = get_option('pagination-key');
	   if(empty($pagination_key)){
	   	  $pagination_key = "bottom";
	   }
	   if ($pagination_key == "top" || $pagination_key == "both" || $pagination_key == "bottom") {
		   	// Build out the pagination
		   	 global $wp_query;  
		   	 // Get the total number of pages
		   	$total_pages = $wp_query->max_num_pages;
		   	// Fix the url leading slash
		   	// Get the url
		   	$url = get_permalink();
		   	// get the last character of the url string either / or not
		   	$lastchar = substr( $url, -1 );
		   	// if the last character is or isn't a / configure the pagination formating accordingly
		   	if ( $lastchar != '/' ){
		   	   $format = "/page/%#%";
		   	} else {
		   	   $format = "page/%#%";
		   	}
		   	// If the total of pages is greater than one create the pagination
		   	if ($total_pages > 1){
		   	   $current_page = max(1, get_query_var('paged'));
		   	   $pagination = paginate_links(array(
		   	   'base' => get_pagenum_link(1) . '%_%',
		   	   'format' => $format,  
		   	      'current' => $current_page,  
		   	      'total' => $total_pages,  
		   	    ));
		   	    $pagination ="<div class='posttiles-pagination'>".$pagination."</div>";  
		   	} else {
		   		$pagination = "<!-- There is no Post Tiles pagination because there aren't enough posts for pagination -->";
		   	}
	   } else {
		 $pagination = "<!-- Post Tiles pagination is disabled in the settings -->";
	   }
	   if ($pagination_key == "top") {
	   		$top_pagination = $pagination;
	   } elseif ($pagination_key == "bottom")  {
		  	$bottom_pagination = $pagination;
	   } elseif ($pagination_key == "both") {
			$top_pagination = $pagination;
			$bottom_pagination = $pagination;
	   } else {
			$top_pagination = $pagination;
			$bottom_pagination = $pagination;
	   }
	   	   	   
	   // the loop
	   if (have_posts()) : while (have_posts()) : the_post();
	   
	   	  // Get Post Elements
	      $temp_title = get_the_title($post->ID);
	      $temp_link = get_permalink($post->ID);
	      $temp_excerpt = get_the_excerpt($post->ID);
		  
		  // Check to see if the excerpt was not defined thus empty
		  if ($excerpt_length == "") {
		  	   // Set excerpt length to 19 as default.
		      $excerpt_length =  '19';
		  }
		  
		  // Truncates the excerpt length (Not using default wordpress for plugin conflicts)
		  $words = explode(' ', $temp_excerpt);
		  if (count($words) > $excerpt_length){
		    array_splice($words, $excerpt_length);
		    $temp_excerpt = implode(' ', $words);
		  	$temp_excerpt .= '&hellip;';
		  }
			 
	      // Grabs the categories then assigns the first category in the string to the class.
	      $category = get_the_category();
	      $category_name = $category[0]->cat_name;
	      $category_slug = $category[0]->slug;
	      
	      // Recovering Saved Color Values
	      // Define the Settings for recording
	      $cat_var = "cat_".$category_slug;
	      // Make lowercase
	      //$cat_var_value = strtolower($cat_var);
	      //$cat_var = str_replace(" ", "_", $cat_var);
	      // Retrieve the option "hexadecimal value" for this category
	      $cat_var_value = get_option($cat_var);
	      // Checks the value to see if it's empty. If it is use default.
	      if (empty($cat_var_value)){
	      	$cat_var_value = "#afe2f8";
	      }
	      
	      // Retrieve the option feature image.
	      $featured_images_key = get_option('featured-images');
	      // See if Featured image is true.
	      // Clear features_style variable.
	      $featured_style = "";
	      // Get the post ID.
	      $theID = get_the_ID();
	      // Get The Date
	      $theDate = get_the_date();
	      // Reset featured image bottom border fix
	      // There's a margin issue on responsive so this fixes the bottom gap with border
	      $featured_border = "border-bottom: 3px solid ".$cat_var_value.";border-right: 2px solid ".$cat_var_value.";";
	      // If there is a featured image.
		  if ($featured_images_key == "true") {
		      if ( has_post_thumbnail()) {
		      	  // Retrieve the featured image.
			      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), array($featured_image_width,$featured_image_height));
			      // Strip featured image down to the url.
			      $url = $thumb['0'];
			      $featured_style = "style='background: url(".$url.") 0 0;' class='featured-image ".$animation_style." ".$cat_var."'";
		      } else {
				  $featured_style = "class='".$cat_var."'";
			  }
	      }
	      // Each output creates the li > link > title > exerpt
	      $output .= "<li ".$featured_style." id='".$theID."'><a href='$temp_link' class='".$cat_var."' style='background-color: $cat_var_value; $featured_border'><h3>$temp_title </h3>$temp_excerpt</a></li>\n";
	      // Each output_key creates a li > category color block > category name
	          
	   endwhile; else:
	   
	      $output .= "No posts available. Double check your the post-tiles shortcode, selected categories, and number of posts.";
	      
	   endif;
	   
	   // Reset the query so it doesn't interupt the normal query
	   wp_reset_query();
	   
	   // If responsive it true add responsive class
	   if ($responsive_key == "true") {
	   		$responsive = "class='responsive'";
	   } 
	   
	   // Display Key
	   $show_key = get_option('category-key');
	   $show_key_jquery = get_option('category-key-jquery');
	   $featured_images_key= get_option('featured-images');
	   
	   // If it's empty then the default value is false (default) 
	   if(empty($show_key)){
	   	$show_key == "false";
	   }
	   // If it's true then build the key.
	   if ($show_key == "true"){
	   		$ca_args = array(
			   // $cat_num pulls from shortcode
			  'include' => $cat_num
			  );
			//List categories  
			$categories = get_categories($ca_args);  
			//Loops through each category and displays key and color.
			foreach($categories as $category) {
					// Set's category names
					$cat_var = $category->name;
					// Sets the slug
					$cat_slug = $category->slug;
					// Cleans up category names that have spaces
					$cat_var_key = "cat_".$cat_slug;
					// Get's the category options which are the hexadecimal colors
					$cat_var_key_val = get_option($cat_var_key);
					// loops through the each category and prints them
					$key_items .= "<a href='#' class='key' id='".$cat_var_key."' style='background-color:".$cat_var_key_val.";'>".$cat_var."</a>\n";
			}
			
			// Creates the finished key
			$key_finished = "<div id='category-key' ".$responsive.">\n".$key_items."<a href='#' class='key' id='category-all'>All</a></div>\n\n";
	   } else {
	   		$key_finished = "<!-- Category Key is turned off. See the admin settings for Post Tiles -->";
	   }
	   	  
	   return '<div id="post-tiles-container">'.$top_pagination.$key_finished.'<ul id="post-tiles" '.$responsive.'>'.$output.'</ul>'.$bottom_pagination.'</div>'; 
	   
	   $plugin_url = plugins_url()."/post-tiles";
}

// Add the Scripts
add_action('wp_enqueue_scripts', 'post_tiles_stylesheet');

// Adds CSS
function post_tiles_stylesheet() {
	$plugin_url = plugins_url()."/post-tiles";
	wp_enqueue_style( 'post-tiles', $plugin_url.'/post-tiles.css' );
//	wp_enqueue_script('post-tiles', $plugin_url.'/post-tiles-frontend.js' );
} 
?>