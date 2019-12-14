<?php

?>
<li class="blog__post-item" style="background: url('<?php echo $post->getFeaturedImageUrl() ?>') no-repeat">
    <div class="overlay"></div>
    <div class="blog__post-excerpt" itemprop="text">
        <?php if ($post->getFeaturedImageUrl() && $post->getFeaturedShowOnHome()): ?>
        <?php endif ?>
        <a href="<?= $post->getUrl() ?>" >
            <span class="blog__post-excerpt_content">
                <header class="blog__post-header">
                    <h2 class="blog__post-title" itemprop="headline">
                        <?= $post->getName() ?>
                    </h2>
                </header>
                <?= $block->getContentMoreTag($post) ?>
                <span><?= __('Read more...') ?></span>
            </span>
        </a>
    </div>
</li>