<!-- Trending Layout (Numbered list with gradient) -->
<?php
global $trending_counter;
if (!isset($trending_counter)) $trending_counter = 1;
$gradient_colors = array('#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8');
$color = $gradient_colors[($trending_counter - 1) % 5];
?>
<article class="news-card layout-trending mb-3">
    <div class="d-flex align-items-center gap-3 p-3" style="background: var(--card-bg); border-left: 4px solid <?php echo $color; ?>;">
        <div class="trending-number" style="background: linear-gradient(135deg, <?php echo $color; ?>, <?php echo $color; ?>99); color: white; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 24px; font-weight: bold; flex-shrink: 0;">
            <?php echo $trending_counter; ?>
        </div>
        <div class="flex-grow-1">
            <h4 class="mb-1" style="font-size: 1.1rem;">
                <a href="<?php the_permalink(); ?>" class="ajax-link text-decoration-none" style="color: var(--text-color);">
                    <?php the_title(); ?>
                </a>
            </h4>
            <div class="meta text-muted small">
                <i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?> •
                <i class="far fa-eye"></i> <?php echo rand(100, 9999); ?> views
            </div>
        </div>
        <div class="trending-actions d-flex gap-2">
            <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-outline-primary ajax-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</article>
<?php $trending_counter++; ?>
