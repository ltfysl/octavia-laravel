# Billing & Credits

## Model

Octavia uses a **usage-based credit model** — the natural fit for LLM costs,
which scale with engine steps, not seats or calendar months.

- **1 credit = 1 engine step.** Evaluate runs cost 1 credit (single step);
  optimize runs reserve `max_steps` credits.
- Runs **reserve** credits at creation. When a run reaches a terminal state
  (`RunObserver`), unused steps are **refunded** automatically.
- New accounts receive a **signup grant** (default 100, configurable via
  `OCTAVIA_SIGNUP_CREDITS`).
- Every mutation writes a `credit_transactions` ledger row in the same DB
  transaction as the balance update — the balance is always reconstructible.

## Implementation

| Piece | Location |
|---|---|
| Ledger + balance mutation | `App\Services\CreditService` |
| Insufficient-balance exception | `App\Exceptions\InsufficientCreditsException` |
| Refund on terminal state | `App\Observers\RunObserver` |
| Reservation at run start | `RunController::store` |
| User-facing history | `/settings/billing` (`settings/Billing.vue`) |
| Public pricing page | `/pricing` (`Pricing.vue`) |

The consume path uses an atomic conditional `decrement`
(`WHERE credits_balance >= amount`) so concurrent run starts cannot
overdraw a balance.

## Stripe launch checklist (human action)

Paid plans are designed but not activated until Stripe credentials exist:

1. Create Stripe account + products: Free ($0) and Pro ($19/month, 5,000 credits).
2. Fill `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` in `.env`.
3. Add a `subscriptions` table + Cashier (or hand-rolled webhook controller):
   `checkout.session.completed` → set plan=pro; `invoice.payment_failed` → dunning;
   `customer.subscription.deleted` → downgrade to free.
4. Monthly credit top-up job for pro users (scheduler): grant 5,000 on renewal.
5. Surface upgrade CTA in `/settings/billing`.

Until then, Pro shows "Coming soon" on `/pricing` and all users operate on the
free credit model. No fake payment UI is exposed.
