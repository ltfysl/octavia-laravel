<?php

namespace App\Services;

use App\Exceptions\InsufficientCreditsException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Credit ledger for usage-based billing. Every mutation writes a
 * credit_transactions row inside the same transaction as the balance
 * update, so the balance is always reconstructible from the ledger.
 *
 * 1 credit = one engine step. Runs reserve max_steps up front and are
 * refunded for steps that never executed.
 */
class CreditService
{
    public const SIGNUP_GRANT = 100;

    public const REASON_SIGNUP = 'signup_grant';

    public const REASON_RUN_RESERVED = 'run_reserved';

    public const REASON_RUN_REFUND = 'run_refund';

    public const REASON_ADMIN_GRANT = 'admin_grant';

    public function signupGrant(): int
    {
        return (int) config('llm.signup_credits', self::SIGNUP_GRANT);
    }

    public function grantSignup(User $user): void
    {
        $this->grant($user, $this->signupGrant(), self::REASON_SIGNUP);
    }

    public function grant(User $user, int $amount, string $reason, array $meta = []): void
    {
        DB::transaction(function () use ($user, $amount, $reason, $meta) {
            $user->increment('credits_balance', $amount);
            $user->creditTransactions()->create([
                'delta' => $amount,
                'reason' => $reason,
                'meta' => $meta ?: null,
            ]);
        });
    }

    /**
     * @throws InsufficientCreditsException
     */
    public function consume(User $user, int $amount, string $reason, array $meta = []): void
    {
        DB::transaction(function () use ($user, $amount, $reason, $meta) {
            // Atomic guard: only decrement when the balance can cover it.
            $affected = User::query()
                ->whereKey($user->id)
                ->where('credits_balance', '>=', $amount)
                ->decrement('credits_balance', $amount);

            if ($affected === 0) {
                throw new InsufficientCreditsException((int) ($user->credits_balance ?? 0), $amount);
            }

            $user->creditTransactions()->create([
                'delta' => -$amount,
                'reason' => $reason,
                'meta' => $meta ?: null,
            ]);
        });
    }
}
