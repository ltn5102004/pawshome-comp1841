<?php // Source: MDN Web Docs (no date) aria-pressed (toggle button) and ARIA live regions (role="status"). Heart icon is an SVG path drawn for this project. ?>
<?php // https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Roles/status_role ?>
<?php // Heart button to save / unsave a pet. Needs $pet and $favouriteIds (ids of the pets this member has saved). ?>
<?php $saved = in_array($pet['id'], $favouriteIds); ?>
<?php // .favourite-wrap holds the button AND the small message that js/main.js shows under it for 5 seconds ?>
<div class="favourite-wrap">
<form action="favourite.php" method="post" class="inline-form favourite-form">
	<?=csrfField()?>
	<input type="hidden" name="petid" value="<?=html($pet['id'])?>">
	<input type="hidden" name="back" value="<?=html(currentPage())?>">
	<button class="btn-favourite<?=$saved ? ' saved' : ''?>" type="submit" aria-pressed="<?=$saved ? 'true' : 'false'?>"
		title="<?=$saved ? 'Remove from favourites' : 'Save to favourites'?>">
		<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20"><path d="M12 21s-7.5-4.6-9.6-9.2C1 8.6 3 5 6.6 5c2.1 0 3.6 1.2 4.4 2.5h2C13.8 6.2 15.3 5 17.4 5 21 5 23 8.6 21.6 11.8 19.5 16.4 12 21 12 21z"/></svg>
		<span class="visually-hidden"><?=$saved ? 'Remove ' . html($pet['name']) . ' from favourites' : 'Save ' . html($pet['name']) . ' to favourites'?></span>
	</button>
</form>
<span class="favourite-note" role="status" aria-live="polite"></span>
</div>
