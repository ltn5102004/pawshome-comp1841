<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) <fieldset> and <legend> (radio buttons). https://developer.mozilla.org/en-US/docs/Web/HTML/Element/fieldset
//   - MDN Web Docs (no date) Client-side form validation (required). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - W3C WAI (no date) ARIA Authoring Practices Guide - Breadcrumb Pattern. https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
	<a href="myaccount.php">My account</a> <span aria-hidden="true">/</span> <span aria-current="page">Adopter profile</span>
</nav>

<section class="panel narrow">
	<h1>Adopter profile</h1>
	<p>Tell shelters about your home once. This profile is sent with every adoption request, so you do not have to repeat it.</p>
	<p class="hint">Fields marked <span class="required">*</span> are required.</p>

	<?php include __DIR__ . '/errors.html.php'; ?>

	<form action="adopterprofile.php" method="post" class="form">
		<?=csrfField()?>
		<input type="hidden" name="back" value="<?=html($back)?>">

		<fieldset class="field">
			<legend>Type of home <span class="required" aria-hidden="true">*</span></legend>
			<div class="radio-group radio-stack">
				<?php foreach (homeTypes() as $home): ?>
					<label class="radio"><input type="radio" name="hometype" value="<?=html($home)?>" required <?=$profile['hometype'] == $home ? 'checked' : ''?>> <?=html($home)?></label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<div class="field">
			<label class="checkbox"><input type="checkbox" name="householdagrees" value="1" <?=!empty($profile['householdagrees']) ? 'checked' : ''?>> Everyone I live with (and my landlord, if I rent) agrees to me adopting a pet</label>
		</div>

		<div class="field-row">
			<div class="field">
				<label for="experience">Experience with pets <span class="required" aria-hidden="true">*</span></label>
				<select id="experience" name="experience" required>
					<option value="">Choose one</option>
					<?php foreach (experienceLevels() as $level): ?>
						<option value="<?=html($level)?>" <?=$profile['experience'] == $level ? 'selected' : ''?>><?=html($level)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="budget">Monthly budget for the pet <span class="required" aria-hidden="true">*</span></label>
				<select id="budget" name="budget" required aria-describedby="budget-hint">
					<option value="">Choose one</option>
					<?php foreach (budgets() as $budget): ?>
						<option value="<?=html($budget)?>" <?=$profile['budget'] == $budget ? 'selected' : ''?>><?=html($budget)?></option>
					<?php endforeach; ?>
				</select>
				<p class="hint" id="budget-hint">Food, insurance and vet bills.</p>
			</div>
		</div>

		<div class="field">
			<label for="otherpets">Other pets at home</label>
			<input type="text" id="otherpets" name="otherpets" value="<?=html($profile['otherpets'] ?? '')?>" maxlength="255" placeholder="e.g. one 5-year-old cat">
		</div>

		<div class="form-actions">
			<button class="btn" type="submit">Save profile</button>
			<a class="btn btn-outline" href="<?=html($back)?>">Cancel</a>
		</div>
	</form>
</section>
