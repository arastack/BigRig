<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoursePackage;
use App\Models\Settings;
use App\Models\TrainingObjective;
use App\Support\Pricing;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Course packages (combo discounts) and the VAT rate added to tuition.
 */
class PackageController extends Controller
{
    public const MAX_GROUPS = 4;

    public function index()
    {
        return view('admin.packages.index', [
            'packages' => CoursePackage::orderByDesc('is_active')->orderBy('name')->get(),
            'courses' => TrainingObjective::orderBy('objective')->get(['id', 'objective', 'price']),
            'vatPercent' => Pricing::vatPercent(),
            'currency' => Settings::query()->value('base_currency') ?: '₦',
        ]);
    }

    public function store(Request $request)
    {
        $package = CoursePackage::create($this->validated($request));

        return back()->with('success', "{$package->name} created.");
    }

    public function update(Request $request, CoursePackage $package)
    {
        $package->update($this->validated($request, $package));

        return back()->with('success', "{$package->name} updated.");
    }

    public function destroy(CoursePackage $package)
    {
        $name = $package->name;
        $package->delete();

        return back()->with('success', "{$name} deleted. Applications already submitted keep the discount they were quoted.");
    }

    public function updateVat(Request $request)
    {
        $data = $request->validate([
            'vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [], ['vat_rate' => 'VAT rate']);

        $settings = Settings::first();
        if (! $settings) {
            return back()->with('error', 'Save the site information first, then set the VAT rate.');
        }

        $settings->forceFill(['vat_rate' => round((float) $data['vat_rate'], 2)])->save();

        return back()->with('success', 'VAT rate set to ' . Pricing::vatLabel() . '%. Applications already submitted keep the amount they were quoted.');
    }

    private function validated(Request $request, ?CoursePackage $package = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'discount' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'weeks' => ['nullable', 'integer', 'min:1', 'max:520'],
            'groups' => ['required', 'array', 'max:' . self::MAX_GROUPS],
            'groups.*' => ['nullable', 'array'],
            'groups.*.*' => ['nullable', 'integer'],
        ], [], [
            'groups' => 'course groups',
        ]);

        // Each submitted group carries a blank placeholder so empty groups are still seen
        $groups = collect($data['groups'])
            ->map(fn ($ids) => collect($ids ?? [])->filter(fn ($id) => filled($id))->map(fn ($id) => (int) $id)->unique()->values()->all())
            ->values();

        $existing = TrainingObjective::pluck('id')->map(fn ($id) => (int) $id);

        $errors = [];
        if ($groups->count() < 2) {
            $errors['groups'] = 'A package needs at least two course groups.';
        }
        foreach ($groups as $i => $ids) {
            if (empty($ids)) {
                $errors["groups.$i"] = 'Group ' . ($i + 1) . ' needs at least one course.';
            } elseif (collect($ids)->diff($existing)->isNotEmpty()) {
                $errors["groups.$i"] = 'Group ' . ($i + 1) . ' contains a course that no longer exists.';
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'discount' => $data['discount'],
            'weeks' => $data['weeks'] ?? null,
            'course_groups' => $groups->all(),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
