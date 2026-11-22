<?php // Source: MDN Web Docs (no date) autocomplete attribute (current-password / new-password) and aria-describedby. ?>
<nav class="breadcrumb" aria-label="Breadcrumb">
	<a href="myaccount.php">My account</a> <span aria-hidden="true">/</span> <span aria-current="page">Edit profile</span>
</nav>

<h1>Edit profile</h1>

<div class="profile-grid">
	<section class="panel" aria-labelledby="details-heading">
		<h2 id="details-heading">Your details</h2>

		<?php $errors = $detailErrors; include __DIR__ . '/errors.html.php'; ?>

		<form action="profile.php" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="form" value="details">
			<div class="field">
				<label for="name">Full name <span class="required" aria-hidden="true">*</span></label>
				<input type="text" id="name" name="name" value="<?=html($user['name'])?>" required maxlength="100" autocomplete="name">
			</div>
			<div class="field">
				<label for="email">Email <span class="required" aria-hidden="true">*</span></label>
				<input type="email" id="email" name="email" value="<?=html($user['email'])?>" required maxlength="255" autocomplete="email">
			</div>
			<?php // LIM-02: the current password is checked only when the email address is changed ?>
			<div class="field">
				<label for="details-current">Current password</label>
				<input type="password" id="details-current" name="current" autocomplete="current-password" aria-describedby="details-current-hint">
				<p class="hint" id="details-current-hint">Only needed if you change your email address.</p>
			</div>
			<button class="btn" type="submit">Save details</button>
		</form>
	</section>

	<section class="panel" aria-labelledby="password-heading">
		<h2 id="password-heading">Change password</h2>

		<?php $errors = $passwordErrors; include __DIR__ . '/errors.html.php'; ?>

		<form action="profile.php" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="form" value="password">
			<div class="field">
				<label for="current">Current password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="current" name="current" required autocomplete="current-password">
			</div>
			<div class="field">
				<label for="password">New password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="password-hint">
				<p class="hint" id="password-hint">At least 8 characters, including a letter and a number.</p>
			</div>
			<div class="field">
				<label for="confirm">Confirm new password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="confirm" name="confirm" required minlength="8" autocomplete="new-password">
			</div>
			<button class="btn" type="submit">Change password</button>
		</form>
	</section>
</div>

<?php // Delete account: danger zone. Source: MDN Web Docs (no date) <details>, <fieldset> and the required attribute. https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/required ?>
<section class="panel danger-zone" aria-labelledby="delete-heading">
	<h2 id="delete-heading">Delete my account</h2>

	<?php if (isAdmin()): ?>
		<p>Admin accounts cannot be deleted here, because the site must always have at least one admin. Ask another admin to remove the account.</p>
	<?php else: ?>
		<p>This <strong>permanently</strong> deletes your account. It cannot be undone. This will be deleted:</p>
		<ul class="delete-list">
			<li><?=html($summary['privatePets'])?> <?=$summary['privatePets'] == 1 ? 'pet' : 'pets'?> you listed yourself (private rehoming), with their photos<?=$summary['requestsReceived'] ? ', and the ' . html($summary['requestsReceived']) . ' adoption ' . ($summary['requestsReceived'] == 1 ? 'request' : 'requests') . ' people sent for them' : ''?></li>
			<li><?=html($summary['requestsSent'])?> adoption <?=$summary['requestsSent'] == 1 ? 'request' : 'requests'?> you sent (a pet reserved for you becomes available again)</li>
			<li><?=html($summary['favourites'])?> <?=$summary['favourites'] == 1 ? 'favourite' : 'favourites'?> and your adopter profile</li>
		</ul>
		<?php if ($summary['shelterPets'] || isShelterStaff()): ?>
			<p class="meta">Pets you listed for a shelter (<?=html($summary['shelterPets'])?>) belong to the shelter and stay on the site<?=isShelterStaff() ? '; you will no longer be listed as staff of ' . html(currentUser()['sheltername']) : ''?>.</p>
		<?php endif; ?>

		<?php $errors = $deleteErrors; include __DIR__ . '/errors.html.php'; ?>

		<form action="profile.php" method="post" class="form" data-confirm="Delete your account for good? This cannot be undone.">
			<?=csrfField()?>
			<input type="hidden" name="form" value="delete">
			<div class="field">
				<label for="delete-current">Your password <span class="required" aria-hidden="true">*</span></label>
				<input type="password" id="delete-current" name="current" required autocomplete="current-password">
			</div>
			<div class="field">
				<label class="checkbox"><input type="checkbox" name="confirmdelete" value="1" required> I understand that my account and the data above will be deleted for good</label>
			</div>
			<button class="btn btn-danger" type="submit">Delete my account</button>
		</form>
	<?php endif; ?>
</section>
