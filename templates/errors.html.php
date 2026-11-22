<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - W3C WAI (no date) ARIA Authoring Practices Guide - Alert Pattern (role=alert). https://www.w3.org/WAI/ARIA/apg/patterns/alert/
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<?php if (!empty($errors)): ?>
	<div class="alert alert-error" role="alert" id="form-errors" tabindex="-1">
		<p><strong>Please fix the following:</strong></p>
		<ul>
			<?php foreach ($errors as $error): ?>
				<li><?=html($error)?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
