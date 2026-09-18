<?php

namespace App\Support;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Carbon;

/** Suscripciones de clientes (membresías personal/business) -> tabla subscriptions. */
class Subscriptions
{
    /** La membresía activa del usuario (o null). */
    public static function activeFor(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $row = Subscription::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if (! $row) {
            return null;
        }

        $end = $row->end_date ? Carbon::parse($row->end_date) : null;
        if ($end && $end->lt(Carbon::now())) {
            return null; // vencida
        }

        return [
            'id' => (int) $row->id,
            'membership_type' => (string) $row->membership_type,
            'start_date' => (string) $row->start_date,
            'end_date' => (string) ($row->end_date ?? ''),
        ];
    }

    /** Igual que el legacy verificador.php: exige login + membresía activa. */
    public static function hasActive(?User $user): bool
    {
        return self::activeFor($user) !== null;
    }

    /** Suscribirse (inserta o renueva la membresía del usuario). */
    public static function subscribe(User $user, string $membershipType): Subscription
    {
        $existing = Subscription::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        $data = [
            'membership_type' => $membershipType,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $existing->update($data);

            return $existing->fresh();
        }

        return Subscription::query()->create(array_merge(['user_id' => $user->id], $data));
    }
}