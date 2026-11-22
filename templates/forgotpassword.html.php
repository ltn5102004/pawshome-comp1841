<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) HTML attribute: autocomplete. https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
//   - OWASP (no date) Forgot Password Cheat Sheet (one message for every email). https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html
?>
<section class="panel narrow small">
	<h1>Forgot your password?</h1>

	<?php if ($sent): ?>
		<p class="alert alert-success" role="status">If an account uses <strong><?=html($email)?></strong>, we have sent it a link to choose a new password. The link works for 30 minutes.</p>
		<p class="center"><a href="login.php">Back to log in</a></p>
	<?php else: ?>
		<p>Enter the email address of your account and we will send you a link to choose a new password.</p>

		<?php include __DIR__ . '/errors.html.php'; ?>

		<form action="forgotpassword.php" method="post" class="form">
			<?=csrfField()?>
			<div class="field">
				<label for="email">Email</label>
				<input type="email" id="email" name="email" value="<?=html($email)?>" required maxlength="255" autocomplete="email">
			</div>
			<button class="btn btn-block" type="submit">Send reset link</button>
		</form>
		<p class="center"><a href="login.php">Back to log in</a></p>
	<?php endif; ?>
</section>
