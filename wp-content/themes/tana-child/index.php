<?php get_header(); ?>

<section class="content-area sticky-parent">

    <div class="container">
        <div class="row">
            
            <div class="col-sm-9 with-sidebar sticky-column">
                <div class="theiaStickySidebar">
                    <div class="category-block articles">
                        <?php
                            get_template_part('templates/tpl', 'page-title');
                        ?>

                        <?php
                        while ( have_posts() ) : the_post();
                            get_template_part( 'content' );
                        endwhile;
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
        <!-- end .row -->
    </div>
    <!-- end .container -->

</section>
<!-- end .content-area -->


<?php get_footer(); ?>