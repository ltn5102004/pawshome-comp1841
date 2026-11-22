<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) ARIA: search role and status role. https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Roles/search_role
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<div class="section-head">
	<h1>Find a pet</h1>
	<a class="btn" href="addpet.php">+ Rehome a pet</a>
</div>

<form class="filters" action="pets.php" method="get" role="search" aria-label="Filter pets">
	<div class="field">
		<label for="search">Search</label>
		<input type="search" id="search" name="search" value="<?=html($filters['search'])?>" placeholder="Name or keyword">
	</div>
	<div class="field">
		<label for="speciesid">Species</label>
		<select id="speciesid" name="speciesid">
			<option value="">All species</option>
			<?php foreach ($species as $item): ?>
				<option value="<?=html($item['id'])?>" <?=$filters['speciesid'] == $item['id'] ? 'selected' : ''?>><?=html($item['name'])?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="field">
		<label for="shelterid">Shelter</label>
		<select id="shelterid" name="shelterid">
			<option value="">All shelters</option>
			<?php foreach ($shelters as $shelter): ?>
				<option value="<?=html($shelter['id'])?>" <?=$filters['shelterid'] == $shelter['id'] ? 'selected' : ''?>><?=html($shelter['name'])?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="field">
		<label for="status">Status</label>
		<select id="status" name="status">
			<option value="">Any status</option>
			<?php foreach (['Available', 'Reserved', 'Adopted'] as $status): ?>
				<option value="<?=$status?>" <?=$filters['status'] == $status ? 'selected' : ''?>><?=$status?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="field">
		<label for="sort">Sort by</label>
		<select id="sort" name="sort">
			<option value="newest" <?=$filters['sort'] == 'newest' ? 'selected' : ''?>>Newest arrivals</option>
			<option value="oldest" <?=$filters['sort'] == 'oldest' ? 'selected' : ''?>>Oldest first</option>
			<option value="name_asc" <?=$filters['sort'] == 'name_asc' ? 'selected' : ''?>>Name (A–Z)</option>
			<option value="name_desc" <?=$filters['sort'] == 'name_desc' ? 'selected' : ''?>>Name (Z–A)</option>
			<option value="age_asc" <?=$filters['sort'] == 'age_asc' ? 'selected' : ''?>>Age (Youngest)</option>
			<option value="age_desc" <?=$filters['sort'] == 'age_desc' ? 'selected' : ''?>>Age (Oldest)</option>
		</select>
	</div>
	<?php
	// Open the "More filters" box when one of its filters is in use
	$moreKeys = array_merge(['gender', 'size', 'age', 'city'], array_keys(compatibilityOptions()));
	$moreOpen = false;
	foreach ($moreKeys as $key) {
		if (!empty($filters[$key])) {
			$moreOpen = true;
		}
	}
	?>
	<?php // Source: MDN Web Docs (no date) <details>: The Details disclosure element. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/details ?>
	<details class="more-filters" <?=$moreOpen ? 'open' : ''?>>
		<summary>More filters</summary>
		<div class="more-filters-grid">
			<div class="field">
				<label for="gender">Gender</label>
				<select id="gender" name="gender">
					<option value="">Any gender</option>
					<?php foreach (['Male', 'Female'] as $gender): ?>
						<option value="<?=$gender?>" <?=$filters['gender'] == $gender ? 'selected' : ''?>><?=$gender?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="age">Age</label>
				<select id="age" name="age">
					<option value="">Any age</option>
					<?php foreach (['young' => 'Young (0-2 years)', 'adult' => 'Adult (3-7 years)', 'senior' => 'Senior (8+ years)'] as $value => $label): ?>
						<option value="<?=$value?>" <?=$filters['age'] == $value ? 'selected' : ''?>><?=$label?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="size">Size</label>
				<select id="size" name="size">
					<option value="">Any size</option>
					<?php foreach (petSizes() as $size): ?>
						<option value="<?=$size?>" <?=$filters['size'] == $size ? 'selected' : ''?>><?=$size?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="city">Area</label>
				<select id="city" name="city">
					<option value="">Anywhere</option>
					<?php foreach ($cities as $city): ?>
						<option value="<?=html($city)?>" <?=$filters['city'] == $city ? 'selected' : ''?>><?=html($city)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<fieldset class="field compat-filter">
				<legend>Must be&hellip;</legend>
				<div class="checkbox-group">
					<?php foreach (compatibilityOptions() as $column => $label): ?>
						<label class="checkbox"><input type="checkbox" name="<?=$column?>" value="1" <?=!empty($filters[$column]) ? 'checked' : ''?>> <?=html($label)?></label>
					<?php endforeach; ?>
				</div>
			</fieldset>
		</div>
	</details>

	<div class="filter-actions">
		<button class="btn" type="submit">Search</button>
		<a class="btn btn-outline" href="pets.php">Clear</a>
	</div>
</form>

<p class="result-count" role="status">
	<?=html($pagination['total'])?> <?=$pagination['total'] == 1 ? 'pet' : 'pets'?> found
	<?php if ($pagination['pages'] > 1): ?>
		&middot; page <?=$pagination['page']?> of <?=$pagination['pages']?>
	<?php endif; ?>
</p>

<?php if (empty($pets)): ?>
	<p class="empty">No pets match your search. Try removing a filter.</p>
<?php else: ?>
	<div class="grid">
		<?php foreach ($pets as $pet): ?>
			<?php include __DIR__ . '/petcard.html.php'; ?>
		<?php endforeach; ?>
	</div>
	<?php include __DIR__ . '/pagination.html.php'; ?>
<?php endif; ?>
