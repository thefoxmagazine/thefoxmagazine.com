<?php

global $tana_archive_columns,$tana_content_class;

// Post thumbnail image & detail markups
$thumbnail = Tana_Tpl::get_post_image('tana-blog-grid');

$titlemarkup = "<h4><a href='".get_permalink()."'>".get_the_title()."</a></h4>";
$metamarkup = "<div class='meta'><span class='author'>".get_the_author_meta( 'display_name', $post->post_author )."</span><span class='date'>".get_the_date()."</span></div>";

$excerptmarkup = "<p>".get_the_excerpt()."</p>";
?>

    <div <?php post_class($tana_content_class); ?>>
        <div class='category-block articles'>

            <div class='post first hover-dark'>
            <?php
                echo $thumbnail, $metamarkup, $titlemarkup, $excerptmarkup;
            ?>
            </div>
            
        </div>
    </div>