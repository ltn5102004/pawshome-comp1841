<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - MDN Web Docs (no date) HTML attribute: autocomplete. https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<section class="panel narrow small">
	<h1>Create an account</h1>
	<p>An account lets you rehome a pet and send adoption requests.</p>

	<?php include __DIR__ . '/errors.html.php'; ?>

	<form action="register.php" method="post" class="form">
		<?=csrfField()?>
		<div class="field">
			<label for="name">Full name <span class="required" aria-hidden="true">*</span></label>
			<input type="text" id="name" name="name" value="<?=html($user['name'])?>" required maxlength="100" autocomplete="name">
		</div>
		<div class="field">
			<label for="email">Email <span class="required" aria-hidden="true">*</span></label>
			<input type="email" id="email" name="email" value="<?=html($user['email'])?>" required maxlength="255" autocomplete="email">
		</div>
		<div class="field">
			<label for="password">Password <span class="required" aria-hidden="true">*</span></label>
			<input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="password-hint">
			<p class="hint" id="password-hint">At least 8 characters, including a letter and a number.</p>
		</div>
		<div class="field">
			<label for="confirm">Confirm password <span class="required" aria-hidden="true">*</span></label>
			<input type="password" id="confirm" name="confirm" required minlength="8" autocomplete="new-password">
		</div>
		<button class="btn btn-block btn-cta" type="submit">Register</button>
	</form>
	<p class="center">Already registered? <a href="login.php">Log in</a></p>
</section>
