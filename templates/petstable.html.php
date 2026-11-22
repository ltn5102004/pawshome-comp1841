<?php
// Source:
//   - Coyier, C. (2011) Responsive Data Tables, CSS-Tricks (data-label: each cell keeps its heading on phones). https://css-tricks.com/responsive-data-tables/
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
// Table of pets with edit / delete buttons. Needs $petList (rows from petSelect) and $tableCaption.
?>
	<div class="table-wrap">
		<table class="data-table">
			<caption class="visually-hidden"><?=html($tableCaption)?></caption>
			<thead>
				<tr><th scope="col">Pet</th><th scope="col">Where</th><th scope="col">Adoption status</th><th scope="col">Listing</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
			</thead>
			<tbody>
				<?php foreach ($petList as $pet): ?>
					<tr>
						<td data-label="Pet">
							<div class="cell-with-thumb">
								<img class="thumb" src="<?=html(imagePath($pet['image']))?>" alt="" width="56" height="42">
								<div>
									<a class="cell-title" href="pet.php?id=<?=html($pet['id'])?>"><?=html($pet['name'])?></a>
									<span class="cell-sub"><?=html($pet['speciesname'])?><?=$pet['breed'] ? ' &middot; ' . html($pet['breed']) : ''?></span>
								</div>
							</div>
						</td>
						<td data-label="Where"><?=html($pet['sheltername'] ?? 'Private rehoming')?><span class="cell-sub"><?=html($pet['sheltercity'])?></span></td>
						<td data-label="Adoption status"><span class="badge badge-<?=strtolower(html($pet['status']))?>"><?=html($pet['status'])?></span></td>
						<td data-label="Listing">
							<span class="badge badge-<?=strtolower(html($pet['approval']))?>"><?=html(reviewLabel($pet['approval']))?></span>
							<?php if ($pet['approval'] == 'Rejected' && $pet['reviewnote']): ?>
								<span class="cell-sub"><?=html($pet['reviewnote'])?></span>
							<?php endif; ?>
						</td>
						<td class="actions" data-label="Actions">
							<a class="btn btn-small btn-outline" href="editpet.php?id=<?=html($pet['id'])?>">Edit<span class="visually-hidden"> <?=html($pet['name'])?></span></a>
							<form action="deletepet.php" method="post" class="inline-form" data-confirm="Delete <?=html($pet['name'])?>? This cannot be undone.">
								<?=csrfField()?>
								<input type="hidden" name="id" value="<?=html($pet['id'])?>">
								<button class="btn btn-small btn-danger" type="submit">Delete<span class="visually-hidden"> <?=html($pet['name'])?></span></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
