# PawsHome – Pet Adoption Board (COMP1841)

A PHP PDO / MySQL CRUD web application built on top of the lab "Jokes" system
(controller + template structure, `includes/DatabaseConnection.php`,
`includes/DatabaseFunctions.php`, admin folder).

## Setup (XAMPP)

1. Copy the `coursework` folder into `/Applications/XAMPP/xamppfiles/htdocs/comp1841/`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin → **Import** → choose `database/petadopt.sql` → Go.
   (It creates the `petadopt` database with sample data.)
4. Make `images/pets` writable so uploads work:
   `chmod 777 images/pets` (Mac: Get Info → Sharing & Permissions → everyone Read & Write).
5. Visit `http://localhost/comp1841/coursework/index.php`

If your MySQL user/password are not `root` / empty, edit `includes/DatabaseConnection.php`.
Contact emails go to `ADMIN_EMAIL` in `includes/Functions.php` (a placeholder, `admin@pawshome.test`; set the real address when deploying).

## Test accounts

| Role   | Email                 | Password   |
|--------|-----------------------|------------|
| Admin  | admin@pawshome.test   | Admin123!  |
| Member | anna@pawshome.test    | Member123! |
| Member | tom@pawshome.test     | Member123! |
| Shelter staff (verified shelter, Greenwich) | staff.greenwich@pawshome.test | Staff123! |
| Shelter staff (shelter NOT verified, York)  | staff.york@pawshome.test      | Staff123! |

## File structure

```
index.php, pets.php, pet.php, shelters.php      public pages (list, detail, JOIN)
addpet.php, editpet.php, deletepet.php          CRUD for pets (members: own pets, admin: all)
requestadoption.php, myaccount.php              adoption requests (extra feature)
requests.php                                    requests for my pets (private owners and shelter staff answer them)
favourite.php                                   save / unsave a pet (favourite table, many-to-many)
profile.php                                     edit name / email, change password, delete my own account
adopterprofile.php                              adopter profile (home, experience, budget) sent with requests
login.php, register.php, logout.php             accounts (password_hash, sessions)
contact.php                                     contact form → mail() + saved to DB
admin/                                          admin area (dashboard, review queue, pets, requests,
                                                shelters, species, users, messages)
includes/DatabaseConnection.php                 PDO connection
includes/DatabaseFunctions.php                  query() + generic functions + named functions
includes/Functions.php                          helpers: escaping, templates, login, validation, upload
templates/                                      HTML templates (*.html.php), incl. reusable
                                                pagination.html.php, favouritebutton.html.php,
                                                trustbadge.html.php and requesttracker.html.php
css/style.css, js/main.js                       responsive design, front-end validation
database/petadopt.sql                           database export
docs/                                           ERD (erd.dbml for dbdiagram.io, erd.mmd for Mermaid), navigation diagram, colour system (colour-system.md)
```

## References

Every piece of code that is adapted from a source has a `Source:` comment above it in the code.
Harvard style, alphabetical by author. Accessed dates should be the date the page was read (here 3 November 2026).


- Apache Software Foundation (no date) *Apache HTTP Server 2.4: mod_authz_core (Require)*. Available at: https://httpd.apache.org/docs/2.4/mod/mod_authz_core.html (Accessed: 6 November 2026).
- Butler, T. and Yank, K. (2017) *PHP & MySQL: Novice to Ninja*. 6th edn. Collingwood: SitePoint. (generic query / insert / update / delete / findById / findAll functions, output buffering templates, many-to-many join table)
- Coyier, C. (2011) *Responsive Data Tables*. CSS-Tricks. Available at: https://css-tricks.com/responsive-data-tables/ (Accessed: 9 November 2026).
- Creative Commons (no date) *Recommended practices for attribution*. Available at: https://wiki.creativecommons.org/wiki/Recommended_practices_for_attribution (Accessed: 3 November 2026).
- dbdiagram.io (no date) *DBML - Database Markup Language*. Available at: https://dbml.dbdiagram.io/docs/ (Accessed: 22 November 2026). (docs/erd.dbml)
- Google (no date) *Material Design 3: Color roles*. Available at: https://m3.material.io/styles/color/roles (Accessed: 9 November 2026).
- Google (no date) *Material Design 3: Design tokens*. Available at: https://m3.material.io/foundations/design-tokens/overview (Accessed: 9 November 2026).
- Google (no date) *Material Symbols and Icons* (Apache License 2.0). Available at: https://fonts.google.com/icons (Accessed: 3 November 2026). ("place" and "arrow_upward" icon paths)
- Gordon, K. (2021) *Using Color to Enhance Your Design*. Nielsen Norman Group. Available at: https://www.nngroup.com/articles/color-enhance-design/ (Accessed: 9 November 2026).
- MDN Web Docs (no date) *::before (:before)*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/::before (Accessed: 3 November 2026).
- MDN Web Docs (no date) *:has()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/:has (Accessed: 9 November 2026).
- MDN Web Docs (no date) *:is()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/:is (Accessed: 9 November 2026).
- MDN Web Docs (no date) *<details>: The Details disclosure element*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/details (Accessed: 3 November 2026).
- MDN Web Docs (no date) *<figcaption>: The Figure Caption element*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/figcaption (Accessed: 3 November 2026).
- MDN Web Docs (no date) *<input type="checkbox">*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/checkbox (Accessed: 3 November 2026).
- MDN Web Docs (no date) *<input type="search">*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/search (Accessed: 9 November 2026).
- MDN Web Docs (no date) *Accept header*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept (Accessed: 6 November 2026).
- MDN Web Docs (no date) *aria-pressed*. Available at: https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-pressed (Accessed: 3 November 2026).
- MDN Web Docs (no date) *ARIA: status role*. Available at: https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Roles/status_role (Accessed: 6 November 2026).
- MDN Web Docs (no date) *403 Forbidden*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/403 (Accessed: 22 November 2026). (templates/forbidden.html.php)
- MDN Web Docs (no date) *attr()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/attr (Accessed: 9 November 2026).
- MDN Web Docs (no date) *backdrop-filter*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter (Accessed: 9 November 2026).
- MDN Web Docs (no date) *border-image*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/border-image (Accessed: 9 November 2026).
- MDN Web Docs (no date) *box-decoration-break*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/box-decoration-break (Accessed: 9 November 2026).
- MDN Web Docs (no date) *Client-side form validation*. Available at: https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation (Accessed: 3 November 2026).
- MDN Web Docs (no date) *content* (alternative text for generated content). Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/content (Accessed: 9 November 2026).
- MDN Web Docs (no date) *drop-shadow()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/filter-function/drop-shadow (Accessed: 9 November 2026).
- MDN Web Docs (no date) *CSS grid layout*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout (Accessed: 3 November 2026).
- MDN Web Docs (no date) *CustomEvent() constructor*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent (Accessed: 9 November 2026).
- MDN Web Docs (no date) *Document and website structure*. Available at: https://developer.mozilla.org/en-US/docs/Learn/HTML/Introduction_to_HTML/Document_and_website_structure (Accessed: 3 November 2026).
- MDN Web Docs (no date) *FormData*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/FormData (Accessed: 6 November 2026).
- MDN Web Docs (no date) *hsl() CSS function*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/hsl (Accessed: 9 November 2026).
- MDN Web Docs (no date) *HTML attribute: autocomplete*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete (Accessed: 3 November 2026).
- MDN Web Docs (no date) *HTML attribute: required*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/required (Accessed: 9 November 2026).
- MDN Web Docs (no date) *HTML table accessibility*. Available at: https://developer.mozilla.org/en-US/docs/Learn/HTML/Tables/Advanced (Accessed: 3 November 2026).
- MDN Web Docs (no date) *HTMLElement: hidden property*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/hidden (Accessed: 9 November 2026).
- MDN Web Docs (no date) *HTMLFormElement*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement (Accessed: 6 November 2026).
- MDN Web Docs (no date) *linear-gradient()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/gradient/linear-gradient (Accessed: 9 November 2026).
- MDN Web Docs (no date) *::selection*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/::selection (Accessed: 9 November 2026).
- MDN Web Docs (no date) *<span>: The Content Span element*. Available at: https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/span (Accessed: 9 November 2026).
- MDN Web Docs (no date) *position*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/position (Accessed: 6 November 2026).
- MDN Web Docs (no date) *prefers-reduced-motion*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-reduced-motion (Accessed: 3 November 2026).
- MDN Web Docs (no date) *radial-gradient()*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/gradient/radial-gradient (Accessed: 9 November 2026).
- MDN Web Docs (no date) *SubmitEvent: submitter property*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/SubmitEvent/submitter (Accessed: 6 November 2026).
- MDN Web Docs (no date) *SVG: Paths*. Available at: https://developer.mozilla.org/en-US/docs/Web/SVG/Tutorial/Paths (Accessed: 9 November 2026).
- MDN Web Docs (no date) *Using CSS custom properties (variables)*. Available at: https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties (Accessed: 3 November 2026).
- MDN Web Docs (no date) *Using the Fetch API*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch (Accessed: 6 November 2026).
- MDN Web Docs (no date) *Window: scrollTo() method*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/Window/scrollTo (Accessed: 3 November 2026).
- MDN Web Docs (no date) *Window: setTimeout() method*. Available at: https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout (Accessed: 6 November 2026).
- MDN Web Docs (no date) *Your first form*. Available at: https://developer.mozilla.org/en-US/docs/Learn/Forms/Your_first_form (Accessed: 3 November 2026).
- Mermaid (no date) *Entity Relationship Diagrams*. Available at: https://mermaid.js.org/syntax/entityRelationshipDiagram.html (Accessed: 22 November 2026). (docs/erd.mmd)
- MySQL (no date) *MySQL 8.0 Reference Manual: Aggregate Function Descriptions* (COUNT, SUM). Available at: https://dev.mysql.com/doc/refman/8.0/en/aggregate-functions.html (Accessed: 9 November 2026).
- MySQL (no date) *MySQL 8.0 Reference Manual: Comparison Functions and Operators* (BETWEEN). Available at: https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html (Accessed: 3 November 2026).
- MySQL (no date) *MySQL 8.0 Reference Manual: CREATE TABLE Statement*. Available at: https://dev.mysql.com/doc/refman/8.0/en/create-table.html (Accessed: 3 November 2026).
- MySQL (no date) *MySQL 8.0 Reference Manual: FOREIGN KEY Constraints*. Available at: https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html (Accessed: 3 November 2026).
- MySQL (no date) *MySQL 8.0 Reference Manual: JOIN Clause*. Available at: https://dev.mysql.com/doc/refman/8.0/en/join.html (Accessed: 3 November 2026).
- MySQL (no date) *MySQL 8.0 Reference Manual: SELECT Statement* (JOIN, GROUP BY, LIMIT / OFFSET). Available at: https://dev.mysql.com/doc/refman/8.0/en/select.html (Accessed: 3 November 2026).
- OWASP (no date) *Authentication Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html (Accessed: 6 November 2026).
- OWASP (no date) *Authorization Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html (Accessed: 6 November 2026).
- OWASP (no date) *Cross-Site Request Forgery Prevention Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html (Accessed: 3 November 2026).
- OWASP (no date) *Input Validation Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html (Accessed: 3 November 2026).
- OWASP (no date) *Session Management Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html (Accessed: 6 November 2026).
- OWASP (no date) *Unvalidated Redirects and Forwards Cheat Sheet*. Available at: https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html (Accessed: 3 November 2026).
- Pantone (2025) *PANTONE 11-4201 Cloud Dancer: Color of the Year 2026*. Available at: https://www.pantone.com/color-of-the-year/2026 (Accessed: 9 November 2026).
- PHP Group (no date) *header*, PHP Manual (the Location header sends a 302 status). Available at: https://www.php.net/manual/en/function.header.php (Accessed: 22 November 2026). (requireCsrfToken fix, DEF-01)
- PHP Group (no date) *mail - Send mail* (Example #2 Sending mail with extra headers). Available at: https://www.php.net/manual/en/function.mail.php (Accessed: 3 November 2026).
- PHP Group (no date) *mb_substr* and *mb_strtoupper*. Available at: https://www.php.net/manual/en/function.mb-substr.php (Accessed: 9 November 2026).
- PHP Group (no date) *ob_start - Turn on output buffering*. Available at: https://www.php.net/manual/en/function.ob-start.php (Accessed: 9 November 2026).
- PHP Group (no date) *PDO::beginTransaction*. Available at: https://www.php.net/manual/en/pdo.begintransaction.php (Accessed: 9 November 2026).
- PHP Group (no date) *PHP Manual* (PDO, password_hash, password_verify, filter_var, htmlspecialchars, filemtime, http_build_query, sessions). Available at: https://www.php.net/manual/en/ (Accessed: 3 November 2026).
- PHP Group (no date) *session_destroy - Destroys all data registered to a session* (Example #1, used for logout.php). Available at: https://www.php.net/manual/en/function.session-destroy.php (Accessed: 3 November 2026).
- PHP Group (no date) *session_set_cookie_params*. Available at: https://www.php.net/manual/en/function.session-set-cookie-params.php (Accessed: 6 November 2026).
- Prichard, M. (2026) *COMP1841 Web Programming 1 lab materials* (Jokes CRUD system, DatabaseFunctions.php). University of Greenwich. Unpublished.
- Raikwar, H. (2023) *Master UI design: Enhance aesthetics with the 60-30-10 rule*. LogRocket. Available at: https://blog.logrocket.com/ux-design/60-30-10-rule (Accessed: 9 November 2026).
- RSPCA (no date) *Find a pet*. Available at: https://www.rspca.org.uk/findapet (Accessed: 3 November 2026).
- Unsplash (no date) *Free pet pictures*. Available at: https://unsplash.com (Accessed: 3 November 2026). (pet photos in images/pets)
- W3C (2023) *Accessible Rich Internet Applications (WAI-ARIA) 1.2* (aria-current="step"). Available at: https://www.w3.org/TR/wai-aria-1.2/ (Accessed: 3 November 2026).
- W3C (2023) *Understanding WCAG 2.2: SC 1.4.1 Use of Color; SC 1.4.3 Contrast (Minimum); SC 1.4.11 Non-text Contrast*. Available at: https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html (Accessed: 9 November 2026).
- W3C WAI (no date) *ARIA Authoring Practices Guide: Alert Pattern*. Available at: https://www.w3.org/WAI/ARIA/apg/patterns/alert/ (Accessed: 3 November 2026).
- W3C WAI (no date) *ARIA Authoring Practices Guide: Breadcrumb Pattern*. Available at: https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/ (Accessed: 3 November 2026).
- W3C WAI (no date) *ARIA Authoring Practices Guide: Disclosure (Show/Hide) Pattern*. Available at: https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/ (Accessed: 3 November 2026).
- W3Schools (no date) *How To - CSS Pagination*. Available at: https://www.w3schools.com/howto/howto_css_pagination.asp (Accessed: 3 November 2026).
- W3Schools (no date) *How To - Scroll Back To Top Button*. Available at: https://www.w3schools.com/howto/howto_js_scroll_to_top.asp (Accessed: 3 November 2026).
- W3Schools (no date) *PHP File Upload*. Available at: https://www.w3schools.com/php/php_file_upload.asp (Accessed: 3 November 2026).
- WebAIM (no date) *Contrast and Color Accessibility*. Available at: https://webaim.org/articles/contrast/ (Accessed: 9 November 2026).
- WebAIM (no date) *Contrast Checker*. Available at: https://webaim.org/resources/contrastchecker/ (Accessed: 3 November 2026).
- WebAIM (no date) *Invisible Content Just for Screen Reader Users*. Available at: https://webaim.org/techniques/css/invisiblecontent/ (Accessed: 3 November 2026).
- WebAIM (no date) *Skip Navigation Links*. Available at: https://webaim.org/techniques/skipnav/ (Accessed: 3 November 2026).
- Wikipedia contributors (no date) *Finite-state machine*. Available at: https://en.wikipedia.org/wiki/Finite-state_machine (Accessed: 6 November 2026).
- Wikipedia contributors (no date) *Moderation system*. Available at: https://en.wikipedia.org/wiki/Moderation_system (Accessed: 5 November 2026). (idea of pre-moderation for the review queue)

Note: the `.svg` pet pictures (older sample data) are Apple/Unicode emoji drawn inside SVG files; the `.jpg` files are the photos to use.

### Image credits (pet photos)

The photographer, licence and link are also stored in the `pet` table (`imagecredit`, `imagesource`) and shown under each photo on the pet page, as the Creative Commons licences require.

- ales brad (no date) *Maine Coon Cat List Characteristics* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Maine_Coon_Cat_List_Characteristics.jpg (Accessed: 3 November 2026). (images/pets/tiger.jpg)
- Anthony O'Neil (no date) *Pet ferret - geograph.org.uk - 8007550* [Photograph]. Wikimedia Commons, CC BY-SA 2.0. Available at: https://commons.wikimedia.org/wiki/File:Pet_ferret_-_geograph.org.uk_-_8007550.jpg (Accessed: 3 November 2026). (images/pets/bandit.jpg)
- Augustus Binu (no date) *Baby Hamster - 2 Weeks Old* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Baby_Hamster_-_2_Weeks_Old.jpg (Accessed: 3 November 2026). (images/pets/nibbles.jpg)
- Augustus Binu (no date) *Pug - 1 year Old* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Pug_-_1_year_Old.jpg (Accessed: 3 November 2026). (images/pets/winston.jpg)
- Basotxerri (no date) *Osnabrück - Zoo - Russköpfchen 01* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Osnabr%C3%BCck_-_Zoo_-_Russk%C3%B6pfchen_01.jpg (Accessed: 3 November 2026). (images/pets/mango.jpg)
- BlackIceNRW (no date) *Ragdoll Kater, drei Jahre alt, RAG n 21 seal-tabby-colourpoint, Januar 2015* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Ragdoll_Kater,_drei_Jahre_alt,_RAG_n_21_seal-tabby-colourpoint,_Januar_2015.JPG (Accessed: 3 November 2026). (images/pets/willow.jpg)
- Brummer, B. (Trougnouf) (no date) *Beagle in Viroinval (DSC04556)* [Photograph]. Wikimedia Commons, CC BY 4.0. Available at: https://commons.wikimedia.org/wiki/File:Beagle_in_Viroinval_(DSC04556).jpg (Accessed: 3 November 2026). (images/pets/daisy.jpg)
- Charles J. Sharp (no date) *Spur-thighed tortoise (Testudo graeca ibera) Babadag* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Spur-thighed_tortoise_(Testudo_graeca_ibera)_Babadag.jpg (Accessed: 3 November 2026). (images/pets/sheldon.jpg)
- Diego Delso (no date) *Cobaya (Cavia porcellus), Tierpark Hellabrunn, Múnich, Alemania, 2012-06-17, DD 01* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Cobaya_(Cavia_porcellus),_Tierpark_Hellabrunn,_M%C3%BAnich,_Alemania,_2012-06-17,_DD_01.JPG (Accessed: 3 November 2026). (images/pets/pumpkin.jpg)
- Diego Delso (no date) *Conejo común (Oryctolagus cuniculus), Tierpark Hellabrunn, Múnich, Alemania, 2012-06-17, DD 02* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Conejo_com%C3%BAn_(Oryctolagus_cuniculus),_Tierpark_Hellabrunn,_M%C3%BAnich,_Alemania,_2012-06-17,_DD_02.JPG (Accessed: 3 November 2026). (images/pets/hazel.jpg)
- Dktue (no date) *Portrait of a labrador retriever* [Photograph]. Wikimedia Commons, CC0. Available at: https://commons.wikimedia.org/wiki/File:Portrait_of_a_labrador_retriever.jpg (Accessed: 3 November 2026). (images/pets/rocky.jpg)
- Felinlove (no date) *Siamese cat, female* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Siamese_cat,_female.jpg (Accessed: 3 November 2026). (images/pets/misty.jpg)
- Filip B (no date) *Dog In The Grass* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Dog_In_The_Grass.jpg (Accessed: 3 November 2026). (images/pets/rosie.jpg)
- Friedrich Haag (no date) *Heimtier 004 2023 08 26* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Heimtier_004_2023_08_26.jpg (Accessed: 3 November 2026). (images/pets/clover.jpg)
- Gzen92 (no date) *Cochon d'Inde (Cavia porcellus) (2)* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Cochon_d%27Inde_(Cavia_porcellus)_(2).jpg (Accessed: 3 November 2026). (images/pets/pip.jpg)
- Heba Aisha (no date) *My Pet Cockatiel* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:My_Pet_Cockatiel.jpg (Accessed: 3 November 2026). (images/pets/sunny.jpg)
- Jakub Hałun (no date) *20110425 German Shepherd Dog 8505* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:20110425_German_Shepherd_Dog_8505.jpg (Accessed: 3 November 2026). (images/pets/bruno.jpg)
- Jakub Hałun (no date) *20170604 kot na wystawie w Krakowie 7971* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:20170604_kot_na_wystawie_w_Krakowie_7971.jpg (Accessed: 3 November 2026). (images/pets/milo.jpg)
- Kreuzschnabel (no date) *Border-Collie-tri-colour-face-1* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Border-Collie-tri-colour-face-1.jpg (Accessed: 3 November 2026). (images/pets/bella.jpg)
- Laitche (no date) *Tabby cats of Ōizumi Ryokuchi Park, January 2019 - 8816* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Tabby_cats_of_%C5%8Cizumi_Ryokuchi_Park,_January_2019_-_8816.jpg (Accessed: 3 November 2026). (images/pets/ginger.jpg)
- Marius Vassnes (no date) *Jack Russel side profile* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Jack_Russel_side_profile.jpg (Accessed: 3 November 2026). (images/pets/alfie.jpg)
- MrPanyGoff (no date) *Juvenile Siberian Husky* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Juvenile_Siberian_Husky.jpg (Accessed: 3 November 2026). (images/pets/nova.jpg)
- Von.grzanka (no date) *Felis catus-cat on snow* [Photograph]. Wikimedia Commons, CC BY-SA 3.0. Available at: https://commons.wikimedia.org/wiki/File:Felis_catus-cat_on_snow.jpg (Accessed: 3 November 2026). (images/pets/pepper.jpg)
- W.carter (no date) *French bulldog in life jacket* [Photograph]. Wikimedia Commons, CC0. Available at: https://commons.wikimedia.org/wiki/File:French_bulldog_in_life_jacket.jpg (Accessed: 3 November 2026). (images/pets/coco.jpg)
- William Crochot (no date) *Teckel - 39* [Photograph]. Wikimedia Commons, CC BY-SA 4.0. Available at: https://commons.wikimedia.org/wiki/File:Teckel_-_39.jpg (Accessed: 3 November 2026). (images/pets/oscar.jpg)

The photos for Biscuit, Luna, Thumper, Kiwi, Max, Peanut and Shadow (ids 1-7) were added earlier; check and add their real source here.
