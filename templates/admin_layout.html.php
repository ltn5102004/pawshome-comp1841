<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system (layout.html.php)
//   - WebAIM (no date) Skip Navigation Links. https://webaim.org/techniques/skipnav/
//   - W3C WAI (no date) ARIA Authoring Practices Guide - Disclosure (Show/Hide) Pattern (menu button, aria-expanded). https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
//   - MDN Web Docs (no date) aria-current. https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Attributes/aria-current
//   - MDN Web Docs (no date) CSS grid layout (sidebar + main area). https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
// Admin pages: a sidebar menu on the left (a menu button on phones) and the page on the right.
?>
<?php
$flash = getFlash();
$page = basename($_SERVER['SCRIPT_NAME']);
// numbers next to the menu items (only when the database is connected)
$badges = (isset($pdo) && function_exists('adminBadgeCounts')) ? adminBadgeCounts($pdo) : ['review' => 0, 'newrequests' => 0, 'messages' => 0];
// menu: file => [label, icon, badge number, what the badge means]
$menu = [
	'index.php' => ['Dashboard', 'dashboard', 0, ''],
	'approvals.php' => ['Review listings', 'review', $badges['review'], 'waiting for review'],
	'requests.php' => ['Adoption requests', 'requests', $badges['newrequests'], 'new'],
	'pets.php' => ['Pets', 'pets', 0, ''],
	'shelters.php' => ['Shelters', 'shelters', 0, ''],
	'species.php' => ['Species', 'species', 0, ''],
	'users.php' => ['Users', 'users', 0, ''],
	'messages.php' => ['Messages', 'messages', $badges['messages'], 'messages']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=html($title)?> | PawsHome Admin</title>
	<link rel="stylesheet" href="<?=asset('css/style.css', '../')?>">
	<link rel="icon" href="<?=asset('images/logo.svg', '../')?>" type="image/svg+xml">
</head>
<body id="admin" class="admin">
	<a class="skip-link" href="#main">Skip to main content</a>

	<div class="admin-shell">
		<aside class="admin-sidebar">
			<div class="admin-brand-row">
				<a class="brand" href="index.php">
					<img src="<?=asset('images/logo.svg', '../')?>" alt="" width="34" height="34">
					<span>PawsHome <small>Admin</small></span>
				</a>
				<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
					<span class="nav-toggle-bar" aria-hidden="true"></span>
					<span class="visually-hidden">Menu</span>
				</button>
			</div>

			<nav id="main-nav" class="admin-nav" aria-label="Admin">
				<ul>
					<?php foreach ($menu as $file => $item): ?>
						<li>
							<a href="<?=$file?>" <?=$page == $file ? 'aria-current="page"' : ''?>>
								<?=icon($item[1])?>
								<span class="admin-nav-label"><?=html($item[0])?></span>
								<?php if ($item[2]): ?>
									<span class="count-badge"><?=html($item[2])?><span class="visually-hidden"> <?=html($item[3])?></span></span>
								<?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<ul class="admin-nav-bottom">
					<li><a href="../index.php"><?=icon('site')?><span class="admin-nav-label">View the site</span></a></li>
					<li><a href="../logout.php"><?=icon('logout')?><span class="admin-nav-label">Log out</span></a></li>
				</ul>
				<p class="admin-user">Signed in as<br><strong><?=html(currentUser()['name'] ?? '')?></strong></p>
			</nav>
		</aside>

		<main id="main" class="admin-main" tabindex="-1">
			<?php if ($flash): ?>
				<div class="alert alert-<?=html($flash['type'])?>" role="<?=$flash['type'] == 'error' ? 'alert' : 'status'?>" aria-live="polite"><?=html($flash['message'])?></div>
			<?php endif; ?>
			<?=$output?>
		</main>
	</div>
	<script src="<?=asset('js/main.js', '../')?>"></script>
</body>
</html>
