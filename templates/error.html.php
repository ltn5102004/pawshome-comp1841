<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<section class="panel narrow center">
	<h1>Something went wrong</h1>
	<p><?=html($message)?></p>
	<p><a class="btn" href="index.php">Back to the home page</a></p>
</section>
