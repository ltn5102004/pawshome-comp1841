<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - W3C WAI (no date) ARIA Authoring Practices Guide - Breadcrumb Pattern. https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/
//   - MDN Web Docs (no date) <dl> description list and <time> element. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/dl
//   - MDN Web Docs (no date) <a>: mailto: and tel: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - OWASP (no date) Cross-Site Request Forgery Prevention Cheat Sheet (hidden token field). https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
//   - MDN Web Docs (no date) Client-side form validation (required, minlength, maxlength, type=email). https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
//   - MDN Web Docs (no date) Window.confirm() - used through data-confirm in js/main.js. https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
	<a href="pets.php">Find a pet</a> <span aria-hidden="true">/</span> <span aria-current="page"><?=html($pet['name'])?></span>
</nav>

<?php // Only the owner and admins can open a pet that is not approved, so tell them why nobody else can see it ?>
<?php if ($pet['approval'] == 'Pending'): ?>
	<div class="alert alert-warning" role="status">
		<strong>Waiting for review.</strong> This listing is not public yet. An admin will check it, and it will appear on the site once it is approved.
	</div>
<?php elseif ($pet['approval'] == 'Rejected'): ?>
	<div class="alert alert-error" role="status">
		<strong>This listing was not approved.</strong>
		<?=$pet['reviewnote'] ? 'Reason: ' . html($pet['reviewnote']) : ''?>
		Edit the listing to fix the problem and it will be sent for review again.
	</div>
<?php endif; ?>

<article class="pet-detail">
	<figure class="pet-figure">
		<img class="pet-photo" src="<?=html(imagePath($pet['image']))?>" alt="Photo of <?=html($pet['name'])?> the <?=html(strtolower($pet['speciesname']))?>" width="600" height="450">
		<?php // Creative Commons licences require the photographer and licence to be shown with the photo.
		// Source: Creative Commons (no date) Recommended practices for attribution. https://wiki.creativecommons.org/wiki/Recommended_practices_for_attribution ?>
		<?php if (!empty($pet['imagecredit'])): ?>
			<figcaption class="photo-credit">
				Photo: <?php if (!empty($pet['imagesource'])): ?><a href="<?=html($pet['imagesource'])?>" rel="noopener" target="_blank"><?=html($pet['imagecredit'])?><span class="visually-hidden"> (opens in a new tab)</span></a><?php else: ?><?=html($pet['imagecredit'])?><?php endif; ?>
			</figcaption>
		<?php endif; ?>
	</figure>

	<div class="pet-info">
		<div class="card-top">
			<h1><?=html($pet['name'])?></h1>
			<span class="badge badge-<?=strtolower(html($pet['status']))?>"><?=html($pet['status'])?></span>
		</div>
		<p class="trust-row"><?php include __DIR__ . '/trustbadge.html.php'; ?></p>

		<dl class="facts">
			<div><dt>Species</dt><dd><?=html($pet['speciesname'])?></dd></div>
			<div><dt>Gender</dt><dd><?=html($pet['gender'])?></dd></div>
			<div><dt>Age</dt><dd><?=html($pet['age'])?> <?=$pet['age'] == 1 ? 'year' : 'years'?></dd></div>
			<?php if ($pet['breed']): ?>
				<div><dt>Breed</dt><dd><?=html($pet['breed'])?></dd></div>
			<?php endif; ?>
			<div><dt>Size</dt><dd><?=html($pet['size'])?></dd></div>
			<div><dt>Energy</dt><dd><?=html($pet['energy'])?></dd></div>
			<div><dt>Listed</dt><dd><time datetime="<?=html($pet['dateadded'])?>"><?=formatDate($pet['dateadded'])?></time></dd></div>
		</dl>

		<div class="favourite-row">
			<?php if (isLoggedIn() && $pet['approval'] == 'Approved'): ?>
				<?php include __DIR__ . '/favouritebutton.html.php'; ?>
			<?php endif; ?>
			<p class="meta">
				&hearts; Saved by <span data-count-for="<?=html($pet['id'])?>"><?=html($pet['favouritecount'])?></span> <span data-count-word-for="<?=html($pet['id'])?>"><?=$pet['favouritecount'] == 1 ? 'person' : 'people'?></span>
				<?php if (!isLoggedIn()): ?>
					&middot; <a href="login.php">Log in</a> to save <?=html($pet['name'])?>
				<?php endif; ?>
			</p>
		</div>

		<h2>About <?=html($pet['name'])?></h2>
		<p class="description"><?=nl2br(html($pet['description']))?></p>

		<?php // Source: WebAIM (no date) Invisible Content Just for Screen Reader Users ("Yes:" / "No:" text). https://webaim.org/techniques/css/invisiblecontent/ ?>
		<h2>Health</h2>
		<ul class="check-list">
			<li class="<?=$pet['vaccinated'] ? 'yes' : 'no'?>"><span class="visually-hidden"><?=$pet['vaccinated'] ? 'Yes: ' : 'No: '?></span>Vaccinated</li>
			<li class="<?=$pet['neutered'] ? 'yes' : 'no'?>"><span class="visually-hidden"><?=$pet['neutered'] ? 'Yes: ' : 'No: '?></span>Neutered / spayed</li>
		</ul>
		<p class="health-notes"><strong>Health conditions:</strong> <?=$pet['healthnotes'] ? html($pet['healthnotes']) : 'None known.'?></p>

		<h2>Would suit a home where&hellip;</h2>
		<ul class="check-list">
			<?php foreach (compatibilityOptions() as $column => $label): ?>
				<li class="<?=$pet[$column] ? 'yes' : 'no'?>"><span class="visually-hidden"><?=$pet[$column] ? 'Yes: ' : 'No: '?></span><?=html($label)?></li>
			<?php endforeach; ?>
		</ul>

		<div class="shelter-box">
			<?php if (isPrivateRehoming($pet)): ?>
				<?php // A private owner's contact details are not published: the adoption request is the way to reach them ?>
				<h2>Private owner</h2>
				<p>Listed by <strong><?=html($pet['postedby'] ?? 'a member')?></strong><?=$pet['sheltercity'] ? ' in ' . html($pet['sheltercity']) : ''?>.
					Send an adoption request and the owner will get in touch.</p>
			<?php else: ?>
				<h2>Shelter</h2>
				<p><strong><?=html($pet['sheltername'])?></strong>, <?=html($pet['sheltercity'])?></p>
				<p><a href="mailto:<?=html($pet['shelteremail'])?>"><?=html($pet['shelteremail'])?></a> &middot; <a href="tel:<?=html(str_replace(' ', '', $pet['shelterphone']))?>"><?=html($pet['shelterphone'])?></a></p>
				<?php if ($pet['postedby']): ?>
					<p class="meta">Listed by <?=html($pet['postedby'])?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<?php if (canManagePet($pet)): ?>
			<div class="owner-actions">
				<a class="btn btn-outline" href="editpet.php?id=<?=html($pet['id'])?>">Edit listing</a>
				<form action="deletepet.php" method="post" class="inline-form" data-confirm="Delete <?=html($pet['name'])?>? This cannot be undone.">
					<?=csrfField()?>
					<input type="hidden" name="id" value="<?=html($pet['id'])?>">
					<button class="btn btn-danger" type="submit">Delete</button>
				</form>
			</div>
		<?php endif; ?>
	</div>
</article>

<section class="panel request-panel" aria-labelledby="request-heading">
	<h2 id="request-heading">Adopt <?=html($pet['name'])?></h2>
	<?php if ($pet['approval'] != 'Approved'): ?>
		<p>Adoption requests open once this listing has been approved.</p>
	<?php elseif (canManagePet($pet)): ?>
		<p>This is a listing you manage, so adoption requests for it are answered by you.
			<a href="<?=isAdmin() ? 'admin/requests.php' : 'requests.php'?>">Open the requests</a>.</p>
	<?php elseif ($pet['status'] == 'Adopted'): ?>
		<p><?=html($pet['name'])?> has already found a home. <a href="pets.php?status=Available">See pets that are still available</a>.</p>
	<?php elseif (!isLoggedIn()): ?>
		<p><a href="login.php">Log in</a> or <a href="register.php">create a free account</a> to send an adoption request.</p>
	<?php elseif ($alreadyRequested): ?>
		<p>You have already sent a request for <?=html($pet['name'])?>. You can follow it on <a href="myaccount.php">your account page</a>.</p>
	<?php else: ?>
		<?php if ($profile): ?>
			<p class="profile-note">Your adopter profile will be sent with this request:
				<?=html($profile['hometype'])?> &middot; <?=html($profile['experience'])?> &middot; budget <?=html($profile['budget'])?>.
				<a href="adopterprofile.php?back=<?=urlencode('pet.php?id=' . $pet['id'])?>">Edit</a></p>
		<?php else: ?>
			<p class="profile-note">Tip: <a href="adopterprofile.php?back=<?=urlencode('pet.php?id=' . $pet['id'])?>">fill in your adopter profile</a> first so the shelter knows about your home.</p>
		<?php endif; ?>
		<form action="requestadoption.php" method="post" class="form">
			<?=csrfField()?>
			<input type="hidden" name="petid" value="<?=html($pet['id'])?>">
			<div class="field">
				<label for="message">Tell the shelter about your home <span class="required" aria-hidden="true">*</span></label>
				<textarea id="message" name="message" rows="4" required minlength="10" maxlength="1000" aria-describedby="message-hint"></textarea>
				<p class="hint" id="message-hint">At least 10 characters. For example: garden, other pets, working hours.</p>
			</div>
			<button class="btn btn-cta" type="submit">Send adoption request</button>
		</form>
	<?php endif; ?>
</section>
