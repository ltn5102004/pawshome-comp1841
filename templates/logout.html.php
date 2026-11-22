<?php
// Shown when logout.php is opened with a plain link (GET): logging out needs the button below (POST + CSRF token).
// Source: COMP1841 lab materials (Prichard, 2026) - controller / template structure;
//         OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
?>
<section class="panel narrow small center">
	<h1>Log out</h1>
	<p>Do you want to log out of PawsHome?</p>
	<form action="logout.php" method="post" class="form">
		<?=csrfField()?>
		<button class="btn btn-block" type="submit">Log out</button>
	</form>
	<p><a href="myaccount.php">Stay logged in</a></p>
</section>
