<?php // Site footer, included at the bottom of layout.html.php so it is the same on every public page ?>
<?php // Source: MDN Web Docs (no date) <footer> and <nav> elements, aria-labelledby; layout in css/style.css (CSS grid). ?>
<footer class="site-footer site-footer-full">
	<div class="container footer-grid">
		<div class="footer-about">
			<a class="brand footer-brand" href="index.php">
				<img src="<?=asset('images/logo.svg')?>" alt="" width="32" height="32">
				<span>PawsHome</span>
			</a>
			<p>Helping shelter pets find a forever home. Browse animals from our partner shelters or rehome a pet responsibly.</p>
		</div>

		<nav class="footer-col" aria-labelledby="footer-explore">
			<h2 id="footer-explore">Explore</h2>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="pets.php">Find a pet</a></li>
				<li><a href="pets.php?status=Available">Available now</a></li>
				<li><a href="shelters.php">Shelters</a></li>
				<li><a href="addpet.php">Rehome a pet</a></li>
			</ul>
		</nav>

		<nav class="footer-col" aria-labelledby="footer-account">
			<h2 id="footer-account">Your account</h2>
			<ul>
				<?php if (isLoggedIn()): ?>
					<li><a href="myaccount.php">My account</a></li>
					<li><a href="myaccount.php#favourites">My favourites</a></li>
					<li><a href="profile.php">Edit profile</a></li>
					<?php if (isAdmin()): ?>
						<li><a href="admin/index.php">Admin area</a></li>
					<?php endif; ?>
					<li><form action="logout.php" method="post" class="logout-form"><?=csrfField()?><button class="link-button" type="submit">Log out</button></form></li>
				<?php else: ?>
					<li><a href="login.php">Log in</a></li>
					<li><a href="register.php">Create an account</a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<div class="footer-col">
			<h2>Get in touch</h2>
			<ul>
				<li><a href="contact.php">Contact the administrator</a></li>
				<li>Old Royal Naval College, Greenwich, London SE10 9LS</li>
				<li>Mon &ndash; Fri, 9am &ndash; 5pm</li>
			</ul>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container footer-bottom-inner">
			<p>&copy; <?=date('Y')?> PawsHome &middot; A Greenwich IT Solutions demo for COMP1841 coursework</p>
		</div>
	</div>
</footer>

<!-- Round "back to top" button. It is hidden until the page is scrolled (see js/main.js) -->
<!-- Source: adapted from W3Schools (no date) How To - Scroll Back To Top Button; arrow icon adapted from Google (no date) Material Icons "arrow_upward" (Apache License 2.0). -->
<a href="#main" class="to-top" id="to-top" aria-label="Back to top" title="Back to top">
	<svg aria-hidden="true" viewBox="0 0 24 24" width="24" height="24"><path d="M12 5l-7 7 1.4 1.4L11 8.8V20h2V8.8l4.6 4.6L19 12z" fill="currentColor"/></svg>
</a>
