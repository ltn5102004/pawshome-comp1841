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
	<h1>Log in</h1>

	<?php include __DIR__ . '/errors.html.php'; ?>

	<form action="login.php" method="post" class="form">
		<?=csrfField()?>
		<div class="field">
			<label for="email">Email</label>
			<input type="email" id="email" name="email" value="<?=html($email)?>" required autocomplete="username">
		</div>
		<div class="field">
			<label for="password">Password</label>
			<input type="password" id="password" name="password" required autocomplete="current-password">
		</div>
		<button class="btn btn-block" type="submit">Log in</button>
	</form>
	<p class="center">New to PawsHome? <a href="register.php">Create an account</a></p>
</section>
