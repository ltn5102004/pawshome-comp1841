<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) Your first form / The label element. https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - MDN Web Docs (no date) HTML attribute: autocomplete. https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<section class="panel narrow">
	<h1>Contact the administrator</h1>
	<p>Questions about a pet, a shelter or your account? Send a message and the PawsHome administrator will reply by email.</p>

	<?php include __DIR__ . '/errors.html.php'; ?>

	<form action="contact.php" method="post" class="form">
		<?=csrfField()?>
		<div class="field">
			<label for="name">Your name <span class="required" aria-hidden="true">*</span></label>
			<input type="text" id="name" name="name" value="<?=html($contact['name'])?>" required maxlength="100" autocomplete="name">
		</div>
		<div class="field">
			<label for="email">Your email <span class="required" aria-hidden="true">*</span></label>
			<input type="email" id="email" name="email" value="<?=html($contact['email'])?>" required maxlength="255" autocomplete="email">
		</div>
		<div class="field">
			<label for="subject">Subject <span class="required" aria-hidden="true">*</span></label>
			<input type="text" id="subject" name="subject" value="<?=html($contact['subject'])?>" required maxlength="150">
		</div>
		<div class="field">
			<label for="body">Message <span class="required" aria-hidden="true">*</span></label>
			<textarea id="body" name="body" rows="6" required minlength="10" maxlength="2000" data-counter><?=html($contact['body'])?></textarea>
		</div>
		<button class="btn" type="submit">Send message</button>
	</form>
</section>
