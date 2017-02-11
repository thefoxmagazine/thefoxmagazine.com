<?php

if ( post_password_required() ) {
    return;
}



if( !function_exists('tana_custom_comment_item') ):
function tana_custom_comment_item($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    if ( 'div' == $args['style'] ) {
        $tag = 'div';
    } else {
        $tag = 'li';
    }

    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
    ?>
    
    <<?php echo esc_attr($tag); ?> class="comment pingback">
        <p><?php esc_html_e('Pingback:', 'tana'); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__('Edit', 'tana'), '<span class="edit-link">', '</span>' ); ?></p>
    <?php
            break;
        default:
    ?>

    <<?php echo esc_attr($tag); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">

    <article>
        <div class="comment-avatar">
            <?php echo get_avatar( $comment, 128 ); ?>
        </div>

        <div class="comment-body">
            <div class="meta-data">
                <a href="javascript:;" class="comment-author"><?php echo get_comment_author(); ?></a>
            </div>
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
            <div class="meta-data">
                <span class="comment-date"><?php printf( '%1$s', get_comment_date() ); ?> - <?php printf( '%1$s', get_comment_time() ); ?></span>
                <span class="comment-reply">
                    <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                </span>
            </div>
        </div>
    </article>

<?php
            break;
    endswitch;
}
endif;







// Comment Navigation
if ( ! function_exists( 'tt_theme_comment_nav' ) ) :
    function tt_theme_comment_nav() {
        // Are there comments to navigate through?
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
        ?>
        <nav class="navigation comment-navigation" role="navigation">
            <h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'tana'); ?></h2>
            <div class="nav-links">
                <?php
                    if ( $prev_link = get_previous_comments_link( esc_html__('Older Comments', 'tana') ) ) :
                        printf( '<div class="nav-previous">%s</div>', $prev_link );
                    endif;

                    if ( $next_link = get_next_comments_link( esc_html__('Newer Comments', 'tana') ) ) :
                        printf( '<div class="nav-next">%s</div>', $next_link );
                    endif;
                ?>
            </div><!-- .nav-links -->
        </nav><!-- .comment-navigation -->
        <?php
        endif;
    }
endif;




?>


<div id="comments" class="comments-area">
    
    <?php if ( have_comments() ) : ?>
    <div class="comments-wrapper">

        <div class="border-line mv5"></div>

        <h2 class="title-block mv8" data-title="<?php esc_attr_e('Comments', 'tana'); ?>">
            <?php
                printf( _nx( 'One comment', 'Comments (%1$s)', get_comments_number(), 'comment', 'tana' ),
                    number_format_i18n( get_comments_number() ), get_the_title() );
            ?>
        </h2>

        <?php tt_theme_comment_nav(); ?>

        <ol class="comment-list">
            <?php
                wp_list_comments( array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 56,
                    'callback'    => 'tana_custom_comment_item'
                ) );
            ?>
        </ol><!-- .comment-list -->

        <?php tt_theme_comment_nav(); ?>

    </div>
    <?php endif; // have_comments() ?>


    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
    ?>
        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'tana'); ?></p>
    <?php else:
        echo "<h3 class='title-block mv8' data-title='".esc_attr__('Comments', 'tana')."'>
            ".esc_attr__('Leave a reply', 'tana')."</h3>";
    endif; ?>


    <?php
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' );
        comment_form(
            array(
                'title_reply' => '',
                'comment_notes_after' => '',
                'class_submit' => '',
                'fields' => array(
                    'author' => '<div class="row">
                                        <p class="comment-form-author col-sm-6">
                                            <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
                                        '" size="30" placeholder="'.esc_attr__('Name *', 'tana').'" ' . $aria_req . ' /></p>',

                    'email' => '<p class="comment-form-email col-sm-6">
                                    <input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
                                    '" size="30" placeholder="'.esc_attr__('Email *', 'tana').'" ' . $aria_req . ' /></p></div>',

                    'url' => '<p class="comment-form-url">
                                    <input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
                                    '" size="30" placeholder="'.esc_attr__('Website', 'tana').'" /></p>',
                ),
                'comment_field' => '<p class="comment-form-comment">
                                        <textarea id="comment" name="comment" placeholder="'.esc_attr__('Comments', 'tana').'"></textarea></p>'
            )
        );
    ?>
    
</div>