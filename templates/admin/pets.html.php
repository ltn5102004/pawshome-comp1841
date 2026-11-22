<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - Coyier, C. (2011) Responsive Data Tables, CSS-Tricks (data-label + attr() so each cell keeps its heading on phones). https://css-tricks.com/responsive-data-tables/
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Pets</h1>
		<p class="admin-sub">Every listing, including the ones that are not published yet.</p>
	</div>
	<div class="admin-head-actions">
		<a class="btn" href="../addpet.php"><?=icon('plus')?> Add a pet</a>
	</div>
</header>

<section class="admin-card">
	<?php $filterTarget = 'pets-table'; $filterLabel = 'Search by name, species, shelter or city'; include __DIR__ . '/tablefilter.html.php'; ?>
	<div class="table-wrap">
		<table class="admin-table" id="pets-table">
			<caption class="visually-hidden">All pets</caption>
			<thead>
				<tr><th scope="col">Pet</th><th scope="col">Where</th><th scope="col">Listed by</th><th scope="col">Added</th><th scope="col">Adoption status</th><th scope="col">Listing</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
			</thead>
			<tbody>
				<?php foreach ($pets as $pet): ?>
					<tr>
						<td data-label="Pet">
							<div class="cell-with-thumb">
								<img class="thumb" src="<?=html(imagePath($pet['image'], '../'))?>" alt="" width="56" height="42">
								<div>
									<a class="cell-title" href="../pet.php?id=<?=html($pet['id'])?>"><?=html($pet['name'])?></a>
									<span class="cell-sub"><?=html($pet['speciesname'])?><?=$pet['breed'] ? ' &middot; ' . html($pet['breed']) : ''?></span>
								</div>
							</div>
						</td>
						<td data-label="Where"><?=html($pet['sheltername'] ?? 'Private rehoming')?><span class="cell-sub"><?=html($pet['sheltercity'])?></span></td>
						<td data-label="Listed by"><?=html(listedByLabel($pet))?></td>
						<td data-label="Added"><?=formatDate($pet['dateadded'])?></td>
						<td data-label="Adoption status"><span class="badge badge-<?=strtolower(html($pet['status']))?>"><?=html($pet['status'])?></span></td>
						<td data-label="Listing"><span class="badge badge-<?=strtolower(html($pet['approval']))?>"><?=html(reviewLabel($pet['approval']))?></span></td>
						<td class="actions" data-label="Actions">
							<a class="btn btn-small btn-outline" href="../editpet.php?id=<?=html($pet['id'])?>">Edit<span class="visually-hidden"> <?=html($pet['name'])?></span></a>
							<form action="../deletepet.php" method="post" class="inline-form" data-confirm="Delete <?=html($pet['name'])?>? This cannot be undone.">
								<?=csrfField()?>
								<input type="hidden" name="id" value="<?=html($pet['id'])?>">
								<input type="hidden" name="back" value="admin">
								<button class="btn btn-small btn-danger" type="submit">Delete<span class="visually-hidden"> <?=html($pet['name'])?></span></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
