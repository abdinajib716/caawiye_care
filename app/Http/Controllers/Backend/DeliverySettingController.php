<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DeliveryLocation;
use App\Models\DeliveryPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliverySettingController extends Controller
{
    public function index(): View
    {
        $locations = DeliveryLocation::orderBy('name')->get();
        $prices = DeliveryPrice::with(['pickupLocation', 'dropoffLocation'])->orderBy('id', 'desc')->get();

        return view('backend.pages.delivery-settings.index', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Delivery Settings'), 'url' => null],
            ],
            'locations' => $locations,
            'prices' => $prices,
        ]);
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:delivery_locations,name',
        ]);

        DeliveryLocation::create($validated);

        return redirect()->route('admin.delivery-settings.index')
            ->with('success', __('Location added successfully'));
    }

    public function destroyLocation(DeliveryLocation $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('admin.delivery-settings.index')
            ->with('success', __('Location deleted successfully'));
    }

    public function storePrice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pickup_location_id' => 'required|exists:delivery_locations,id',
            'dropoff_location_id' => 'required|exists:delivery_locations,id|different:pickup_location_id',
            'price' => 'required|numeric|min:0',
        ]);

        DeliveryPrice::create($validated);

        return redirect()->route('admin.delivery-settings.index')
            ->with('success', __('Delivery price added successfully'));
    }

    public function destroyPrice(DeliveryPrice $price): RedirectResponse
    {
        $price->delete();

        return redirect()->route('admin.delivery-settings.index')
            ->with('success', __('Delivery price deleted successfully'));
    }
}
