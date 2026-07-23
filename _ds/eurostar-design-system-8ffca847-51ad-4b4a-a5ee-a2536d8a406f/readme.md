# Eurostar Design System

Source: a mounted local codebase, **`Eurostar Sito PROPOSTA 2/`** — a proposed-redesign HTML prototype of the Eurostar corporate website (three pages: home, "Machines / Filling" category, and a "MEC LD" product detail page), built as click-through Design Components with real copy, real photography/video, and a working scroll-driven interaction layer. No Figma link or additional codebase was provided. All tokens, copy tone, and components in this system are reverse-engineered from that prototype's inline styles and markup — treat exact pixel/color/spacing values as ground truth over generic conventions.

## Company & product context

**Eurostar S.r.l.** is an Italian manufacturer of bottling and packaging machinery, based in **Canelli, Piedmont** — <cite index="0-1">"independent and family-run since 1996, engineering in the heart of Piedmont."</cite> 2026 marks their 30th anniversary (logo lockup includes a "30 years — 1996–2026" mark). They are a B2B industrial equipment maker, not a consumer brand: customers are bottlers and producers across wine, spirits, beer, water & soft drinks, juices, and oil & vinegar (plus pharma/chemicals as adjacent sectors seen in imagery).

**Machine range** (filling technologies): Gravity, Volumetric, Isobaric, Vacuum — sold as monobloc units or complete lines, spanning capping, labelling and washing too. Named products/lines observed: **MEC LD** (gravity monobloc), **MEC PV** (volumetric), **POWERFILL** (high-speed isobaric), **CANFILL 9.1** (can filling & seaming), and a smaller-format sub-brand **Squadron** ("soluzioni dedicate ai piccoli laboratori" — solutions for small labs/craft producers).

Company pillars evident across the site: heritage/family ownership, in-house engineering ("every line built in-house"), global reach (60+ countries, 2,000+ machines installed per the ticker copy), and strong post-sale support (24-hour response, remote diagnostics, spare parts).

## Content fundamentals

- **Voice**: confident, technical, precise — short declarative sentences, industrial vocabulary used correctly (isobaric, monobloc, gearmotor, AISI 304/316L), never fluffy. Sample: <cite index="0-2">"Air is drawn from the bottle, replaced with inert gas, and the exact level is set with excess returned to a separate tank."</cite>
- **Person**: mostly third-person/product-first ("We build the...", "Every station is guarded..."); occasional direct "you" in prompts ("What do you bottle?", "Keep scrolling").
- **Casing**: headlines and nav/labels are ALL CAPS (via `text-transform:uppercase` on Barlow); body copy is sentence case.
- **Punctuation as device**: hero headline plays with a literal period — "Enjoy the filling." animates as if liquid fills the letterform. Em dashes used often to append a clarifying clause.
- **Numbers as proof**: specs are always concrete and unit-labelled — "24k bottles/hour", "±1 mm fill accuracy", "60+ countries" — never vague superlatives.
- **No emoji, ever.** Iconography (line SVGs) carries any illustrative weight instead.
- **Taglines**: "Enjoy the filling." (hero) and, playfully, "Enjoy the filling and the feeling." in the footer sign-off. Logo tagline: "BOTTLING & PACKAGING SOLUTIONS."
- **Micro-copy tone**: utility-bar ticker rotates plain, informative lines ("Full support: we respond within 24 hours") — never salesy.

## Visual foundations

- **Palette**: deep navy (`#14213F`) is the dominant brand color — used as the default page/background ink, not just an accent. A near-black navy (`#000A23`) tops the page as a slim utility bar; an even darker "space" navy (`#0B1330`) is used for full-bleed statement sections. Action blue (`#25387E`) is the one accent used for every CTA/link/selection; a lighter steel blue (`#516FB4`) is exclusively the *hover* state for that accent; a pale periwinkle (`#8FA6D9`) is reserved for small caps labels/kickers sitting on dark backgrounds. Light sections use near-white grays (`#F5F6F8`, `#F2F3F5`) as card/section backgrounds, never pure white blocks side by side.
- **Type**: two families only. **Barlow** (700–900) for everything structural — H1s, section titles, nav, buttons, kickers, stat numbers — always uppercase, tight/negative letter-spacing at large sizes, wide positive tracking at small caps sizes. **Roboto** (300–400) for all body copy and long-form text, light-weight (300) by default, giving a technical-but-airy read against the heavy display type. No serif anywhere.
- **Scale**: hero headlines go enormous — up to `clamp(52px,10.5vw,180px)` on the homepage and `clamp(72px,13vw,190px)` on a product page — line-height compressed to 0.82–0.92. Body copy stays modest (15–20px, line-height 1.6–1.7) for contrast.
- **Spacing/justezza**: fluid, viewport-relative padding via `clamp()` almost everywhere (e.g. section block padding `clamp(64px,10vh,140px)`, inline padding `clamp(20px,6vw,80px)`) rather than fixed breakpoint padding — the layout breathes at every size. Content is centered in a `1360px` (or `1180px` for statement/reading sections) max-width rail.
- **Backgrounds**: alternates full-bleed video (hero, product 3D-render clips), full-bleed photography with dark gradient scrims (product category heroes), and flat navy/near-black sections for "statement" copy blocks. No hand-drawn illustration, no repeating pattern/texture, no gradient meshes — gradients that do appear are strictly functional scrims/vignettes for text legibility over media (e.g. `linear-gradient(90deg, rgba(20,33,63,.92) 0%, transparent 100%)`).
- **Animation**: scroll-driven and deliberate, never bouncy. Scroll-linked "pinned" sections (hero headline lift, horizontal industries carousel, product "how it works" station sequence) driven by scroll position math, not CSS scroll-timeline. Standard easing is `cubic-bezier(0.22,1,0.36,1)`; reveal-on-scroll fades+rises (opacity 0→1, translateY 20–26px→0) over 0.7–0.9s. Numbers count up on scroll-into-view. Video clips crossfade/alternate on `ended`. Respects `prefers-reduced-motion`.
- **Hover states**: color shift is the default hover treatment — action blue darkens to `#516FB4→#25387E` or lightens depending on context; image cards scale their photo 1.05–1.07x inside a fixed-size frame (never the card itself scaling); an underline/arrow slides in on link rows. Cards lift `translateY(-6px to -8px)` plus a soft blue-tinted shadow.
- **Press states**: not distinctly styled beyond the hover treatment (no explicit `:active` scale/darken found) — treat press as a slightly stronger version of hover.
- **Borders**: hairline `1px` borders in translucent navy (`rgba(20,33,63,0.16–0.22)`) separate spec rows and stat columns on light backgrounds; translucent white (`rgba(255,255,255,.25)`) for the same role on dark nav/footer.
- **Shadows**: used sparingly and only for elevation, never decoratively — a soft blue-tinted lift shadow on hovered cards (`0 18px 44px rgba(37,56,126,.12)`), a hairline shadow under the solid nav bar, and a heavier shadow under the full-width mega-menu panel.
- **Corner radius**: effectively **zero** everywhere — cards, images, buttons and inputs are all hard-edged rectangles. The one shape signature is a **45° diagonal cut on the top-right corner of every filled button/CTA** (`clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 0 100%)`) — this notch is the brand's substitute for rounded corners. Small dots (ticker dot, step indicators) and the star in the logo are the only fully round/organic shapes.
- **Cards**: no border, no radius, no drop shadow at rest — just a flat light-gray fill (`#F2F3F5`) or a photo filling the frame; shadow and lift only appear on hover.
- **Transparency/blur**: transparency (rgba white/navy overlays) is used constantly for scrims and translucent panels; no backdrop-filter/blur was found anywhere in the source — overlays are flat rgba, not glassmorphism.
- **Imagery color vibe**: industrial-cool — product renders and factory photography lean grayscale/steel/blue; category imagery (wine, spirits, beer, water bottles) is warmer/product-true-color but always gets a navy-tinted gradient scrim overlaid when text sits on top. No visible film grain.
- **Layout rules**: nav is fixed/pinned, transparent-over-hero until scroll then solidifies to white; a slim dark "utility bar" sits above the main nav (desktop only, hidden under 1240px). A full-page dark mega-menu panel drops from the nav on hover/click.

## Iconography

- **System**: hand-coded inline line-style SVGs, 24×24 viewBox, `stroke` only (no fill), stroke-width 1.4–1.7, `stroke-linecap/linejoin: round` — simple geometric glyphs (crate/catalogue, newspaper/news, envelope/contact, globe/language, search, arrow, checklist, gauge, shield/protection, screen/HMI). This is a **custom minimal line-icon set**, not a named icon font/library.
- No emoji, no unicode-character icons, no PNG icon sprites — every icon in the source is inline SVG markup.
- The 45° arrow-in-square icon used for CTAs ("→" rotated) and the diagonal button-corner notch are the two most repeated iconographic motifs.
- **This design system does not bundle a copied icon set** (the source only had a handful of bespoke inline SVGs, not a library). Components below use the same restrained inline-SVG approach for their icon slots; when a consuming project needs a broader icon set, [Lucide](https://lucide.dev) (CDN) is the closest stroke-style match to substitute — **flagged here as a substitution**, not sourced from Eurostar.

## Fonts — flag for follow-up

**Barlow** (400/500/600/700/800/900) and **Roboto** (300/400/500/700) are both open-source Google Fonts already used via a Google Fonts CDN `@import` in `tokens/fonts.css` — this matches the source exactly (the prototype also loads them from `fonts.googleapis.com`), so no substitution was needed and no local binaries were bundled. If you need fully offline/self-hosted fonts, ask and I can fetch and embed the `.woff2` files directly.

## Assets copied in

`assets/` contains real files copied from the source proposal: the Eurostar wordmark (color, white, nav, "30 years" lockups), the Squadron sub-brand mark, founder/team photography, factory heritage photo, machine product renders (MEC LD, MEC PV, POWERFILL, CANFILL 9.1), container cutaways (PET/glass/wine bottle), sector imagery for all 9 industries, solution-icon photography (filling/capping/labelling/washing), sustainability and tech-line photography, a world/references map, and a few "how it works" station photos. Videos and some near-duplicate asset variants (`-v2`, `-v3`…) were left in the source folder and not copied to keep this project lean — ask if you need a specific one pulled in.

## Index — what's in this project

- `styles.css` — root stylesheet; import this one file.
- `tokens/` — `colors.css`, `typography.css`, `spacing.css` (custom properties), `fonts.css` (Google Fonts import).
- `assets/` — logos, product/sector photography (see above).
- `guidelines/` — foundation specimen cards shown in the Design System tab (Colors, Type, Spacing, Brand groups).
- `components/core/` — `Button`, `Tag`, `SpecRow`, `SectionKicker`, `StatBlock`, `MachineCard` — the reusable primitives distilled from the prototype (no component library existed in the source, so this is an "intentional addition" set sized to the brand's needs — see below).
- `ui_kits/marketing-site/` — a recreation of the Eurostar marketing site: home hero, industries strip, machine range grid, and a MEC LD-style product detail page, wired together with real click-through nav.
- `SKILL.md` — Claude Code / Agent Skills compatible entry point.

**Intentional additions**: the source prototype had no formal component library (it's hand-built page markup) — `components/core/` is therefore an authored-from-scratch set sized to what the three source pages actually use (buttons, tags/kickers, spec rows, stat blocks, machine cards), not a generic design-system starter kit. Nothing here was invented beyond what appears at least once in the source pages.

## Caveats & ask

- Only one local codebase was available (no Figma, no additional product surfaces) — this system reflects a **prototype/proposal**, not the live production Eurostar site; treat it as the best available ground truth.
- No dedicated icon library or icon font was found in the source; the substitution note above applies if you need more icons than the handful of bespoke SVGs shown.
- Please review the copied imagery selection — many near-duplicate asset variants exist in the source folder that weren't copied; flag any specific ones you'd like added.
- Sample slide deck: none was provided, so no `slides/` was created — say the word if you'd like a Eurostar-branded deck template.
