<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiscountController extends Controller
{
    /**
     * Display a listing of discounts and promo codes.
     */
    public function index(): View
    {
        $discounts = Discount::with('course')->withCount('invoices')->latest()->paginate(12);

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discount.
     */
    public function create(): View
    {
        $courses = Course::all();

        return view('admin.discounts.create', compact('courses'));
    }

    /**
     * Store a newly created discount in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'code' => ['required', 'string', 'max:50', 'uppercase', 'unique:discounts,code'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['nullable', 'date'],
            'expires_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')->with('success', "Discount code '{$validated['code']}' created successfully.");
    }

    /**
     * Show the form for editing the specified discount.
     */
    public function edit(Discount $discount): View
    {
        $courses = Course::all();

        return view('admin.discounts.edit', compact('discount', 'courses'));
    }

    /**
     * Update the specified discount in storage.
     */
    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'code' => ['required', 'string', 'max:50', 'uppercase', Rule::unique('discounts')->ignore($discount->id)],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['nullable', 'date'],
            'expires_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')->with('success', "Discount code '{$discount->code}' updated successfully.");
    }

    /**
     * Remove the specified discount from storage.
     */
    public function destroy(Discount $discount): RedirectResponse
    {
        $code = $discount->code;
        $discount->delete();

        return redirect()->route('admin.discounts.index')->with('success', "Discount '{$code}' deleted successfully.");
    }
}
