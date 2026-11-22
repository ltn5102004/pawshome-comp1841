<?php // Source: adapted from W3Schools (no date) How To - CSS Pagination; MDN Web Docs (no date) aria-current and rel="next" / rel="prev". ?>
<?php // Reusable page links. Needs $pagination from paginate(). Nothing is shown when there is only one page. ?>
<?php if ($pagination['pages'] > 1): ?>
	<nav class="pagination" aria-label="Pages">
		<?php if ($pagination['page'] > 1): ?>
			<a class="page-link" href="<?=html(pageUrl($pagination['page'] - 1))?>" rel="prev">&larr; Previous</a>
		<?php endif; ?>

		<ul>
			<?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
				<li>
					<?php if ($i == $pagination['page']): ?>
						<span class="page-link current" aria-current="page"><span class="visually-hidden">Page </span><?=$i?></span>
					<?php else: ?>
						<a class="page-link" href="<?=html(pageUrl($i))?>"><span class="visually-hidden">Page </span><?=$i?></a>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
		</ul>

		<?php if ($pagination['page'] < $pagination['pages']): ?>
			<a class="page-link" href="<?=html(pageUrl($pagination['page'] + 1))?>" rel="next">Next &rarr;</a>
		<?php endif; ?>
	</nav>
<?php endif; ?>
