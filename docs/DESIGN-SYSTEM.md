# Better Las Piñas Design System

The product uses an approachable civic-service visual language: Las Piñas green, restrained gold accents, warm off-white backgrounds, rounded native-app surfaces, clear status hierarchy, and compact mobile navigation. The City of Las Piñas seal identifies the experience, while documentary-style concern photos and the City Hall landmark support local context.

## Core tokens

| Purpose | Value |
| --- | --- |
| Primary green | `#176B45` |
| Deep green | `#104D35` |
| Accent gold | `#D5A847` |
| Background | `#F4F8F3` |
| Surface | `#FFFFFF` |
| Text | `#20352C` |
| Muted text | `#65746D` |
| Border | `#DCE7DF` |
| Success | `#168451` |
| Warning | `#996515` |
| Error | `#B42318` |

Tokens live in `frontend/src/styles/index.css`. Controls favor pill or 12–24px rounded geometry, subtle green-tinted elevation, visible focus rings, and touch targets around 44–52px.

## Layout patterns

- Mobile uses a branded top app bar and five-item bottom navigation: Home, Map, Report, Track, and Profile.
- Desktop uses a persistent sidebar for public and resident routes; staff and administrators use a role-aware workspace navigation.
- Resident screens use progress cards, compact metrics, report lists, photographic evidence, and a vertical timeline.
- Staff and administrator screens use operational heroes, metric cards, tabs, queues, charts, calendars, tables, and responsive detail panels.
- Maps use green pins and routes, restrained neutral tiles, floating filters, and bottom information sheets.
- Skeleton loaders mirror the final dashboard, list, detail, profile, analytics, and map structures.

## Motion and interaction

Page entrances, active navigation, buttons, cards, filters, modals, progress rings, charts, and loaders use short purposeful transitions. Horizontal concern cards use scroll snapping and touch gestures. `prefers-reduced-motion` disables nonessential animation.

## Accessibility

The interface uses semantic landmarks, explicit form labels, keyboard-operable controls, descriptive button labels, focus-visible indicators, accessible modal behavior, live status messages, readable contrast, and text labels alongside critical icons. The minimum supported viewport width is 320px.
