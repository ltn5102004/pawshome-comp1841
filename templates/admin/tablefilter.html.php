<?php
// Source:
//   - MDN Web Docs (no date) <input type="search">. https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/search
//   - MDN Web Docs (no date) HTMLElement.hidden (rows are hidden by js/main.js). https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/hidden
// "Search this list" box above an admin table. Needs $filterTarget (the id of the table) and $filterLabel.
// The filtering happens in the browser (js/main.js); without JavaScript the box is simply not shown.
?>
<div class="table-filter" hidden data-filter-for="<?=html($filterTarget)?>">
	<label class="visually-hidden" for="filter-<?=html($filterTarget)?>"><?=html($filterLabel)?></label>
	<input type="search" id="filter-<?=html($filterTarget)?>" placeholder="<?=html($filterLabel)?>" autocomplete="off">
	<span class="filter-count meta" aria-live="polite"></span>
</div>
