<?php get_header(); ?>

<?php 

global $tana_archive_columns;
$tana_archive_columns = Tana_Std::get_mod('archive_style', 'regular');
$content_class = "col-md-9 with-sidebar sticky-column";
if( strpos($tana_archive_columns, 'nosidebar') !== false ) {
    $content_class = "col-md-12";
}
?>

<div class="content-area sticky-parent">
    <div class="container">
        <div class="row">
            <div class="<?php print $content_class;?>">
                <div class="category-block articles">
                    <div class="theiaStickySidebar">
                        <?php
                            get_template_part('templates/tpl', 'page-title');
                        ?>

                        <?php
                        if( $tana_archive_columns == 'regular' ) {
                            while ( have_posts() ) : the_post();
                                get_template_part( 'content' );
                            endwhile;
                        } else {
                            global $tana_content_class;
                            // Column variations
                            $tana_content_class = "col-xs-12 col-sm-6";
                            $tana_content_class = strpos($tana_archive_columns, '3 columns') !== false ? "col-xs-12 col-sm-6 col-md-4" : $tana_content_class;
                            $tana_content_class = strpos($tana_archive_columns, '4 columns') !== false ? "col-xs-12 col-sm-6 col-md-3" : $tana_content_class;
                            $tana_content_class = strpos($tana_archive_columns, '6 columns') !== false ? "col-xs-12 col-sm-4 col-md-2" : $tana_content_class;

                            echo "<div class='row blog-grid-$tana_archive_columns'>";
                            while ( have_posts() ) : the_post();
                                get_template_part( 'content', 'grid' );
                            endwhile;
                            echo "</div>";
                        }
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
            
            <?php 
            if( strpos($tana_archive_columns, 'nosidebar') === false ) :
                get_sidebar(); 
            endif;
            ?>

        </div>
    </div>
</div>


<?php get_footer(); ?>