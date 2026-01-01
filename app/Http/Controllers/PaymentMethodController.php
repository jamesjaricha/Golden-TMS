<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::withCount('complaints')->latest()->get();
        return view('payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment_methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'is_active' => 'boolean',
        ]);

        $paymentMethod = PaymentMethod::create($validated);

        ActivityLogService::log(
            'payment_method_created',
            "Created payment method: {$paymentMethod->name}",
            $paymentMethod
        );

        return redirect()->route('payment-methods.index')->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $paymentMethod->loadCount('complaints');
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'is_active' => 'boolean',
        ]);

        $oldName = $paymentMethod->name;
        $paymentMethod->update($validated);

        ActivityLogService::log(
            'payment_method_updated',
            "Updated payment method: {$oldName} to {$paymentMethod->name}",
            $paymentMethod
        );

        return redirect()->route('payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Prevent deletion if payment method has associated tickets
        if ($paymentMethod->complaints()->count() > 0) {
            return back()->with('error', 'Cannot delete payment method with associated tickets.');
        }

        $name = $paymentMethod->name;
        $paymentMethod->delete();

        ActivityLogService::log(
            'payment_method_deactivated',
            "Deactivated payment method: {$name}"
        );

        return redirect()->route('payment-methods.index')->with('success', 'Payment method deactivated successfully.');
    }
}
