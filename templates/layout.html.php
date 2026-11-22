<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system (layout.html.php)
//   - MDN Web Docs (no date) Document and website structure. https://developer.mozilla.org/en-US/docs/Learn/HTML/Introduction_to_HTML/Document_and_website_structure
//   - WebAIM (no date) Skip Navigation Links. https://webaim.org/techniques/skipnav/
//   - W3C WAI (no date) ARIA Authoring Practices Guide - Disclosure (Show/Hide) Pattern (menu button, aria-expanded). https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
//   - MDN Web Docs (no date) aria-current. https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-current
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<?php $flash = getFlash(); $page = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="PawsHome - find a pet to adopt from shelters near you.">
	<title><?=html($title)?> | PawsHome</title>
	<link rel="stylesheet" href="<?=asset('css/style.css')?>">
	<link rel="icon" href="<?=asset('images/logo.svg')?>" type="image/svg+xml">
</head>
<body>
	<a class="skip-link" href="#main">Skip to main content</a>

	<header class="site-header">
		<div class="container header-inner">
			<a class="brand" href="index.php">
				<img src="<?=asset('images/logo.svg')?>" alt="" width="36" height="36">
				<span>PawsHome</span>
			</a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
				<span class="nav-toggle-bar" aria-hidden="true"></span>
				<span class="visually-hidden">Menu</span>
			</button>
			<nav id="main-nav" class="main-nav" aria-label="Main">
				<ul>
					<li><a href="index.php" <?=$page == 'index.php' ? 'aria-current="page"' : ''?>>Home</a></li>
					<li><a href="pets.php" <?=$page == 'pets.php' ? 'aria-current="page"' : ''?>>Find a pet</a></li>
					<li><a href="shelters.php" <?=$page == 'shelters.php' ? 'aria-current="page"' : ''?>>Shelters</a></li>
					<li><a href="addpet.php" <?=$page == 'addpet.php' ? 'aria-current="page"' : ''?>>Rehome a pet</a></li>
					<li><a href="contact.php" <?=$page == 'contact.php' ? 'aria-current="page"' : ''?>>Contact</a></li>
				</ul>
				<ul class="nav-account">
					<?php if (isLoggedIn()): ?>
						<?php if (isAdmin()): ?>
							<li><a class="nav-admin" href="admin/index.php">Admin</a></li>
						<?php endif; ?>
						<?php // Menu link for people who answer adoption requests (shelter staff, or members who listed a pet) ?>
						<?php $incoming = (!isAdmin() && isset($pdo) && function_exists('countRequestsToHandle')) ? countRequestsToHandle($pdo, currentUserId(), currentShelterId()) : ['total' => 0, 'new' => 0]; ?>
						<?php if ($incoming['total'] > 0 || isShelterStaff()): ?>
							<li><a href="requests.php" <?=$page == 'requests.php' ? 'aria-current="page"' : ''?>>Requests<?php if ($incoming['new']): ?> <span class="count-badge" aria-label="<?=html($incoming['new'])?> new"><?=html($incoming['new'])?></span><?php endif; ?></a></li>
						<?php endif; ?>
						<li><a href="myaccount.php" <?=in_array($page, ['myaccount.php', 'profile.php']) ? 'aria-current="page"' : ''?>>My account</a></li>
						<?php // Log out is a POST form with the CSRF token (LIM-01), styled like the other menu links ?>
						<li><form action="logout.php" method="post" class="logout-form"><?=csrfField()?><button class="link-button" type="submit">Log out</button></form></li>
					<?php else: ?>
						<li><a href="login.php" <?=$page == 'login.php' ? 'aria-current="page"' : ''?>>Log in</a></li>
						<li><a class="btn btn-small" href="register.php" <?=$page == 'register.php' ? 'aria-current="page"' : ''?>>Register</a></li>
					<?php endif; ?>
				</ul>
			</nav>
		</div>
	</header>

	<main id="main" class="container" tabindex="-1">
		<?php if ($flash): ?>
			<div class="alert alert-<?=html($flash['type'])?>" role="<?=$flash['type'] == 'error' ? 'alert' : 'status'?>" aria-live="polite"><?=html($flash['message'])?></div>
		<?php endif; ?>
		<?=$output?>
	</main>

	<?php include __DIR__ . '/footer.html.php'; ?>
	<script src="<?=asset('js/main.js')?>"></script>
</body>
</html>
