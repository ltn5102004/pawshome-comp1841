<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - Coyier, C. (2011) Responsive Data Tables, CSS-Tricks. https://css-tricks.com/responsive-data-tables/
//   - MDN Web Docs (no date) HTML table accessibility (caption, scope). https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<header class="admin-head">
	<div>
		<h1>Shelters</h1>
		<p class="admin-sub">Partner shelters. Staff of a <strong>verified</strong> shelter can publish listings without review.</p>
	</div>
</header>

<div class="admin-split">
	<section class="admin-card">
		<h2><?=$shelter['id'] == '' ? 'Add a shelter' : 'Edit ' . html($shelter['name'])?></h2>
		<?php include __DIR__ . '/../errors.html.php'; ?>
		<form action="shelters.php<?=$shelter['id'] != '' ? '?edit=' . html($shelter['id']) : ''?>" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="shelter[id]" value="<?=html($shelter['id'])?>">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" id="name" name="shelter[name]" value="<?=html($shelter['name'])?>" required maxlength="100">
			</div>
			<div class="field">
				<label for="city">City</label>
				<input type="text" id="city" name="shelter[city]" value="<?=html($shelter['city'])?>" required maxlength="100">
			</div>
			<div class="field">
				<label for="email">Email</label>
				<input type="email" id="email" name="shelter[email]" value="<?=html($shelter['email'])?>" required maxlength="255">
			</div>
			<div class="field">
				<label for="phone">Phone</label>
				<input type="tel" id="phone" name="shelter[phone]" value="<?=html($shelter['phone'])?>" required pattern="[0-9 +()\-]{6,30}">
			</div>
			<div class="field">
				<label class="checkbox"><input type="checkbox" name="shelter[verified]" value="1" <?=!empty($shelter['verified']) ? 'checked' : ''?>> Verified shelter (checked by the PawsHome team)</label>
			</div>
			<div class="form-actions">
				<button class="btn" type="submit"><?=$shelter['id'] == '' ? 'Add shelter' : 'Save changes'?></button>
				<?php if ($shelter['id'] != ''): ?><a class="btn btn-outline" href="shelters.php">Cancel</a><?php endif; ?>
			</div>
		</form>
	</section>

	<section class="admin-card">
		<?php $filterTarget = 'shelters-table'; $filterLabel = 'Search by name or city'; include __DIR__ . '/tablefilter.html.php'; ?>
		<div class="table-wrap">
			<table class="admin-table" id="shelters-table">
				<caption class="visually-hidden">Shelters</caption>
				<thead><tr><th scope="col">Shelter</th><th scope="col">Contact</th><th scope="col">Pets</th><th scope="col">Staff</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
				<tbody>
					<?php foreach ($shelters as $row): ?>
						<tr>
							<td data-label="Shelter">
								<span class="cell-title"><?=html($row['name'])?></span>
								<span class="cell-sub"><?=html($row['city'])?> &middot; <?php if ($row['verified']): ?><span class="badge badge-verified"><span aria-hidden="true">&#10003;</span> Verified</span><?php else: ?><span class="badge badge-unverified">Not verified</span><?php endif; ?></span>
							</td>
							<td data-label="Contact"><a href="mailto:<?=html($row['email'])?>"><?=html($row['email'])?></a><span class="cell-sub"><?=html($row['phone'])?></span></td>
							<td data-label="Pets"><?=html($row['petcount'])?></td>
							<td data-label="Staff"><?=html($row['staffcount'])?></td>
							<td class="actions" data-label="Actions">
								<a class="btn btn-small btn-outline" href="shelters.php?edit=<?=html($row['id'])?>">Edit<span class="visually-hidden"> <?=html($row['name'])?></span></a>
								<form action="shelters.php" method="post" class="inline-form" data-confirm="Delete <?=html($row['name'])?>?">
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
