# Design System

The visual direction is approachable civic technology: grounded greens, a restrained gold accent, rounded functional surfaces, generous space, and clear native-app navigation. The original logo combines a location pin with bamboo-like stems and does not use the Las Piñas City seal.

## Tokens

| Purpose | Value |
| --- | --- |
| Primary | `#176B45` |
| Deep green | `#104D35` |
| Accent gold | `#D5A847` |
| Background | `#F4F8F3` |
| Surface | `#FFFFFF` |
| Text | `#20352C` |
| Muted text | `#65746D` |
| Border | `#DCE7DF` |
| Success | `#168451` |
| Warning | `#9A6513` |
| Error | `#B42318` |

Tokens are CSS custom properties in `frontend/src/styles/index.css` and are available to Tailwind 4 through its CSS-first pipeline. Reusable controls use 12–20px radii, subtle green-tinted shadows, a 48px default control height, and visible gold focus rings.

## Responsive behavior

Desktop uses a compact sticky top navigation and bounded content widths. At 800px and below, primary routes move to a safe-area-aware fixed bottom navigation. At 520px, grids collapse, action buttons become full width, and report panels use edge-to-edge space. The minimum supported layout width is 320px.

## Accessibility

Pages use semantic landmarks, explicit labels, keyboard navigation, focus-visible indicators, readable contrast, and status messages. Motion is reduced under `prefers-reduced-motion`. Icons supplement text rather than replacing it for critical actions.
