<?php

namespace App\Support;

use App\Models\TrainingObjective;
use Illuminate\Support\Collection;

/**
 * The single place fees are calculated: course prices, package discounts, then VAT.
 */
class Pricing
{
    /**
     * @return array{subtotal: float, discount: float, discount_label: ?string, tax: float, total: float}
     */
    public static function quote(array $courseIds): array
    {
        $courses = TrainingObjective::whereIn('id', array_unique(array_map('intval', $courseIds)))->get();
        $subtotal = (float) $courses->sum('price');

        $combo = self::combosFor($courses)->sortByDesc('discount')->first();
        $discount = (float) min($combo['discount'] ?? 0, $subtotal);
        $tax = round(($subtotal - $discount) * Nigeria::VAT_RATE, 2);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_label' => $combo['name'] ?? null,
            'tax' => $tax,
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }

    /**
     * Package discounts the selected courses qualify for.
     */
    public static function combosFor(Collection $courses): Collection
    {
        return collect(config('admissions.combos', []))->filter(function ($combo) use ($courses) {
            return collect($combo['groups'])->every(fn ($keywords) => $courses->contains(
                fn ($course) => collect($keywords)->contains(fn ($word) => stripos($course->objective, $word) !== false)
            ));
        })->values();
    }

    /**
     * Combos with the ids of the courses in each group, for the application form's live total.
     */
    public static function combosForBrowser(Collection $courses): array
    {
        return collect(config('admissions.combos', []))->map(fn ($combo) => [
            'name' => $combo['name'],
            'discount' => (float) $combo['discount'],
            'groups' => collect($combo['groups'])->map(fn ($keywords) => $courses
                ->filter(fn ($course) => collect($keywords)->contains(fn ($word) => stripos($course->objective, $word) !== false))
                ->pluck('id')->values()->all())->all(),
        ])->values()->all();
    }
}
