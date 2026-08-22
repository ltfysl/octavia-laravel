# Colors

All tokens are defined once in `resources/css/app.css` under Tailwind v4 `@theme`.

## Ink — structure & text (cool dark gray-violet, never pure black)
| Token | Value | Use |
|-------|-------|-----|
| ink-950 | #101018 | Headings, strongest text |
| ink-900 | #1a1a26 | Body text |
| ink-700 | #3d3d52 | Secondary text, labels |
| ink-500 | #6b6b85 | Muted text |
| ink-300 | #b3b3c7 | Placeholders, timestamps |
| ink-200 | #d5d5e2 | Borders |
| ink-100 | #e9e9f1 | Dividers, meter tracks |

## Paper — surfaces (warm off-white, the workspace feel)
| Token | Value | Use |
|-------|-------|-----|
| paper-50 | #fbfbfa | App background |
| paper-100 | #f5f5f2 | Panels, code blocks |
| paper-200 | #ecece7 | Hover fills |
| paper-300 | #dfdfd7 | Strong hover borders |

## Violet — the single brand accent
| Token | Value | Use |
|-------|-------|-----|
| violet-600 | #5f4bd8 | Primary buttons, active nav, logo tile |
| violet-500 | #7563e8 | Focus rings, score bars below target |
| violet-50/100 | #f3f1fe/#e7e3fd | Active nav background, badges |

## Signal colors — meaning, never decoration
| Token | Use |
|-------|-----|
| mint-500 #1fa97a | Score ≥ target / ≥ 80%, passed criteria |
| amber-450 #e8a13c | Mid-range scores (50–79%), mutation steps |
| rose-450 #e0526b | Failing scores (< 50%), destructive actions, errors |

## Rules
- Never introduce new hues per feature. New categories reuse violet or neutral tones.
- Dark surfaces (`ink-950`) are used exactly twice: the final CTA band on the landing page and the prompt-content block in run steps (code-instrument feel).
- Contrast: all text/background pairs meet WCAG AA (ink-500 on paper-50 = 4.6:1 minimum).
