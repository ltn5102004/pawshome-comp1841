<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) HTML attribute: autocomplete (new-password) and aria-describedby. https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
?>
<section class="panel narrow small">
	<h1>Choose a new password</h1>

	<?php if (!$reset): ?>
		<p class="alert alert-error" role="alert">This link is not valid any more. Links work once and only for 30 minutes.</p>
		<p class="center"><a class="btn" href="forgotpassword.php">Ask for a new link</a></p>
	<?php else: ?>
		<p>Account: <strong><?=html($reset['email'])?></strong></p>

		<?php include __DIR__ . '/errors.html.php'; ?>

		<form action="resetpassword.php" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="token" value="<?=html($token)?>">
			<div class="field">
				<label for="password">New password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="reset-hint">
				<p class="hint" id="reset-hint">At least 8 characters, including a letter and a number.</p>
			</div>
			<div class="field">
				<label for="confirm">Confirm new password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="confirm" name="confirm" required minlength="8" autocomplete="new-password">
			</div>
			<button class="btn btn-block" type="submit">Save new password</button>
		</form>
	<?php endif; ?>
</section>
