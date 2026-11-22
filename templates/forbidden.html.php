<?php
// Page shown with HTTP 403 when a form arrives without a valid CSRF token (used by requireCsrfToken() in includes/Functions.php).
// It is a small stand-alone page, so it works from the public pages and from admin/ (where $base is '../').
// Source:
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) 403 Forbidden. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/403
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Security check failed | PawsHome</title>
	<link rel="stylesheet" href="<?=asset('css/style.css', $base)?>">
	<link rel="icon" href="<?=asset('images/logo.svg', $base)?>" type="image/svg+xml">
</head>
<body>
	<main id="main" class="container">
		<section class="panel narrow center">
			<h1>Security check failed</h1>
			<p class="alert alert-error" role="alert">The form could not be sent because its security token was missing or out of date (for example, the page was open for a long time). Nothing has been changed.</p>
			<p><a class="btn" href="<?=html($back)?>">Go back and try again</a></p>
		</section>
	</main>
</body>
</html>
