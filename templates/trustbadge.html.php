<?php
// Source:
//   - Badge styling follows the existing .badge classes in css/style.css
//   - MDN Web Docs (no date) title attribute. https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/title
// Shows who is offering the pet. Needs $pet (from petSelect()).
?>
<?php if (isPrivateRehoming($pet)): ?>
	<span class="badge badge-private" title="Listed by a private owner, not a shelter">Private rehoming</span>
<?php elseif ($pet['shelterverified']): ?>
	<span class="badge badge-verified" title="This shelter has been checked by the PawsHome team"><span aria-hidden="true">&#10003;</span> Verified shelter</span>
<?php else: ?>
	<span class="badge badge-unverified" title="The PawsHome team has not checked this shelter yet">Shelter not yet verified</span>
<?php endif; ?>
