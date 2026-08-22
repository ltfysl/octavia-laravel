# Landing page

## Structure
1. **Hero** — badge ("Fine-tuning, but for prompts"), h1, subtitle, dual CTA (register / how-it-works anchor), and a static product mock showing a 3-step evolution (33% → 67% → 100%). The mock is the pitch: it shows the actual product mechanic before any scrolling.
2. **How it works** — 3 numbered steps: describe "good" → run evolution → inspect & keep.
3. **Features** — 6 cards, each tied to a real implemented capability (benchmarks, evolution engine, step visibility, playground, versions, marketplace). No feature is advertised that doesn't exist in the app.
4. **FAQ** — 4 `<details>` items answering the real objections (why not just ask an AI?, coding skills?, which models?, ownership?).
5. **Final CTA** — dark ink band, single action.

## SEO fundamentals
- Semantic `h1→h2→h3` hierarchy; one h1 per page.
- Title + meta description via Inertia `<Head>`.
- Open Graph + Twitter card tags on the home page.
- JSON-LD `SoftwareApplication` structured data with honest free price.
- `/sitemap.xml` (SitemapController) lists all public routes; `public/robots.txt` disallows the authenticated app area.
- Fonts self-hosted via Bunny Fonts CDN with preconnect; build output is code-split by Vite.

## Honesty rules
- No fabricated testimonials, customer counts or fake logos. Social proof will be added only when real.
- Pricing claims must match the pricing page exactly.
