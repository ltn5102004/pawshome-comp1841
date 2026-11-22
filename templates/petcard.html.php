<article class="card pet-card">
	<a class="card-image" href="pet.php?id=<?=html($pet['id'])?>" tabindex="-1" aria-hidden="true">
		<img src="<?=html(imagePath($pet['image']))?>" alt="<?=html($pet['name'])?>" loading="lazy" width="400" height="300">
	</a>
	<div class="card-body">
		<div class="card-top">
			<h3><a href="pet.php?id=<?=html($pet['id'])?>"><?=html($pet['name'])?></a></h3>
			<span class="badge badge-<?=strtolower(html($pet['status']))?>"><?=html($pet['status'])?></span>
		</div>
		<p class="meta"><?=html($pet['breed'] ?: $pet['speciesname'])?> &middot; <?=html($pet['gender'])?> &middot; <?=html($pet['age'])?> <?=$pet['age'] == 1 ? 'year' : 'years'?> &middot; <?=html($pet['size'])?></p>
		<p class="meta meta-place">
			<?php // Source: map pin icon adapted from Google (no date) Material Icons "place" (Apache License 2.0). ?>
			<svg aria-hidden="true" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
			<?=html(placeLabel($pet))?>
		</p>
		<?php
		// Source: MDN Web Docs (no date) <ul> and aria-label. https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-label
		// Small tags for the "good with" options that are true for this pet (the list is left out when there are none)
		$tags = [];
		foreach (compatibilityOptions() as $column => $label) {
			if ($pet[$column]) {
				$tags[] = $label;
			}
		}
		?>
		<?php if ($tags): ?>
			<ul class="tag-list" aria-label="Suits">
				<?php foreach ($tags as $tag): ?>
					<li class="tag"><?=html($tag)?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<p class="trust-row"><?php include __DIR__ . '/trustbadge.html.php'; ?></p>
		<div class="card-footer">
			<span class="meta favourite-count">&hearts; <span data-count-for="<?=html($pet['id'])?>"><?=html($pet['favouritecount'])?></span> <span class="visually-hidden">people saved this pet</span></span>
			<?php if (isLoggedIn()): ?>
				<?php include __DIR__ . '/favouritebutton.html.php'; ?>
			<?php endif; ?>
		</div>
	</div>
</article>
