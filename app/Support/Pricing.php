<?php

namespace App\Support;

use App\Models\CoursePackage;
use App\Models\Settings;
use App\Models\TrainingObjective;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * The single place fees are calculated: course prices, package discounts, then VAT.
 */
class Pricing
{
    public const DEFAULT_VAT_PERCENT = 7.5;

    /**
     * @return array{subtotal: float, discount: float, discount_label: ?string, tax: float, total: float}
     */
    public static function quote(array $courseIds): array
    {
        $courses = TrainingObjective::whereIn('id', array_unique(array_map('intval', $courseIds)))->get();
        $subtotal = (float) $courses->sum('price');

        $combo = self::combosFor($courses)->sortByDesc('discount')->first();
        $discount = (float) min($combo['discount'] ?? 0, $subtotal);
        $tax = round(($subtotal - $discount) * self::vatRate(), 2);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_label' => $combo['name'] ?? null,
            'tax' => $tax,
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }

    /**
     * VAT as a percentage, e.g. 7.5. Set under Packages & VAT.
     */
    public static function vatPercent(): float
    {
        $rate = once(fn () => Settings::query()->value('vat_rate'));

        return $rate === null ? self::DEFAULT_VAT_PERCENT : (float) $rate;
    }

    /**
     * VAT as a fraction, e.g. 0.075.
     */
    public static function vatRate(): float
    {
        return self::vatPercent() / 100;
    }

    /**
     * VAT percentage formatted for labels: "7.5", "10".
     */
    public static function vatLabel(): string
    {
        return rtrim(rtrim(number_format(self::vatPercent(), 2, '.', ''), '0'), '.');
    }

    /**
     * Active packages for display.
     *
     * @return array<int, array{id: int, name: string, description: ?string, discount: float, weeks: ?int, groups: array<int, array<int, int>>}>
     */
    public static function packages(): array
    {
        return self::activePackages()->map(fn (CoursePackage $package) => [
            'id' => $package->id,
            'name' => $package->name,
            'description' => $package->description,
            'discount' => (float) $package->discount,
            'weeks' => $package->weeks,
            'groups' => $package->groups(),
        ])->values()->all();
    }

    /**
     * Package discounts the selected courses qualify for.
     */
    public static function combosFor(Collection $courses): Collection
    {
        $ids = $courses->pluck('id');

        return collect(self::packages())
            ->filter(fn ($package) => self::qualifies($package['groups'], $ids))
            ->values();
    }

    /**
     * Packages with the ids of the courses in each group, for the application form's live total.
     * Groups are limited to the courses offered on the form.
     */
    public static function combosForBrowser(Collection $courses): array
    {
        $offered = $courses->pluck('id')->map(fn ($id) => (int) $id);

        return collect(self::packages())->map(fn ($package) => [
            'name' => $package['name'],
            'description' => $package['description'],
            'discount' => $package['discount'],
            'weeks' => $package['weeks'],
            'groups' => collect($package['groups'])
                ->map(fn ($group) => $offered->intersect($group)->values()->all())->all(),
        ])->values()->all();
    }

    private static function qualifies(array $groups, Collection $ids): bool
    {
        $ids = $ids->map(fn ($id) => (int) $id);

        return count($groups) > 0
            && collect($groups)->every(fn ($group) => $ids->intersect($group)->isNotEmpty());
    }

    private static function activePackages(): Collection
    {
        return once(function () {
            if (! Schema::hasTable('course_packages')) {
                return collect();
            }

            return CoursePackage::active()->orderBy('name')->get();
        });
    }
}
