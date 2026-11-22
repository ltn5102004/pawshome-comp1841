# PawsHome colour system - "Cloud & Ink"

How the colours of the site were chosen, and the proof that they are readable. The values live at the top of `css/style.css`.

## 1. Idea

A young, fashion-style look: a soft **cloud** off-white base (Pantone's Colour of the Year 2026 is a white, *Cloud Dancer*), near-black **ink** text and black pill buttons, pastel tints for personality, and two bold "pop" colours: **electric violet** for the main action and **acid lime** for counters that need you.

## 2. Theory used

| Principle | How it is applied |
|---|---|
| **HSL / HSB model** | Every colour is `hsl(hue saturation lightness)`. A family keeps one hue; tints (+white) only raise lightness, shades (+black) only lower it, tones (+grey) lower saturation, so hover and soft states are predictable. |
| **Complementary harmony** | Violet 255° and lime 78° are 177° apart on the wheel (complementary), giving a strong focal point for the call-to-action and the counters. |
| **Pastel tints** | Lavender, pink, peach, butter, sky and mint all have the same high lightness, so they can be used together (stat boxes, role banners) without clashing. |
| **Tones as neutrals** | Ink and greys are tones of the violet hue (very low saturation), so even the "black" belongs to the palette. The background is a warm off-white, not pure #fff; text is ink, not pure #000. |
| **Colour psychology** | Mint = success (Available, Approved, Published), sky blue = information and trust (shelter staff, New, Reserved), butter yellow = waiting (Interviewing, Waiting for review), cherry red = refused / delete, grey = closed (Adopted). |
| **60-30-10 rule** | ~60% cloud background and cards; ~30% ink (text, normal buttons, active menu, admin sidebar, footer) plus pastel tints; ~10% violet only for the ONE main action of a page (*Find a pet*, *Send adoption request*, *Register*, *Add / Save pet*) and lime for attention counters. |
| **Role colours** | Each user type has its own pastel aura: adopter lavender-pink, pet owner peach-butter, shelter staff sky-mint, admin lavender-sky with ink. |
| **WCAG 2.2** | 4.5:1 for text, 3:1 for input/chip borders and focus rings (SC 1.4.3, 1.4.11). All pairs pass - see the table. |
| **Never colour alone** | Badges always contain words; success badges add a tick and refused badges a cross (hidden from screen readers); alerts have an icon; the admin to-do list has icon + colour + words. |
| **Semantic tokens** | Components only use tokens such as `--color-success`, `--color-cta`, `--color-border-strong`; there is no hex code outside the token block. Changing the whole theme only meant changing the tokens. |

## 3. Palette (layer 1)

| Token | HSL | Hex |
|---|---|---|
| `--ink-950` | hsl(250 24% 9%) | `#13111c` |
| `--ink-900` | hsl(250 22% 11%) | `#181622` |
| `--ink-800` | hsl(250 18% 20%) | `#2d2a3c` |
| `--ink-700` | hsl(250 12% 30%) | `#464356` |
| `--ink-600` | hsl(250 8% 38%) | `#5c5969` |
| `--ink-400` | hsl(250 6% 52%) | `#807d8c` |
| `--cloud-0` | hsl(40 40% 99%) | `#fdfdfb` |
| `--cloud-50` | hsl(40 22% 95.5%) | `#f6f4f1` |
| `--cloud-100` | hsl(40 18% 92%) | `#eeece7` |
| `--cloud-200` | hsl(40 14% 86%) | `#e0ddd6` |
| `--violet-50` | hsl(255 100% 97%) | `#f4f0ff` |
| `--violet-100` | hsl(255 95% 93%) | `#e5dcfe` |
| `--violet-200` | hsl(255 90% 86%) | `#cbbbfb` |
| `--violet-500` | hsl(255 88% 63%) | `#774ef4` |
| `--violet-600` | hsl(255 75% 55%) | `#6136e2` |
| `--violet-700` | hsl(255 62% 45%) | `#4f2cba` |
| `--lime-100` | hsl(78 85% 90%) | `#eefbd0` |
| `--lime-400` | hsl(78 88% 62%) | `#c0f349` |
| `--lime-800` | hsl(80 90% 22%) | `#496b06` |
| `--pink-50` | hsl(340 100% 97%) | `#fff0f5` |
| `--pink-100` | hsl(340 95% 92%) | `#fed7e4` |
| `--pink-600` | hsl(340 80% 52%) | `#e72364` |
| `--pink-700` | hsl(340 70% 40%) | `#ad1f4e` |
| `--peach-50` | hsl(22 100% 96%) | `#fff2eb` |
| `--peach-100` | hsl(22 100% 90%) | `#ffdfcc` |
| `--peach-600` | hsl(15 85% 50%) | `#ec4913` |
| `--peach-700` | hsl(15 80% 37%) | `#aa3913` |
| `--sky-50` | hsl(200 100% 96%) | `#ebf8ff` |
| `--sky-100` | hsl(200 95% 90%) | `#cdeefe` |
| `--sky-600` | hsl(205 85% 42%) | `#107ac6` |
| `--sky-700` | hsl(205 80% 32%) | `#105c93` |
| `--mint-50` | hsl(155 70% 94%) | `#e5faf1` |
| `--mint-100` | hsl(155 60% 86%) | `#c6f1df` |
| `--mint-600` | hsl(158 70% 34%) | `#1a9367` |
| `--mint-700` | hsl(158 75% 25%) | `#10704d` |
| `--butter-50` | hsl(48 100% 93%) | `#fff8db` |
| `--butter-100` | hsl(48 100% 82%) | `#ffeda3` |
| `--butter-600` | hsl(42 100% 45%) | `#e6a100` |
| `--butter-800` | hsl(35 90% 26%) | `#7e4c07` |
| `--cherry-50` | hsl(350 100% 97%) | `#fff0f2` |
| `--cherry-100` | hsl(350 95% 92%) | `#fed7de` |
| `--cherry-600` | hsl(350 72% 48%) | `#d32240` |
| `--cherry-700` | hsl(350 70% 38%) | `#a51d34` |

## 4. Meaning (layer 2)

| Token | Value | Meaning |
|---|---|---|
| `--color-bg / --color-surface` | cloud-50 / cloud-0 | 60% background and cards |
| `--color-text` | ink-900 | text |
| `--color-primary` | ink-900 | 30% normal buttons, active menu |
| `--color-link` | violet-700 | links |
| `--color-cta` | violet-600 | 10% main call-to-action |
| `--color-pop` | lime-400 | counters that need you |
| `--color-success*` | mint | good / done |
| `--color-info*` | sky | in progress, information |
| `--color-warning*` | butter | waiting for someone |
| `--color-error*` | cherry | refused, delete, errors |
| `--color-border-strong` | ink-400 | edges of form controls |
| `--color-focus` | violet-600 (white on dark) | keyboard focus ring |

## 5. Contrast check (WCAG 2.2 relative-luminance formula)

| Pair | Ratio | Needed | Result |
|---|---|---|---|
| ink text on cloud bg | 16.26:1 | 4.5:1 | Pass |
| ink text on surface | 17.52:1 | 4.5:1 | Pass |
| muted text on surface | 6.68:1 | 4.5:1 | Pass |
| muted text on bg | 6.19:1 | 4.5:1 | Pass |
| link violet-700 on surface | 8.59:1 | 4.5:1 | Pass |
| link violet-700 on bg | 7.96:1 | 4.5:1 | Pass |
| white on ink button | 17.85:1 | 4.5:1 | Pass |
| white on ink hover | 13.93:1 | 4.5:1 | Pass |
| white on violet CTA | 6.70:1 | 4.5:1 | Pass |
| white on violet CTA hover | 8.74:1 | 4.5:1 | Pass |
| ink on lime pop | 13.73:1 | 4.5:1 | Pass |
| control border ink-400 vs surface | 3.94:1 | 3:1 | Pass |
| control border vs bg | 3.66:1 | 3:1 | Pass |
| focus violet-600 vs bg | 6.10:1 | 3:1 | Pass |
| focus white vs ink sidebar | 18.67:1 | 3:1 | Pass |
| success text on tint | 5.59:1 | 4.5:1 | Pass |
| info text on tint | 6.52:1 | 4.5:1 | Pass |
| warning text on tint | 6.74:1 | 4.5:1 | Pass |
| error text on tint | 6.72:1 | 4.5:1 | Pass |
| white on error btn | 5.16:1 | 4.5:1 | Pass |
| neutral badge | 5.76:1 | 4.5:1 | Pass |
| violet-700 on violet-50 | 7.81:1 | 4.5:1 | Pass |
| ink on adopter aura violet-100 | 13.60:1 | 4.5:1 | Pass |
| ink on pink-100 | 13.64:1 | 4.5:1 | Pass |
| ink on owner aura peach-100 | 14.17:1 | 4.5:1 | Pass |
| ink on butter-100 | 15.21:1 | 4.5:1 | Pass |
| ink on staff aura sky-100 | 14.67:1 | 4.5:1 | Pass |
| ink on mint-100 | 14.49:1 | 4.5:1 | Pass |
| muted on violet-100 | 7.28:1 | 4.5:1 | Pass |
| muted ink-700 on peach-100 | 7.59:1 | 4.5:1 | Pass |
| role owner peach-700 icon/num on surface | 6.24:1 | 4.5:1 | Pass |
| role staff sky-700 on surface | 6.93:1 | 4.5:1 | Pass |
| role adopter violet-700 on surface | 8.59:1 | 4.5:1 | Pass |
| lime on ink (admin active) | 14.37:1 | 3:1 | Pass |
| sidebar text 78% white on ink | 11.18:1 | 4.5:1 | Pass |
| footer link violet-200 on ink | 10.23:1 | 4.5:1 | Pass |
| ink-900 on surface (role admin) | 17.52:1 | 4.5:1 | Pass |

## Sources

- Pantone (2025) *PANTONE 11-4201 Cloud Dancer - Color of the Year 2026*. https://www.pantone.com/color-of-the-year/2026
- W3C (2023) *Understanding SC 1.4.1 Use of Color*, *1.4.3 Contrast (Minimum)*, *1.4.11 Non-text Contrast*. WCAG 2.2. https://www.w3.org/WAI/WCAG22/Understanding/
- Google (no date) *Material Design 3: Color roles* and *Design tokens*. https://m3.material.io/styles/color/roles
- MDN Web Docs (no date) *hsl() CSS function*. https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/hsl
- Gordon, K. (2021) *Using Color to Enhance Your Design*. Nielsen Norman Group. https://www.nngroup.com/articles/color-enhance-design/
- Raikwar, H. (2023) *Master UI design: Enhance aesthetics with the 60-30-10 rule*. LogRocket. https://blog.logrocket.com/ux-design/60-30-10-rule
- WebAIM (no date) *Contrast and Color Accessibility*. https://webaim.org/articles/contrast/


## Bold accents (added 9 Nov 2026)

The first Cloud & Ink version was calm; these accents give each page a clear focal point. The loud colours are still limited to a few places, so the 60-30-10 balance stays.

| Accent | Where | Contrast |
|---|---|---|
| Violet -> magenta gradient (255 deg -> 322 deg, analogous) with a glow | the ONE main button per page, Register | white text 6.7:1 (violet end), 5.3:1 (magenta end) |
| Dark "night" banner hsl(255 35% 16%) with violet / magenta glows | home page hero | white 16.5:1, light text 12.9:1 |
| Lime highlighter | key words in headings, text selection, active menu item text | ink on lime 13.8:1, lime on ink 13.8:1, lime on night 15.3:1 |
| Brand stripe (violet -> magenta -> lime) | top of header and footer | decorative only |
| Deeper role auras (L 72-86%) | My account / Requests banners | ink 10.3:1 or more; small grey line uses ink-700 |
| Coral hsl(6 82% 46%) instead of brown | pet owner role colour (avatar, tile line) | white 5.0:1 |
