Here's the color palette extracted from the Wave screenshot, plus a full design system guide:

**Extracted hex colors:**

| Role seen in UI | Hex |
|---|---|
| Primary blue (buttons, links, active) | `#2B6CB0` / `#3182CE` |
| Sidebar background | `#F7F8FA` |
| Selected nav item bg | `#EBF8FF` |
| Card / panel background | `#FFFFFF` |
| Body text | `#2D3748` |
| Muted / secondary text | `#718096` |
| Border / divider | `#E2E8F0` |
| Inflow bar (chart blue) | `#3182CE` |
| Page background | `#F0F2F5` |
| CTA button (dark blue) | `#1A4E8C` |It seems the visual renderer is timing out right now, so here's the full design system as text — everything you need:

---

## 🎨 Color Palette (from Wave screenshot)

| Element | Hex |
|---|---|
| CTA Button | `#1A4E8C` |
| Primary / Links / Chart | `#3182CE` |
| Active nav item bg | `#EBF8FF` |
| Sidebar / left panel | `#F7F8FA` |
| Page background | `#F0F2F5` |
| Card / content panel | `#FFFFFF` |
| Body & heading text | `#2D3748` |
| Muted / secondary text | `#718096` |
| Borders & dividers | `#E2E8F0` |

---

## 📐 Usage Instructions

**Page background → `#F0F2F5`**
The outermost canvas. The slightly warm gray creates depth so white cards visually "float." Never use pure white as the page bg — cards need that contrast to be visible.

**Sidebar / left panel → `#F7F8FA`**
Slightly cooler than the page bg, which separates the persistent navigation from the content area without needing a heavy border between them.

**Cards & content panels → `#FFFFFF`**
All data cards, tables, modals, and form containers. Always pair with a `0.5px solid #E2E8F0` border — white on light gray needs a subtle edge to define boundaries.

**Selected / active nav item → `#EBF8FF`**
Pill or row highlight for the currently active nav entry. Pair with `#2B6CB0` for the icon and label text. Never use the full primary blue as the highlight bg — too heavy.

**Borders & dividers → `#E2E8F0`**
Card edges, table row lines, input outlines, horizontal rules. Use `0.5px` weight for card borders, `1px` for input focus rings.

**Links & interactive accents → `#3182CE`**
Clickable text, chart inflow bars, icon highlights. Also used for secondary outlined buttons (this color as both the border and label text).

**Primary CTA button → `#1A4E8C`**
Reserved for the single most important action per screen ("Create new", "Pay now"). Rounded pill shape with white text. Use only **once per view** — more than one dilutes the hierarchy.

**Body & heading text → `#2D3748`**
All headings, table values, and primary labels. Softer than pure black, which reduces eye strain in data-dense financial interfaces.

**Muted / secondary text → `#718096`**
Timestamps, metric card labels above numbers, placeholder text, help text, and inactive nav items. Never apply this to anything actionable or clickable.

---

## 🔤 Recommended Fonts

| Role | Font | Why |
|---|---|---|
| UI / Body | **Inter** | Has proper tabular figures — digits align perfectly in financial tables |
| Headings | **DM Sans** | Slightly rounded, approachable but professional — great for page/section titles |
| Data / Monospace | **JetBrains Mono** | Account numbers, transaction IDs, codes, hex values |

All three are free on Google Fonts. Inter should be your default; switch to DM Sans only for `h1`/`h2` headings, and JetBrains Mono only for raw data strings.