<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - W3Schools (no date) PHP File Upload (form with enctype=multipart/form-data). https://www.w3schools.com/php/php_file_upload.asp
//   - MDN Web Docs (no date) <fieldset> and <legend> (radio buttons). https://developer.mozilla.org/en-US/docs/Web/HTML/Element/fieldset
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<section class="panel narrow">
	<h1><?=html($title)?></h1>
	<p class="hint">Fields marked <span class="required">*</span> are required.</p>

	<?php include __DIR__ . '/errors.html.php'; ?>

	<form action="" method="post" enctype="multipart/form-data" class="form">
		<?=csrfField()?>
		<?php if (!empty($pet['id'])): ?>
			<input type="hidden" name="pet[id]" value="<?=html($pet['id'])?>">
		<?php endif; ?>

		<div class="field">
			<label for="name">Pet name <span class="required" aria-hidden="true">*</span></label>
			<input type="text" id="name" name="pet[name]" value="<?=html($pet['name'] ?? '')?>" required maxlength="50" autocomplete="off">
		</div>

		<div class="field-row">
			<div class="field">
				<label for="speciesid">Species <span class="required" aria-hidden="true">*</span></label>
				<select id="speciesid" name="pet[speciesid]" required>
					<option value="">Choose a species</option>
					<?php foreach ($species as $item): ?>
						<option value="<?=html($item['id'])?>" <?=($pet['speciesid'] ?? '') == $item['id'] ? 'selected' : ''?>><?=html($item['name'])?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="age">Age in years <span class="required" aria-hidden="true">*</span></label>
				<input type="number" id="age" name="pet[age]" value="<?=html($pet['age'] ?? '')?>" required min="0" max="30" step="1" inputmode="numeric">
			</div>
		</div>

		<fieldset class="field">
			<legend>Gender <span class="required" aria-hidden="true">*</span></legend>
			<div class="radio-group">
				<?php foreach (['Male', 'Female'] as $gender): ?>
					<label class="radio">
						<input type="radio" name="pet[gender]" value="<?=$gender?>" required <?=($pet['gender'] ?? '') == $gender ? 'checked' : ''?>>
						<?=$gender?>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<div class="field-row">
			<?php // Who can choose where the pet is: admins pick a shelter; staff and existing shelter pets are fixed; members give a city ?>
			<?php if ($canChooseShelter): ?>
				<div class="field">
					<label for="shelterid">Shelter</label>
					<select id="shelterid" name="pet[shelterid]" aria-describedby="shelterid-hint">
						<option value="">No shelter (private rehoming)</option>
						<?php foreach ($shelters as $shelter): ?>
							<option value="<?=html($shelter['id'])?>" <?=($pet['shelterid'] ?? '') == $shelter['id'] ? 'selected' : ''?>><?=html($shelter['name'])?> (<?=html($shelter['city'])?>)</option>
						<?php endforeach; ?>
					</select>
					<p class="hint" id="shelterid-hint">For a private rehoming, leave this empty and fill in the city below.</p>
				</div>
			<?php elseif ($fixedShelterId): ?>
				<div class="field">
					<span class="label-text">Shelter</span>
					<?php foreach ($shelters as $shelter): ?>
						<?php if ($shelter['id'] == $fixedShelterId): ?>
							<p class="fixed-value"><strong><?=html($shelter['name'])?></strong> (<?=html($shelter['city'])?>)</p>
						<?php endif; ?>
					<?php endforeach; ?>
					<p class="hint">The shelter is set by your account and cannot be changed here.</p>
				</div>
			<?php endif; ?>
			<?php // Status follows the adoption process: new pets are Available; Reserved is set by an approved request ?>
			<?php if (count($statusOptions) > 1): ?>
				<div class="field">
					<label for="status">Status <span class="required" aria-hidden="true">*</span></label>
					<select id="status" name="pet[status]" required aria-describedby="status-hint">
						<?php foreach ($statusOptions as $status): ?>
							<option value="<?=$status?>" <?=($pet['status'] ?? 'Available') == $status ? 'selected' : ''?>><?=$status == 'Reserved' ? 'Reserved (an adoption request was approved)' : $status?></option>
						<?php endforeach; ?>
					</select>
					<p class="hint" id="status-hint">Choose <strong>Adopted</strong> once the pet has gone to its new home. <strong>Reserved</strong> is set automatically when you approve an adoption request.</p>
				</div>
			<?php else: ?>
				<div class="field">
					<span class="label-text">Status</span>
					<p class="fixed-value">Available</p>
					<p class="hint">New pets start as Available. The pet becomes Reserved when you approve an adoption request, and you can mark it Adopted when it has found its home.</p>
				</div>
			<?php endif; ?>
		</div>

		<div class="field">
			<label for="description">Description <span class="required" aria-hidden="true">*</span></label>
			<textarea id="description" name="pet[description]" rows="6" required minlength="10" maxlength="2000" aria-describedby="description-hint" data-counter><?=html($pet['description'] ?? '')?></textarea>
			<p class="hint" id="description-hint">Personality, health, and the kind of home that would suit this pet (10&ndash;2000 characters).</p>
		</div>

		<?php if (empty($fixedShelterId)): ?>
			<div class="field">
				<label for="city">City where the pet is <?php if (!$canChooseShelter): ?><span class="required" aria-hidden="true">*</span><?php endif; ?></label>
				<input type="text" id="city" name="pet[city]" value="<?=html($pet['city'] ?? '')?>" maxlength="100" <?=$canChooseShelter ? '' : 'required'?> placeholder="e.g. Leeds" autocomplete="address-level2">
				<?php if (!$canChooseShelter): ?>
					<p class="hint">This listing is a <strong>private rehoming</strong>. It is checked by an admin before it appears on the site.</p>
				<?php endif; ?>
			</div>
		<?php elseif (!$canChooseShelter && isShelterStaff() && empty($pet['id'])): ?>
			<?php // New listing by shelter staff: say whether it is published at once ?>
			<p class="profile-note">
				<?php if (currentUser()['shelterverified']): ?>
					Your shelter is verified, so this listing is published straight away.
				<?php else: ?>
					Your shelter has not been verified yet, so an admin will check this listing before it appears.
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<h2 class="form-section">Health &amp; personality</h2>

		<div class="field-row">
			<div class="field">
				<label for="breed">Breed</label>
				<input type="text" id="breed" name="pet[breed]" value="<?=html($pet['breed'] ?? '')?>" maxlength="80" placeholder="e.g. Beagle, Tabby, Lop rabbit">
			</div>
			<div class="field">
				<label for="size">Size <span class="required" aria-hidden="true">*</span></label>
				<select id="size" name="pet[size]" required>
					<?php foreach (petSizes() as $size): ?>
						<option value="<?=$size?>" <?=($pet['size'] ?? 'Medium') == $size ? 'selected' : ''?>><?=$size?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<fieldset class="field">
			<legend>Energy level <span class="required" aria-hidden="true">*</span></legend>
			<div class="radio-group">
				<?php foreach (energyLevels() as $energy): ?>
					<label class="radio">
						<input type="radio" name="pet[energy]" value="<?=$energy?>" required <?=($pet['energy'] ?? 'Medium') == $energy ? 'checked' : ''?>>
						<?=$energy?>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<fieldset class="field">
			<legend>Health</legend>
			<div class="checkbox-group">
				<label class="checkbox"><input type="checkbox" name="pet[vaccinated]" value="1" <?=!empty($pet['vaccinated']) ? 'checked' : ''?>> Vaccinated</label>
				<label class="checkbox"><input type="checkbox" name="pet[neutered]" value="1" <?=!empty($pet['neutered']) ? 'checked' : ''?>> Neutered / spayed</label>
			</div>
		</fieldset>

		<div class="field">
			<label for="healthnotes">Health conditions</label>
			<textarea id="healthnotes" name="pet[healthnotes]" rows="2" maxlength="500" aria-describedby="healthnotes-hint"><?=html($pet['healthnotes'] ?? '')?></textarea>
			<p class="hint" id="healthnotes-hint">Any illness, allergy or medicine the new owner should know about. Leave empty if none.</p>
		</div>

		<fieldset class="field">
			<legend>Would suit a home where&hellip;</legend>
			<div class="checkbox-group">
				<?php foreach (compatibilityOptions() as $column => $label): ?>
					<label class="checkbox"><input type="checkbox" name="pet[<?=$column?>]" value="1" <?=!empty($pet[$column]) ? 'checked' : ''?>> <?=html($label)?></label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<div class="field">
			<label for="image">Photo</label>
			<?php if (!empty($pet['image'])): ?>
				<div class="current-image">
					<img src="<?=html(imagePath($pet['image']))?>" alt="Current photo of <?=html($pet['name'])?>" width="160" height="120">
					<label class="checkbox"><input type="checkbox" name="removeimage" value="1"> Remove current photo</label>
				</div>
			<?php endif; ?>
			<input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" aria-describedby="image-hint">
			<p class="hint" id="image-hint">JPG, PNG, GIF or WEBP, maximum 2MB.<?=!empty($pet['image']) ? ' Choosing a new file replaces the current photo.' : ''?></p>
			<img class="image-preview" id="image-preview" alt="Preview of the chosen photo" hidden>
		</div>

		<div class="form-actions">
			<button class="btn btn-cta" type="submit" name="submit" value="1"><?=html($buttonText)?></button>
			<a class="btn btn-outline" href="<?=html($cancelUrl)?>">Cancel</a>
		</div>
	</form>
</section>
