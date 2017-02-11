<?php get_header(); ?>


<div class="content-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-9 with-sidebar">
                <div class="category-block articles">
                    <?php
                        get_template_part('templates/tpl', 'page-title');
                    ?>

                    <article class="blog-item blog-single page404">
                        <h1 class="post-title text-center">
                            <?php echo wp_kses( __('The page You are searching<br>was not found!', 'tana'), array('br'=>array()) ); ?>
                        </h1>

                        <div class="entry-excerpt text-center">

                            <p>
                                <?php esc_html_e('Sorry, the post you are looking for is not available. Maybe you want to perform a search?', 'tana'); ?>
                            </p>
                            <?php get_search_form(); ?>

                        </div>
                    </article>

                </div>
            </div>
            
            <?php get_sidebar(); ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>