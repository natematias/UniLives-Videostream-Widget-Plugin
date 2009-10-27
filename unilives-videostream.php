<?php
/*
Plugin Name: UniLives VideoStream
Plugin URI: http://www.unilives.com
Description: Displays a set of video thumbnails for posts containing videos
Author: J. Nathan Matias
Version: 1
*/

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'unilives_videostream_load_widgets' );

/* Function that registers our widget. */
function unilives_videostream_load_widgets() {
  register_widget( 'UniLivesVideostreamWidget' );
}


//OPTIONS
// number of posts
// height of thumbnails
class UniLivesVideostreamWidget extends WP_Widget {
  function UniLivesVideostreamWidget() {
      /* Widget settings. */
    $widget_ops = array( 'classname' => 'UniLivesVideostreamWidget', 'description' => 'Displays a videostream of tagged and thumbnailed videos on UniLives.com' );
      /* Create the widget. */
    $this->WP_Widget( 'unilives-videostream-widget', 'UniLives Videostream Widget', $widget_ops);
  }

  public static function get_video_category_link(){
   $category_ids = get_all_category_ids();
    foreach($category_ids as $cat_id) {
      if(!strcmp(get_cat_name($cat_id),"video")){
        echo get_category_link($cat_id);
      }
    }
  }
	
  function widget( $args, $instance ) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title'] );

    echo $before_widget;
    
    echo $before_title;
    echo $title;
    echo $after_title;

    //TODO: make the number of shown posts configurable
    $videoposts = new WP_Query(array(
      'category_name' => "video", 
      'meta_key' => "video-thumb",
      'posts_per_page' => $instance['show'] 
    ));
    if( $videoposts->have_posts() ) {?>

<div class="unilives_video_widget">

<?php while ($videoposts->have_posts()) : 
        $post = $videoposts->the_post();
        $url = get_post_meta(get_the_ID(), 'video-thumb', true);
        if($url=='' || $url==null){ continue;}
        $url = get_post_meta(get_the_ID(), 'video-thumb', true);

        $author = get_post_meta(get_the_ID(), 'video-author', true);
        $duration = get_post_meta(get_the_ID(), 'video-duration', true);
        $university = get_post_meta(get_the_ID(), 'video-university', true);
        $location = get_post_meta(get_the_ID(), 'video-location', true);
?>
<a href="<?php the_permalink();?>" class="unilives_video_link"><div class="unilives_video"><img src="<?php echo $url ?>" height="<?php echo  $instance['height']?>"/><div class="unilives_video_info"><div class="unilives_video_title"><?php the_title();?></div><div class="unilives_video_author"><?php echo $author?></div><div class="unilives_video_duration"><?php echo $duration?></div><div class="unilives_video_university"><?php echo $university?></div><div class="unilives_video_location"><?php echo $location?></div><div class="unilives_video_excerpt"><?php the_excerpt();?></div></div></div></a>
<?php    endwhile;?>
<div class="unilives_video_widget_separator">
</div>
<div class="unilives_video_widget_title"><?php echo $title?> (<a href="<?php echo $instance['all videos'];?>">all videos</a> )</div>

</div><?php
    }
    echo $after_widget;
    return $instance;
  }
 
  // cribbed from the twitter widget, my this is a disaster waiting to happen, especially the display of forms
  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array('title' => '', 'show' => '4', 'height' => '100', 'all videos'=>'/category/video/') );
    $title = esc_attr($instance['title']);
    $show = absint($instance['show']);
    if ( $show < 1 || 20 < $show )
       $show = '4';
    $height = absint($instance['height']);
    if ( $height < 1 || 300 < $height )
      $heigh = '100';
    $all_videos = $instance['all videos'];

    echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:') . '
          <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />
          </label></p>
          <p><label for="' . $this->get_field_id('show') . '">' . __('Max posts to show:') . '
                        <select id="' . $this->get_field_id('show') . '" name="' . $this->get_field_name('show') . '">';
                for ( $i = 1; $i <= 20; ++$i )
                        echo "<option value='$i' " . ( $show == $i ? "selected='selected'" : '' ) . ">$i</option>";

                echo '          </select>
          </label></p>
          <p><label for="' . $this->get_field_id('height') . '">' . __('Common height of thumbnails:') . '
          <input class="widefat" id="' . $this->get_field_id('height') . '" name="' . $this->get_field_name('height') . '" type="text" value="' . $height . '" />
          </label></p>
          <p><label for="' . $this->get_field_id('all videos') . '">' . __('URI of "all videos" link:') . '
          <input class="widefat" id="' . $this->get_field_id('all videos') . '" name="' . $this->get_field_name('all videos') . '" type="text" value="' . $all_videos . '" />
          </label></p>';

         
  }
}
?>
