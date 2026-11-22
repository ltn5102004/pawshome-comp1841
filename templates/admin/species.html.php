<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Species</h1>
		<p class="admin-sub">The kinds of animal a pet can be. A species that pets still use cannot be deleted.</p>
	</div>
</header>

<div class="admin-split">
	<section class="admin-card">
		<h2><?=$item['id'] == '' ? 'Add a species' : 'Edit ' . html($item['name'])?></h2>
		<?php include __DIR__ . '/../errors.html.php'; ?>
		<form action="species.php<?=$item['id'] != '' ? '?edit=' . html($item['id']) : ''?>" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="species[id]" value="<?=html($item['id'])?>">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" id="name" name="species[name]" value="<?=html($item['name'])?>" required maxlength="50">
			</div>
			<div class="form-actions">
				<button class="btn" type="submit"><?=$item['id'] == '' ? 'Add species' : 'Save changes'?></button>
				<?php if ($item['id'] != ''): ?><a class="btn btn-outline" href="species.php">Cancel</a><?php endif; ?>
			</div>
		</form>
	</section>

	<section class="admin-card">
		<div class="table-wrap">
			<table class="admin-table" id="species-table">
				<caption class="visually-hidden">Species</caption>
				<thead><tr><th scope="col">Name</th><th scope="col">Pets</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
				<tbody>
					<?php foreach ($speciesList as $row): ?>
						<tr>
							<td data-label="Name"><?=html($row['name'])?></td>
							<td data-label="Pets"><?=html($row['petcount'])?></td>
							<td class="actions" data-label="Actions">
								<a class="btn btn-small btn-outline" href="species.php?edit=<?=html($row['id'])?>">Edit<span class="visually-hidden"> <?=html($row['name'])?></span></a>
								<form action="species.php" method="post" class="inline-form" data-confirm="Delete <?=html($row['name'])?>?">
									<?=csrfField()?>
									<input type="hidden" name="delete" value="<?=html($row['id'])?>">
									<button class="btn btn-small btn-danger" type="submit">Delete<span class="visually-hidden"> <?=html($row['name'])?></span></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>
