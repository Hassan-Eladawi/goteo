<?php
	use Goteo\Library\Text,
			Goteo\Model\Blog\Post;
			$allow = Post::allowed($this['post']);
			$level = (int) $this['level'] ?: 3;
?>
<?php if ($allow == 1) : ?>
 <script type="text/javascript">
	jQuery(document).ready(function ($) { 
	    //change div#preview content when textarea lost focus
		$("#message").blur(function(){
			$("#preview").html($("#message").val());
		});
		
		//add fancybox on #a-preview click
		$("#a-preview").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	});
</script>
<div class="widget blog-comment">
    <h<?php echo $level ?> class="title"><?php echo Text::get('blog-send_comment-header'); ?></h<?php echo $level ?>>
    <form method="post" action="/message/post/<?php echo $this['post']; ?>/<?php echo $this['project']; ?>">
	    <div id="bocadillo"></div>
        <textarea id="message" name="message" cols="50" rows="5"></textarea>
        <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;Previsualizar</a>
        <div style="display:none">
        	<div id="preview" style="width:400px;height:300px;overflow:auto;">
                    
                </div>
        </div>
        <button <?php echo (!empty($_SESSION['user']->id)) ? 'class="green"' : 'class="disabled" disabled="disabled"'; ?> type="submit"><?php echo Text::get('blog-send_comment-button'); ?></button>
    </form>
</div>
<?php else : ?>
    <p><?php echo Text::get('blog-comments_no_allowed'); ?></p>
<?php endif; ?>
