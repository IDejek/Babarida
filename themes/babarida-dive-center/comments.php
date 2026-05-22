<?php
/**
 * Comments Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

if (post_password_required()) return;

if (have_comments()) :
?>
<div id="comments" class="comments-area" style="padding-top:32px; border-top:1px solid var(--gray-100);">
    <h3 style="font-family:var(--font-display); font-size:1.3rem; font-weight:700; color:var(--blue-deep); margin-bottom:24px;">
        <?php
        $count = get_comments_number();
        printf(esc_html(_n('%d Comment', '%d Comments', $count, 'babarida')), $count);
        ?>
    </h3>
    <ol class="comment-list" style="list-style:none; display:flex; flex-direction:column; gap:16px;">
        <?php
        wp_list_comments(array(
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 48,
            'callback'    => 'babarida_comment_callback',
        ));
        ?>
    </ol>
    <?php
    the_comments_navigation(array(
        'prev_text' => '<i class="fa-solid fa-chevron-left"></i> ' . __('Older Comments', 'babarida'),
        'next_text' => __('Newer Comments', 'babarida') . ' <i class="fa-solid fa-chevron-right"></i>',
    ));
    ?>
</div>
<?php endif; ?>

<?php
if (comments_open()) :
    comment_form(array(
        'class_form'         => 'comment-form',
        'title_reply'        => '<span style="font-family:var(--font-display); font-size:1.2rem; font-weight:700; color:var(--blue-deep);">Leave a Comment</span>',
        'comment_notes_before' => '<p class="form-note" style="margin-bottom:16px;">' . esc_html__('Your email address will not be published. Required fields are marked *', 'babarida') . '</p>',
        'class_submit'       => 'btn btn-primary',
        'label_submit'       => __('Post Comment', 'babarida'),
        'comment_field'      => '<div class="form-group"><label class="form-label" for="comment">' . __('Comment *', 'babarida') . '</label><textarea class="form-textarea" id="comment" name="comment" rows="5" required></textarea></div>',
        'fields'             => array(
            'author' => '<div class="form-row"><div class="form-group"><label class="form-label" for="author">' . __('Name *', 'babarida') . '</label><input type="text" class="form-input" id="author" name="author" required></div>',
            'email'  => '<div class="form-group"><label class="form-label" for="email">' . __('Email *', 'babarida') . '</label><input type="email" class="form-input" id="email" name="email" required></div></div>',
            'url'    => '<div class="form-group"><label class="form-label" for="url">' . __('Website', 'babarida') . '</label><input type="url" class="form-input" id="url" name="url"></div>',
        ),
    ));
endif;
?>

<?php
function babarida_comment_callback($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('comment-item', $comment); ?> style="padding:20px; background:var(--gray-50); border-radius:var(--radius-lg);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <?php echo get_avatar($comment, $args['avatar_size'], '', '', array('style' => 'border-radius:50%; border:2px solid var(--blue-light);')); ?>
            <div>
                <strong style="font-size:0.9rem; color:var(--blue-deep);"><?php echo get_comment_author_link($comment); ?></strong>
                <span style="display:block; font-size:0.72rem; color:var(--gray-400);"><?php printf(esc_html__('%1$s at %2$s', 'babarida'), get_comment_date('', $comment), get_comment_time()); ?></span>
            </div>
        </div>
        <div style="font-size:0.88rem; color:var(--gray-600); line-height:1.7;">
            <?php if ($comment->comment_approved == '0') : ?>
                <em style="color:var(--warning); font-size:0.82rem;"><?php esc_html_e('Your comment is awaiting moderation.', 'babarida'); ?></em><br>
            <?php endif; ?>
            <?php comment_text(); ?>
        </div>
        <div style="margin-top:10px; font-size:0.75rem;">
            <?php
            comment_reply_link(array_merge($args, array(
                'add_below' => 'comment',
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'before'    => '',
                'after'     => '',
            )));
            ?>
        </div>
    </<?php echo $tag; ?>>
    <?php
}
