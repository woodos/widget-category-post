<?php 
/*
Plugin Name: widget category display Widget
Author:woodos.
Author URI:http://www.echosite.it
Description:This plugin is used to show post under particular category.
Version: 1.0
Author URI:http://www.echosite.it
*/


/**
 * cplw_scripts_method() function includes required jquery files.
 *
 */
function cplw_scripts_method() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('cycle_js', plugins_url('/js/jquery.cycle.all.js', __FILE__));
}

/** Tell WordPress to run cplw_scripts_method() when the 'wp_enqueue_scripts' hook is run. */
add_action('wp_enqueue_scripts', 'cplw_scripts_method'); 

/**
 * cplw_stylesheet() function includes required css files.
 *
 */
function cplw_stylesheet() {
    wp_register_style( 'main-style', plugins_url('/css/main.css', __FILE__) );
    wp_enqueue_style( 'main-style' );
}

/** Tell WordPress to run cplw_scripts_method() when the 'cplw_stylesheet' hook is run. */
add_action( 'wp_enqueue_scripts', 'cplw_stylesheet' ); 


/**
 * cplw_required_css() function includes required css files for admin side.
 *
 */
add_action( 'admin_head', 'cplw_required_css' );

function cplw_required_css() {
    wp_register_style( 'cplw_css', plugins_url('/css/basic.css', __FILE__) );
    wp_enqueue_style( 'cplw_css' );
}

 
class Category_Post_List_widget extends WP_Widget 
{
	function Category_Post_List_widget() {
		parent::WP_Widget(false,$name=" widget category display",array('description'=>'fill in the details view after putting in your ideal location'));
	}
	
	/**
	 * Displays category posts widget on blog.
	 *
	 * @param array $look current settings of widget .
	 * @param array $args of widget area
	 */
	function widget($args,$look) {
		global $post;
		$post_old = $post; // Save the post object.
		extract($args);

		// If not title, use the name of the category.
		if( !$look["widget_title"] ) 
		{
			$category_info = get_category($look["cat"]);
			$look["widget_title"] = $category_info->name;
  		}
		
		
	
		//sort by
		$valid_sort_by = array('date', 'title', 'comment_count', 'rand');
		if ( in_array($look['sort_by'], $valid_sort_by) ) 
		{
		    $sort_by = $look['sort_by'];

		    $sort_order = (bool) isset($look['asc_sort_order']) ? 'ASC' : 'DESC';
		} else 
		{
		    // by default, display latest first
		    $sort_by = 'date';
		    $sort_order = 'DESC';
		}
		// Get effect for front end
		$effects = $look['effects']	;
		$effects_time = $look['effects_time'];

		// Get  post info.
		$cat_posts = new WP_Query(
		"showposts=" . $look["num"] . 
		"&cat=" . $look["cat"] .
		"&orderby=" . $sort_by .
		"&order=" . $sort_order
		);

		// Excerpt length 
		$new_excerpt_length = create_function('$length', "return " . $look["excerpt_length"] . ";");
		if ( $look["excerpt_length"] > 0 )
			add_filter('excerpt_length', $new_excerpt_length);
		$arrExlpodeFields = explode(',',$look['display']);
		
		echo $before_widget; 
		// Widget title
		echo $before_title;
		if( in_array("link",$arrExlpodeFields) )
			echo '<a href="' . get_category_link($look["cat"]) . '">' . $look["widget_title"] . '</a>';
		else
			echo $look["widget_title"];
		echo $after_title;

		$i = 0;
		global $wp_query;
		$total_posts = $wp_query->found_posts;
		
		// Post list
		
		
	

		?>
		<script type="text/javascript">
	        jQuery(document).ready(function() {
	        		var effect = '<?php echo $effects; ?>';
	                if(effect != 'none')
	                {
	                    jQuery('.news_scroll').cycle({ 
	                        fx: effect, 
	                        timeout: '<?php echo $effects_time; ?>',
	                        random:  1
	                    }); 
	                }
	            });
	    </script>
    
	   	<div <?php if($look['widget_h']!=''){ ?> style="height:<?php echo $look['widget_h'] ?>px !important; width:<?php echo $look['widget_w'] ?>px !important" <?php } ?>>
			<div class="ovflhidden news_scroll">				
				<?php while ( $cat_posts->have_posts() )
					{
						$cat_posts->the_post(); ?>						
		            		<div class="fl newsdesc">
                            
                            <?php $class_title = "class='".$look['widget_class_title_display']."'"; ?>
                            <?php $display_title =  "<".$look['widget_title_display']." ".$class_title." >" ?>
                            <?php $display_title_close =  "</".$look['widget_title_display'].">" ?>
								
								
								<?php echo $display_title ?>
                                <?php if($look["widget_target_link"]!=''){ 
								 $target='target="'.$look["widget_target_link"].'"';
								}else{
									
									$target='';
									}
								if($look["widget_rel_link"] !=''){
									$rel='rel="'.$look["widget_rel_link"].'"';
									}else{
									$rel ='';	
										}
								?>
                                <a class="post-title" href="<?php the_permalink(); ?>" <?php echo $target ?>  <?php echo $rel ?> title="<?php _e('Permanent link to') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a><?php echo $display_title_close ?>
                            <?php if($look["widget_date"] == "1"){ ?> 
                            <p class="post_date" ><?php the_time($look['date_format']); ?></p>
                             <?php } ?>     
                                
                                
                               <?php if($look["widget_thumbnail_display"]=="1"){
								   if ( has_post_thumbnail()) {	
								    ?>
                               
                               <?php if($look["widget_thumbnail_display_position"]=="0"){
								   $class_thumb = "sx";
							   }else{
								    $class_thumb = "dx";
							   } ?>
								<div class="post_thumbnail_<?php echo $class_thumb ?>">
							<?php
							
							
							 if($look["link_thumbnail_display"] == "1"){
								 
							 	 
								
						 if($look["widget_target_link"]!=''){ 
								 $target='target="'.$look["widget_target_link"].'"';
							}else{
									
									$target='';
									}
								if($look["widget_rel_link"] !=''){
									$rel='rel="'.$look["widget_rel_link"].'"';
									}else{
									$rel ='';	
										}
								?>
                                <a class="post-title" href="<?php the_permalink(); ?>" <?php echo $target ?>  <?php echo $rel ?> title="<?php _e('Permanent link to') ?> <?php the_title_attribute(); ?>">
                                 <?php } ?>
                               <?php
							   
					
$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb', array($instance["thumb_w"],$instance["thumb_h"] ), true );
$url = $thumb['0'];
?>
<img  src="<?php echo $url; ?>" alt="<?php the_title_attribute(); ?>" width="<?php echo $look["thumb_w"]; ?>" height="<?php echo $look["thumb_h"]; ?>" title="<?php the_title_attribute(); ?>" />
	<?php if($look["link_thumbnail_display"] == "1"){ ?> </a> <?php } ?>
		</div>
                                <?php 
 }
			} ?>
              <?php if($look["widget_excerpt"] == "1"){ ?> 
              <?php    if($look["widget_d_excerpt"] != ''){ $style_excerpt = 'style="text-align:'.$look["widget_d_excerpt"].'"'; }else{$style_excerpt = '';}?>    
                
                  <span <?php echo $style_excerpt ?> <?php if($look["widget_class_excerpt"] != ''){ echo 'class="'.$look["widget_class_excerpt"].'"'; } ?>><?php echo(get_the_excerpt()); ?></span         
					><?php }  ?>		
					<?php if($look["widget_author"]=="1"){ ?>
                                <?php $text_author = $look["text_widget_author"] ?>
                                <br/> <p class="post_author" ><?php  echo $text_author." ".get_the_author(); ?></p>
								<?php }  ?>
                                
								
							</div>												
						<?php	
					} 
				echo $after_widget;
				?>
		
		<?php 
		remove_filter('excerpt_length', $new_excerpt_length);
		$post = $post_old; // Restore the post object.
	}

	function update($new_instance,$old_instance) 
	{
		global $wpdb;
		$displayFields = array();
		if($_POST['display']){
			array_push($_POST['display'], 'title');
			$displayFields = array_unique($_POST['display']);
		}
		else
		{
			$displayFields = array('title');
		}
		$strImplodeFields = implode(',',$displayFields);
		$new_instance['display'] = $strImplodeFields;
		
		return $new_instance;
	}

	/**
	 * The configuration form.
	 *
	 * @param array $look of widget to display already stored value .
	 * 
	 */
	function form($look) 
	{ 	
		$displayFields = array();
		$displayFields = ($look['display']) ? $look['display'] : 'title';
		$display=($look['display']) ? $look['display'] : array();
		$arrExlpodeFields = explode(',', $displayFields);
		$look["widget_w"] = $look["widget_w"] ? $look["widget_w"] : '220';
		$look["widget_h"] = $look["widget_h"] ? $look["widget_h"] : '300';
		$look["excerpt_length"] = $look["excerpt_length"] ? $look["excerpt_length"] : '10';
		$look["scroll_by"] = $look["scroll_by"] ? $look["scroll_by"] : '3';
		$look["date_format"] = $look["date_format"] ? $look["date_format"] : 'F j, Y';
		$look["effects_time"] = $look["effects_time"] ? $look["effects_time"] : '3000';
		?>
		<p>
			<label for="<?php echo $this->get_field_id("widget_title"); ?>">
				<?php _e( 'Title' ); ?>:
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id("widget_title"); ?>" name="<?php echo $this->get_field_name("widget_title"); ?>" type="text" value="<?php echo esc_attr($look["widget_title"]); ?>" />
		</p>
		<p>
			<label>
				<?php _e( 'Category' ); ?>:
			</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("cat"), 'selected' => $look["cat"] ) ); ?>
		</p>
        <p>
        <label>
				<?php _e( 'Display date' ); ?>:
			</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_date"); ?>" name="<?php echo $this->get_field_name("widget_date"); ?>"  >
            <option></option>
            <option <?php if($look["widget_date"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'no' ); ?></option>
             <option <?php if($look["widget_date"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'yes' ); ?></option>
            </select>
        </p>
         <p>
        <label>
				<?php _e( 'Date Format' ); ?>:
			</label>
            <select class="widefat" id="<?php echo $this->get_field_id("date_format"); ?>" name="<?php echo $this->get_field_name("date_format"); ?>"  >
            <option></option>
            <option <?php if($look["date_format"] == "F j, Y g:i a"){ ?> selected="selected" <?php } ?> value="F j, Y g:i a"><?php echo date('F j, Y g:i a') ?></option>
             <option <?php if($look["date_format"] == "F j, Y"){ ?> selected="selected" <?php } ?>  value="F j, Y"><?php echo date('F j, Y') ?></option>
               <option <?php if($look["date_format"] == "F, Y"){ ?> selected="selected" <?php } ?>  value="F, Y"><?php echo date('F, Y') ?></option>
                 <option <?php if($look["date_format"] == "g:i a"){ ?> selected="selected" <?php } ?>  value="g:i a"><?php echo date('g:i a') ?></option>
                   <option <?php if($look["date_format"] == "g:i:s a"){ ?> selected="selected" <?php } ?>  value="g:i:s a"><?php echo date('g:i:s a') ?></option>
                     <option <?php if($look["date_format"] == "l, F jS, Y"){ ?> selected="selected" <?php } ?>  value="l, F jS, Y"><?php echo date('l, F jS, Y') ?></option>
                       <option <?php if($look["date_format"] == "M j, Y @ G:i"){ ?> selected="selected" <?php } ?>  value="M j, Y @ G:i"><?php echo date('M j, Y @ G:i') ?></option>
                         <option <?php if($look["date_format"] == "Y/m/d \a\t g:i A"){ ?> selected="selected" <?php } ?>  value="Y/m/d \a\t g:i A"><?php echo date('Y/m/d \a\t g:i A') ?></option>
                           <option <?php if($look["date_format"] == "Y/m/d \a\t g:ia"){ ?> selected="selected" <?php } ?>  value="Y/m/d \a\t g:ia"><?php echo date('Y/m/d \a\t g:ia') ?></option>
            <option <?php if($look["date_format"] == "Y/m/d g:i:s A"){ ?> selected="selected" <?php } ?>  value="Y/m/d g:i:s A"><?php echo date('Y/m/d g:i:s A') ?></option>
            </select>
        </p>
        
        
         <p>
        <label>
				<?php _e( 'Display Author' ); ?>:
			</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_author"); ?>" name="<?php echo $this->get_field_name("widget_author"); ?>"  >
            <option></option>
            <option <?php if($look["widget_author"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'no' ); ?></option>
             <option <?php if($look["widget_author"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'yes' ); ?></option>
            </select>
        </p>
        
         <p>
        <label>
				<?php _e( 'Text display Author' ); ?>:
			</label>
            
          <input class="widefat" id="<?php echo $this->get_field_id("text_widget_author"); ?>" name="<?php echo $this->get_field_name("text_widget_author"); ?>" value="<?php echo $look["text_widget_author"] ?>" />
            
        </p>
          <p>
        <label>
				<?php _e( 'Display Excerpt' ); ?>:
			</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_excerpt"); ?>" name="<?php echo $this->get_field_name("widget_excerpt"); ?>"  >
            
            <option <?php if($look["widget_excerpt"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'no' ); ?></option>
             <option <?php if($look["widget_excerpt"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'yes' ); ?></option>
            </select>
        </p>
          <p>
        <label>
				<?php _e( 'Display Excerpt All' ); ?>:
			</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_d_excerpt"); ?>" name="<?php echo $this->get_field_name("widget_d_excerpt"); ?>"  >
            <option></option>
            <option <?php if($look["widget_d_excerpt"] == "left"){ ?> selected="selected" <?php } ?> value="left"><?php _e( 'Left' ); ?></option>
             <option <?php if($look["widget_d_excerpt"] == "right"){ ?> selected="selected" <?php } ?>  value="right"><?php _e( 'Right' ); ?></option>
             <option <?php if($look["widget_d_excerpt"] == "justify"){ ?> selected="selected" <?php } ?>  value="justify"><?php _e( 'Justify' ); ?></option>
             <option <?php if($look["widget_d_excerpt"] == "center"){ ?> selected="selected" <?php } ?>  value="center"><?php _e( 'Center' ); ?></option>
            </select>
        </p>
          <p>
        <label>
				<?php _e( 'class Excerpt' ); ?>:
			</label>
            <input class="widefat" id="<?php echo $this->get_field_id("widget_class_excerpt"); ?>" name="<?php echo $this->get_field_name("widget_class_excerpt"); ?>" type="text" value="<?php echo $look["widget_class_excerpt"] ?>" />
            
        </p>
        
		<p>
			<label><?php _e('Widget dimensions'); ?>:</label>
				<br />
				<label for="<?php echo $this->get_field_id("widget_w"); ?>">
					Width: 
				</label>
				<input class="widefat widget_dimension" type="text" id="<?php echo $this->get_field_id("widget_w"); ?>" name="<?php echo $this->get_field_name("widget_w"); ?>" value="<?php echo $look["widget_w"]; ?>"  size='3'  maxlength="3"/> px
				<br />
				<label for="<?php echo $this->get_field_id("widget_h"); ?>">
					Height: 
				</label>
				<input class="widefat widget_dimension" type="text" id="<?php echo $this->get_field_id("widget_h"); ?>" name="<?php echo $this->get_field_name("widget_h"); ?>" value="<?php echo $look["widget_h"]; ?>"  size='3'  maxlength="3"/> px			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("effects_time"); ?>">
				<?php _e('Effect Duration (milliseconds)'); ?>:
			</label>
			<input  class="text_alignment" id="<?php echo $this->get_field_id("effects_time"); ?>" name="<?php echo $this->get_field_name("effects_time"); ?>" type="text" value="<?php echo absint($look["effects_time"]); ?>" size='3' />			
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id("num"); ?>">
				<?php _e('Number of posts to show'); ?>:
			</label>
			<input class="text_alignment" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($look["num"]); ?>" size='3' />			
    	</p>
	    <p>
			<label for="<?php echo $this->get_field_id("sort_by"); ?>">
        		<?php _e('Sort by'); ?>:
        	</label>
	        <select id="<?php echo $this->get_field_id("sort_by"); ?>" name="<?php echo $this->get_field_name("sort_by"); ?>">
		        <option value="date"<?php selected( $look["sort_by"], "date" ); ?>>Date</option>
		        <option value="title"<?php selected( $look["sort_by"], "title" ); ?>>Title</option>
		        <option value="comment_count"<?php selected( $look["sort_by"], "comment_count" ); ?>>Number of comments</option>
		        <option value="rand"<?php selected( $look["sort_by"], "rand" ); ?>>Random</option>
	        </select>			
    	</p>
    	<p>
			<label for="<?php echo $this->get_field_id("effects"); ?>">
        		<?php _e('Effects'); ?>:
        	</label>
        	<select id="<?php echo $this->get_field_id("effects"); ?>" name="<?php echo $this->get_field_name("effects"); ?>" class="widefat effect">
				<?php
					$arrEffect = array("none","scrollHorz","scrollVert"); 
					foreach($arrEffect as $strKey => $strValue)
					{
				?>
				<option value="<?php echo $strValue; ?>" <?php selected( $look["effects"], "$strValue" ); ?>><?php echo ucfirst($strValue); ?></option>
				<?php } ?>
			</select>	        
    	</p>  
        <p>
        <label for="<?php echo $this->get_field_id("title_display"); ?>">
        		<?php _e('title display'); ?>:
        	</label><?php echo $look['title_display'] ?>
         <select class="widefat" id="<?php echo $this->get_field_id("widget_title_display"); ?>" name="<?php echo $this->get_field_name("widget_title_display"); ?>"   >
         <option <?php if($look['widget_title_display']=="h2"){ ?> selected="selected" <?php } ?> value="h2">h2</option>
         <option <?php if($look['widget_title_display']=="h3"){ ?> selected="selected" <?php } ?>  value="h3">h3</option>
         <option <?php if($look['widget_title_display']=="h4"){ ?> selected="selected" <?php } ?> value="h4">h4</option>
         <option <?php if($look['widget_title_display']=="h5"){ ?> selected="selected" <?php } ?> value="h5">h5</option>
         <option <?php if($look['widget_title_display']=="strong"){ ?> selected="selected" <?php } ?> value="strong">strong</option>
         <option <?php if($look['widget_title_display']=="span"){ ?> selected="selected" <?php } ?> value="span">span</option>
         <option <?php if($look['widget_title_display']=="p"){ ?> selected="selected" <?php } ?> value="p">p</option>
         </select>
        </p>
         <p>
        <label for="<?php echo $this->get_field_id("class_title_display"); ?>">
        		<?php _e('class title display'); ?>:
        	</label>
           
        <input  id="<?php echo $this->get_field_id("widget_class_title_display"); ?>" name="<?php echo $this->get_field_name("widget_class_title_display"); ?>" value="<?php echo $look['widget_class_title_display'] ?>" type="text"  />
        </p>
         
          <p>
			<label><?php _e('Target link'); ?>:</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_target_link"); ?>" name="<?php echo $this->get_field_name("widget_target_link"); ?>"  >
      <option></option>
            <option <?php if($look["widget_target_link"] == "_blank"){ ?> selected="selected" <?php } ?> value="_blank"><?php _e( 'blank' ); ?></option>
             <option <?php if($look["widget_target_link"] == "_parent"){ ?> selected="selected" <?php } ?>  value="_parent"><?php _e( 'parent' ); ?></option>
              <option <?php if($look["widget_target_link"] == "_self"){ ?> selected="selected" <?php } ?>  value="_self"><?php _e( 'self' ); ?></option>
               <option <?php if($look["widget_target_link"] == "_top"){ ?> selected="selected" <?php } ?>  value="_top"><?php _e( 'top' ); ?></option>
               <option <?php if($look["widget_target_link"] == "new"){ ?> selected="selected" <?php } ?>  value="new"><?php _e( 'new' ); ?></option>
            </select>
            </p>
             <p>
			<label><?php _e('Rel link'); ?>:</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_rel_link"); ?>" name="<?php echo $this->get_field_name("widget_rel_link"); ?>"  >
      <option></option>
            <option <?php if($look["widget_rel_link"] == "alternate"){ ?> selected="selected" <?php } ?> value="alternate"><?php _e( 'Alternate' ); ?></option>
             <option <?php if($look["widget_rel_link"] == "archives"){ ?> selected="selected" <?php } ?>  value="archives"><?php _e( 'Archives' ); ?></option>
              <option <?php if($look["widget_rel_link"] == "author"){ ?> selected="selected" <?php } ?>  value="author"><?php _e( 'Author' ); ?></option>
               <option <?php if($look["widget_rel_link"] == "bookmark"){ ?> selected="selected" <?php } ?>  value="bookmark"><?php _e( 'Bookmark' ); ?></option>
               <option <?php if($look["widget_rel_link"] == "external"){ ?> selected="selected" <?php } ?>  value="external"><?php _e( 'External' ); ?></option>
                 <option <?php if($look["widget_rel_link"] == "first"){ ?> selected="selected" <?php } ?> value="alternate"><?php _e( 'First' ); ?></option>
             <option <?php if($look["widget_rel_link"] == "archives"){ ?> selected="selected" <?php } ?>  value="archives"><?php _e( 'First' ); ?></option>
              <option <?php if($look["widget_rel_link"] == "glossary"){ ?> selected="selected" <?php } ?>  value="glossary"><?php _e( 'Glossary' ); ?></option>
               <option <?php if($look["widget_rel_link"] == "help"){ ?> selected="selected" <?php } ?>  value="help"><?php _e( 'Help' ); ?></option>
               <option <?php if($look["widget_rel_link"] == "index"){ ?> selected="selected" <?php } ?>  value="index"><?php _e( 'Index' ); ?></option>
                 <option <?php if($look["widget_rel_link"] == "last"){ ?> selected="selected" <?php } ?>  value="last"><?php _e( 'Last' ); ?></option>
                  <option <?php if($look["widget_rel_link"] == "license"){ ?> selected="selected" <?php } ?>  value="license"><?php _e( 'License' ); ?></option>
                   <option <?php if($look["widget_rel_link"] == "next"){ ?> selected="selected" <?php } ?>  value="next"><?php _e( 'Next' ); ?></option>
                    <option <?php if($look["widget_rel_link"] == "nofollow"){ ?> selected="selected" <?php } ?>  value="nofollow"><?php _e( 'Nofollow' ); ?></option>
                       <option <?php if($look["widget_rel_link"] == "prefetch"){ ?> selected="selected" <?php } ?>  value="prefetch"><?php _e( 'Prefetch' ); ?></option>
                      <option <?php if($look["widget_rel_link"] == "prev"){ ?> selected="selected" <?php } ?>  value="prev"><?php _e( 'Prev' ); ?></option>
                      
                    <option <?php if($look["widget_rel_link"] == "search"){ ?> selected="selected" <?php } ?>  value="search"><?php _e( 'Search' ); ?></option>
                  <option <?php if($look["widget_rel_link"] == "tag"){ ?> selected="selected" <?php } ?>  value="tag"><?php _e( 'Tag' ); ?></option>
                  <option <?php if($look["widget_rel_link"] == "up"){ ?> selected="selected" <?php } ?>  value="up"><?php _e( 'Up' ); ?></option>
            </select>
            </p>
          	
   		
		<p>
			<label for="<?php echo $this->get_field_id("excerpt_length"); ?>">
				<?php _e( 'Excerpt length (in words):' ); ?>
			</label>
			<input class="text_alignment" type="text" id="<?php echo $this->get_field_id("excerpt_length"); ?>" name="<?php echo $this->get_field_name("excerpt_length"); ?>" value="<?php echo $look["excerpt_length"]; ?>" size="3" />
		</p>
        <p>
			<label><?php _e('Thumbnail display'); ?>:</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_thumbnail_display"); ?>" name="<?php echo $this->get_field_name("widget_thumbnail_display"); ?>"  >
            <option></option>
            <option <?php if($look["widget_thumbnail_display"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'no' ); ?></option>
             <option <?php if($look["widget_thumbnail_display"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'yes' ); ?></option>
            </select>
            </p>
            
             <p>
			<label><?php _e('Thumbnail display position'); ?>:</label>
            <select class="widefat" id="<?php echo $this->get_field_id("widget_thumbnail_display_position"); ?>" name="<?php echo $this->get_field_name("widget_thumbnail_display_position"); ?>"  >
      
            <option <?php if($look["widget_thumbnail_display_position"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'left' ); ?></option>
             <option <?php if($look["widget_thumbnail_display_position"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'right' ); ?></option>
            </select>
            </p>
        
        
        <p>
			<label for="<?php echo $this->get_field_id("link_thumbnail_display"); ?>">
        		<?php _e('link thumbnail display'); ?>:
        	</label>
	      
                
                 <select class="widefat" id="<?php echo $this->get_field_id("link_thumbnail_display"); ?>" name="<?php echo $this->get_field_name("link_thumbnail_display"); ?>"  >
          
            <option <?php if($look["link_thumbnail_display"] == "0"){ ?> selected="selected" <?php } ?> value="0"><?php _e( 'no' ); ?></option>
             <option <?php if($look["link_thumbnail_display"] == "1"){ ?> selected="selected" <?php } ?>  value="1"><?php _e( 'yes' ); ?></option>  
	        </select>			
		</p>
        
        
        
		<p>
			<label><?php _e('Thumbnail dimensions'); ?>:</label>
			<br/>	
			<label for="<?php echo $this->get_field_id("thumb_w"); ?>">
					Width:
			</label> 
			<input class="widefat widget_dimension" type="text" id="<?php echo $this->get_field_id("thumb_w"); ?>" name="<?php echo $this->get_field_name("thumb_w"); ?>" value="<?php echo $look["thumb_w"]; ?>"  size='3'  maxlength="3"/> px
			<br/>	
			<label for="<?php echo $this->get_field_id("thumb_h"); ?>">
				Height: 
			</label>
			<input class="widefat widget_dimension" type="text" id="<?php echo $this->get_field_id("thumb_h"); ?>" name="<?php echo $this->get_field_name("thumb_h"); ?>" value="<?php echo $look["thumb_h"]; ?>"  size='3' maxlength="3"/> px						
		</p>
		
		
		<?php 
	}
} 
add_action('widgets_init', create_function('', 'return register_widget("Category_Post_List_widget");'));
?>