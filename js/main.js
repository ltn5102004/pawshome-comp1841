// PawsHome - small JavaScript enhancements. The site still works without JavaScript.

document.addEventListener('DOMContentLoaded', function () {

	// Small message bubble used by the heart button and the request stage buttons: shows the text under
	// the button and hides it again after 5 seconds. A second message restarts the 5 seconds.
	// Source: MDN Web Docs (no date) window.setTimeout() and clearTimeout().
	//         https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout
	function showBubble(note, message, isError) {
		note.textContent = message;
		note.classList.toggle('error', isError);
		note.classList.add('show');
		clearTimeout(note.hideTimer);
		note.hideTimer = setTimeout(function () {
			note.classList.remove('show');
		}, 5000);
	}

	// Source: MDN Web Docs (no date) Element.classList and aria-expanded.
	// 1. Mobile menu button
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('main-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	// Source: MDN Web Docs (no date) Window.confirm() and Event.preventDefault().
	// 2. Ask before deleting anything (forms with a data-confirm attribute)
	// (one listener on the document, so forms that are replaced later by js code still ask)
	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (form.hasAttribute && form.hasAttribute('data-confirm') && !confirm(form.getAttribute('data-confirm'))) {
			event.preventDefault();
		}
	});

	// Source: MDN Web Docs (no date) Client-side form validation (checkValidity, reportValidity).
	// 3. Front-end validation: highlight invalid fields and focus the first one
	document.querySelectorAll('form.form').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			if (!form.checkValidity()) {
				event.preventDefault();
				form.classList.add('was-validated');
				var firstInvalid = form.querySelector(':invalid');
				if (firstInvalid) {
					firstInvalid.focus();
				}
				form.reportValidity();
			}
		});
	});

	// Source: MDN Web Docs (no date) HTMLTextAreaElement, input event and aria-live.
	// 4. Character counter under long text boxes
	document.querySelectorAll('textarea[data-counter]').forEach(function (box) {
		var counter = document.createElement('p');
		counter.className = 'char-count';
		counter.setAttribute('aria-live', 'polite');
		box.insertAdjacentElement('afterend', counter);
		function update() {
			counter.textContent = box.value.length + ' / ' + box.maxLength + ' characters';
		}
		box.addEventListener('input', update);
		update();
	});

	// Source: MDN Web Docs (no date) URL.createObjectURL() and setCustomValidity().
	// 5. Preview the chosen photo and check its size before uploading
	var fileInput = document.getElementById('image');
	var preview = document.getElementById('image-preview');
	if (fileInput && preview) {
		fileInput.addEventListener('change', function () {
			var file = fileInput.files[0];
			fileInput.setCustomValidity('');
			if (!file) {
				preview.hidden = true;
				return;
			}
			if (file.size > 2000000) {
				fileInput.setCustomValidity('The image must be smaller than 2MB.');
				fileInput.reportValidity();
				preview.hidden = true;
				return;
			}
			preview.src = URL.createObjectURL(file);
			preview.hidden = false;
		});
	}

	// Source: adapted from W3Schools (no date) How To - Scroll Back To Top Button;
	//         MDN Web Docs (no date) Window.scrollTo(), Window.matchMedia() and prefers-reduced-motion.
	// 6. Round "back to top" button: show it as soon as the page is scrolled down a little
	var toTop = document.getElementById('to-top');
	if (toTop) {
		function checkScroll() {
			toTop.classList.toggle('show', window.scrollY > 80);
		}
		window.addEventListener('scroll', checkScroll, { passive: true });
		checkScroll();

		toTop.addEventListener('click', function (event) {
			event.preventDefault();
			// Smooth scroll, unless the user has asked the system for less motion
			var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
			// Move keyboard focus back to the top of the content (accessibility)
			document.getElementById('main').focus({ preventScroll: true });
		});
	}

	// Source: MDN Web Docs (no date) Using the Fetch API; FormData; Element.closest(); window.setTimeout().
	//         https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
	//         https://developer.mozilla.org/en-US/docs/Web/API/FormData
	// 7. Heart button: save / remove a favourite WITHOUT reloading the page. A short message appears under
	//    the button and fades away after 5 seconds. Without JavaScript the form still works as a normal post.
	document.querySelectorAll('.favourite-form').forEach(function (form) {
		var wrap = form.closest('.favourite-wrap');
		var note = wrap ? wrap.querySelector('.favourite-note') : null;
		var button = form.querySelector('.btn-favourite');
		var petId = form.querySelector('input[name="petid"]').value;

		// Very old browsers: leave the form alone and let it submit normally
		if (!window.fetch || !window.FormData || !note || !button) {
			return;
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			if (button.disabled) {
				return;                          // ignore a double click while the request is running
			}
			button.disabled = true;

			fetch(form.action, {
				method: 'POST',
				body: new FormData(form),        // includes the CSRF token
				credentials: 'same-origin',
				headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' }
			})
				.then(function (response) { return response.json(); })
				.then(function (data) {
					if (data.ok) {
						// Turn the heart on / off
						button.classList.toggle('saved', data.saved);
						button.setAttribute('aria-pressed', data.saved ? 'true' : 'false');
						button.title = data.saved ? 'Remove from favourites' : 'Save to favourites';
						button.querySelector('.visually-hidden').textContent =
							(data.saved ? 'Remove ' : 'Save ') + data.petname + (data.saved ? ' from' : ' to') + ' favourites';
						// New number of hearts everywhere this pet is shown on the page
						document.querySelectorAll('[data-count-for="' + petId + '"]').forEach(function (el) {
							el.textContent = data.count;
						});
						document.querySelectorAll('[data-count-word-for="' + petId + '"]').forEach(function (el) {
							el.textContent = data.count == 1 ? 'person' : 'people';
						});
					}
					showBubble(note, data.message, !data.ok);
				})
				.catch(function () {
					showBubble(note, 'Sorry, something went wrong. Please try again.', true);
				})
				.then(function () {
					button.disabled = false;
				});
		});
	});

	// Source: MDN Web Docs (no date) SubmitEvent.submitter; Event delegation; Element.closest().
	//         https://developer.mozilla.org/en-US/docs/Web/API/SubmitEvent/submitter
	// 8. Request stage buttons (requests.php and admin/requests.php): change the stage WITHOUT reloading the page.
	//    The server answers with the new HTML for the parts that changed, and the message appears under the
	//    buttons for 5 seconds. One listener on the document, because these forms are replaced after each press.
	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (!form.classList || !form.classList.contains('js-ajax-form') || !window.fetch || !window.FormData) {
			return;                              // not our form, or an old browser: normal submit
		}
		var scope = form.closest('[data-request]');
		var note = scope ? scope.querySelector('.action-note') : null;
		var button = event.submitter;            // the button that was pressed (carries name="action" value="...")
		if (!note || !button || !button.name) {
			return;
		}
		event.preventDefault();

		var data = new FormData(form);
		data.append(button.name, button.value);
		var buttons = form.querySelectorAll('button');
		buttons.forEach(function (b) { b.disabled = true; });   // no double clicks while waiting

		// NOTE: form.getAttribute('action') and not form.action. These forms have a button called "action", and
		// a form control with that name replaces the form.action property (it would give the button, not the address).
		// Source: MDN Web Docs (no date) HTMLFormElement - named controls hide form properties. https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement
		fetch(form.getAttribute('action'), {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
			headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' }
		})
			.then(function (response) { return response.json(); })
			.then(function (result) {
				if (result.ok && result.fragments) {
					// Swap in the new HTML (new status, new tracker, new buttons)
					Object.keys(result.fragments).forEach(function (selector) {
						var target = scope.querySelector(selector);
						if (target) {
							target.innerHTML = result.fragments[selector];
						}
					});
				}
				showBubble(note, result.message, !result.ok);
				// tell other parts of the page (the stage filter) that a request changed
				// Source: MDN Web Docs (no date) CustomEvent. https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
				document.dispatchEvent(new CustomEvent('requestmoved'));
			})
			.catch(function () {
				showBubble(note, 'Sorry, something went wrong. Please try again.', true);
			})
			.then(function () {
				// the old buttons are gone when the card was replaced; this only matters after an error
				buttons.forEach(function (b) { b.disabled = false; });
			});
	});

	// Source: MDN Web Docs (no date) HTMLElement.hidden; String.prototype.includes(); <input type="search">.
	//         https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/hidden
	// 9. Admin tables: "search this list" box. Hides the rows that do not contain what was typed.
	//    The box is hidden in the HTML and only shown here, so without JavaScript nobody sees a box that does nothing.
	document.querySelectorAll('.table-filter[data-filter-for]').forEach(function (box) {
		var table = document.getElementById(box.getAttribute('data-filter-for'));
		var input = box.querySelector('input');
		var counter = box.querySelector('.filter-count');
		if (!table || !input) {
			return;
		}
		var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
		box.hidden = false;
		input.addEventListener('input', function () {
			var words = input.value.trim().toLowerCase();
			var shown = 0;
			rows.forEach(function (row) {
				var match = row.textContent.toLowerCase().indexOf(words) !== -1;
				row.hidden = !match;
				if (match) {
					shown++;
				}
			});
			counter.textContent = words === '' ? '' : 'Showing ' + shown + ' of ' + rows.length;
		});
	});

	// Source: MDN Web Docs (no date) aria-pressed; HTMLElement.hidden; Element.closest().
	//         https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-pressed
	// 10. requests.php: buttons that show only the requests in one stage, with a count on each button.
	//     The stage is read from data-stage inside each card, so it is still right after a card was swapped by fetch.
	var stageFilter = document.querySelector('.stage-filter');
	if (stageFilter) {
		var stageCards = Array.prototype.slice.call(document.querySelectorAll('.request-card[data-request]'));
		var stageEmpty = document.querySelector('.stage-filter-empty');
		var chosenStage = '';
		var stageOf = function (card) {
			var el = card.querySelector('[data-stage]');
			return el ? el.getAttribute('data-stage') : '';
		};
		var applyStage = function () {
			var shown = 0;
			stageCards.forEach(function (card) {
				var stage = stageOf(card);
				card.className = card.className.replace(/\s*stage-\S+/g, '') + ' stage-' + stage.toLowerCase();
				card.hidden = chosenStage !== '' && stage !== chosenStage;
				if (!card.hidden) {
					shown++;
				}
			});
			stageFilter.querySelectorAll('[data-stage-filter]').forEach(function (button) {
				var value = button.getAttribute('data-stage-filter');
				button.setAttribute('aria-pressed', value === chosenStage ? 'true' : 'false');
				var count = value === '' ? stageCards.length : stageCards.filter(function (c) { return stageOf(c) === value; }).length;
				button.querySelector('.chip-count').textContent = count;
			});
			if (stageEmpty) {
				stageEmpty.hidden = shown > 0;
			}
		};
		stageFilter.hidden = false;
		stageFilter.addEventListener('click', function (event) {
			var button = event.target.closest('[data-stage-filter]');
			if (button) {
				chosenStage = button.getAttribute('data-stage-filter');
				applyStage();
			}
		});
		document.addEventListener('requestmoved', applyStage);
		applyStage();
	}

	// Source: MDN Web Docs (no date) HTMLElement.focus() and tabindex.
	// 11. Move keyboard focus to the error box so screen readers announce it
	var errors = document.getElementById('form-errors');
	if (errors) {
		errors.focus();
	}
});
