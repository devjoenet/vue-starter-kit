# Design Context

## Users
This starter is the baseline for Southeast Code marketing pages, demo surfaces, CRM-style workspaces, client portals, and internal tools. The public-facing version must help prospects and stakeholders understand quickly that Southeast Code builds practical custom systems, while the same visual language must scale cleanly into more operational product surfaces later.

## Brand Personality
The brand should feel confident, energetic, and modern. It should read as technically capable and deliberate, not trendy for its own sake. Interfaces should create trust quickly, show momentum, and feel authored rather than assembled from a generic starter template.

## Aesthetic Direction
Design for both light and dark themes using the Southeast Code palette as the base:

- Deep Anchor: `#1e2022` / `oklch(23.3% 0.009 257.7)` for dark-mode backgrounds and light-mode text.
- Light Surface: `#f0f5f9` / `oklch(96.7% 0.01 238.1)` for light-mode backgrounds and dark-mode supporting surfaces.
- Primary Brand: `#218380` / `oklch(51.8% 0.098 194.5)` for the main identity, primary CTAs, and key emphasis.
- Action Cyan: `#47b8e0` / `oklch(71.9% 0.124 230.3)` for secondary emphasis, links, and supporting brand cues.
- Vivid Accent: `#e71d36` / `oklch(59.1% 0.228 25.9)` for limited high-attention accents.
- Soft Muted: `#a593e0` / `oklch(67.5% 0.106 287.1)` for tertiary accents and atmospheric moments.

The current `Welcome.vue` stack is the style baseline to spread across the rest of the application:

- One dominant shell or page-purpose surface should lead the screen, with generous inset padding and rounded geometry.
- Use a quiet utility header and let the primary page message own the visual hierarchy.
- Prefer oversized, left-aligned display type with restrained supporting copy rather than stacked card headlines or dashboard chrome.
- Use one integrated media or illustration stage blended into the surface with vignettes and accent light, not fake browser frames or boxed mockups.
- Move secondary proof, supporting explanation, and expansion paths into a distinct lower band or follow-up section instead of crowding the hero.
- Keep brand-mark coloring consistent: Georgia shape in `Primary Brand`, code glyph in `Action Cyan`.
- The handwritten accent treatment seen on the welcome page is a selective emphasis tool. Use it sparingly for section kickers or a single emphasized word, not as a default heading style everywhere.
- Favor tinted neutrals, softened gradients, subtle blur, and restrained glow. Avoid overbright dark mode and loud ambient color wash.

Explicitly avoid code-window cosplay, nested card stacks, fake metrics, floating pill clutter as primary structure, and generic SaaS panel compositions.

## Design Principles
1. Lead with one clear message per major region. Hero sections, headers, and support bands should each have a single job.
2. Give important surfaces interior breathing room. Nothing critical should feel pinned to the edge of a shell.
3. Separate primary narrative from secondary proof. Explain first, support second.
4. Use media as evidence and atmosphere, not decoration. Integrated illustration is preferred over framed mockups.
5. Keep color assignments purposeful and consistent. `Primary Brand` drives identity and main actions; `Action Cyan` supports and highlights.
6. Build for transferability. Public pages, auth screens, dashboards, and admin views should all feel like variations of one system, not separate design languages.
