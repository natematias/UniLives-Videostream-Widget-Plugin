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
// width of thumbnails
class UniLivesVideostreamWidget extends WP_Widget {
  function UniLivesVideostreamWidget() {
      /* Widget settings. */
    $widget_ops = array( 'classname' => 'UniLivesVideostreamWidget', 'description' => 'Displays a videostream of tagged and thumbnailed videos on UniLives.com' );
      /* Create the widget. */
    $this->WP_Widget( 'unilives-videostream-widget', 'UniLives Videostream Widget', $widget_ops);
  }
	
  function widget( $args, $instance ) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title'] );

    echo $before_widget;

    //TODO: make the number of shown posts configurable
    $videoposts = new WP_Query(array(
      'category_name' => "video", 
      'meta_key' => "video-thumb",
      'show_posts' => 4
    ));
    if( $videoposts->have_posts() ) {
      while ($videoposts->have_posts()) : 
        $post = $videoposts->the_post();
        $url = get_post_meta(get_the_ID(), 'video-thumb', true);
        if($url=='' || $url==null){ continue;}
        $url = get_post_meta(get_the_ID(), 'video-thumb', true);

        $author = get_post_meta(get_the_ID(), 'video-author', true);
        $duration = get_post_meta(get_the_ID(), 'video-duration', true);
        $university = get_post_meta(get_the_ID(), 'video-university', true);
        $location = get_post_meta(get_the_ID(), 'video-location', true);
?>
<a href="<?php the_permalink();?>"><div class="unilives_video"><img src="<?php echo $url ?>"/><div class="unilives_video_info"><div class="unilives_video_title"><?php the_title();?></div><div class="unilives_video_author"><?php echo $author?></div><div class="unilives_video_duration"><?php echo $duration?></div><div class="unilives_video_university"><?php echo $university?></div><div class="unilives_video_location"><?php echo $location?></div><div class=""><?php the_excerpt();?></div></div></div></a>
<?
      endwhile;
    }

    echo $after_widget;
    return $instance;
  }
 
  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array('account' => '', 'title' => '', 'show' => '5', 'hidereplies' => false) );
    $title = esc_attr($instance['title']);
    $show = absint($instance['show']);
    if ( $show < 1 || 20 < $show )
       $show = '5';
   $height = absint($instance['height']);
    if ( $height < 1 || 300 < $height )
       $width = '80';

 
  }
}
?>
