<?php
// Source:
//   - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
//   - MDN Web Docs (no date) <section> and <ol> elements. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/section
//   - PHP Group (no date) htmlspecialchars, PHP Manual (via html() in includes/Functions.php). https://www.php.net/manual/en/function.htmlspecialchars.php
?>
<section class="hero">
	<div class="hero-text">
		<?php // "highlight" span: a lime marker behind the key words (styled in css/style.css, BOLD ACCENTS). Source: MDN Web Docs (no date) <span>. https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/span ?>
		<h1>Every pet deserves a <span class="highlight">loving home</span></h1>
		<p>PawsHome brings together rescue shelters and caring families. Browse animals waiting for adoption, or rehome a pet responsibly.</p>
		<div class="hero-actions">
			<a class="btn btn-large btn-cta" href="pets.php">Find a pet</a>
			<a class="btn btn-large btn-outline" href="addpet.php">Rehome a pet</a>
		</div>
	</div>
	<img class="hero-image" src="images/hero.svg" alt="Illustration of a dog and a cat sitting together" width="420" height="320">
</section>

<section class="stats" aria-label="Site statistics">
	<div class="stat"><span class="stat-number"><?=html($totalPets)?></span><span class="stat-label">pets listed</span></div>
	<div class="stat"><span class="stat-number"><?=html($available)?></span><span class="stat-label">waiting for a home</span></div>
	<div class="stat"><span class="stat-number"><?=html($adopted)?></span><span class="stat-label">happily adopted</span></div>
	<div class="stat"><span class="stat-number"><?=html($totalShelters)?></span><span class="stat-label">partner shelters</span></div>
</section>

<section>
	<div class="section-head">
		<h2>Newest arrivals</h2>
		<a href="pets.php">See all pets &rarr;</a>
	</div>
	<?php if (empty($latest)): ?>
		<p class="empty">No pets are available right now. Please check back soon.</p>
	<?php else: ?>
		<div class="grid">
			<?php foreach ($latest as $pet): ?>
				<?php include __DIR__ . '/petcard.html.php'; ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<section class="steps">
	<h2>How adoption works</h2>
	<ol class="step-list">
		<li><h3>Browse</h3><p>Search by species, shelter or status to find a pet that suits your home.</p></li>
		<li><h3>Request</h3><p>Create a free account and send an adoption request with a short message.</p></li>
		<li><h3>Meet</h3><p>The shelter reviews your request and contacts you to arrange a visit.</p></li>
	</ol>
</section>
