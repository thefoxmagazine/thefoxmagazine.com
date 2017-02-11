<?php get_header(); ?>


<div class="content-area sticky-parent">
    <div class="container">
        <div class="row">
            <div class="col-md-9 with-sidebar sticky-column">
                <div class="category-block articles">
                    <div class="theiaStickySidebar">
                        <?php
                            get_template_part('templates/tpl', 'page-title');
                        ?>

                        <?php
                        if(have_posts()) :
                            while ( have_posts() ) : the_post();
                                get_template_part( 'content', get_post_format() );
                            endwhile;
                        else: ?>
                            <h3><?php esc_html_e('Your search term cannot be found', 'tana'); ?></h3>
                            <p><?php esc_html_e('Sorry, the post you are looking for is not available. Maybe you want to perform a search?', 'tana'); ?></p>
                            <?php get_search_form();?>
                            <br>
                            <p><?php esc_html_e('For best search results, mind the following suggestions:', 'tana'); ?></p>
                            <ul class="borderlist-not">
                                <li><?php esc_html_e('Always double check your spelling.', 'tana'); ?></li>
                                <li><?php esc_html_e('Try similar keywords, for example: tablet instead of laptop.', 'tana'); ?></li>
                                <li><?php esc_html_e('Try using more than one keyword.', 'tana'); ?></li>
                            </ul>
                        <?php
                        endif;
                        ?>

                        <?php
                        $pagination = Tana_Tpl::pagination();
                        if( !empty($pagination) ){
                            echo "<div class='post-navigation'>$pagination</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <?php get_sidebar(); ?>

        </div>
    </div>
</div>


<?php get_footer(); ?>