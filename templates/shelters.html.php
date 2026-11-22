<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) <a>: mailto: and tel: links. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<h1>Our partner shelters</h1>
<p class="lead">Every pet on PawsHome is cared for by one of these rescue centres until it finds a new home.
	Shelters marked <span class="badge badge-verified"><span aria-hidden="true">&#10003;</span> Verified</span> have been checked by the PawsHome team.</p>

<div class="grid">
	<?php foreach ($shelters as $shelter): ?>
		<article class="card shelter-card">
			<div class="card-body">
				<h2><?=html($shelter['name'])?></h2>
				<?php if ($shelter['verified']): ?>
					<span class="badge badge-verified"><span aria-hidden="true">&#10003;</span> Verified shelter</span>
				<?php else: ?>
					<span class="badge badge-unverified">Not yet verified</span>
				<?php endif; ?>
				<p class="meta meta-place"><?=html($shelter['city'])?></p>
				<p><a href="mailto:<?=html($shelter['email'])?>"><?=html($shelter['email'])?></a><br>
				<a href="tel:<?=html(str_replace(' ', '', $shelter['phone']))?>"><?=html($shelter['phone'])?></a></p>
				<p><a class="btn btn-outline btn-small" href="pets.php?shelterid=<?=html($shelter['id'])?>">View <?=html($shelter['petcount'])?> <?=$shelter['petcount'] == 1 ? 'pet' : 'pets'?></a></p>
			</div>
		</article>
	<?php endforeach; ?>
</div>
