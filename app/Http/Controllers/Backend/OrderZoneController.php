<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderZoneController extends Controller
{
    /**
     * Display the Order Zone page.
     */
    public function index(): View
    {
        $this->authorize('create', Order::class);

        return view('backend.pages.order-zone.index', [
            'breadcrumbs' => [
                'title' => __('Order Zone'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Order Zone'), 'url' => null],
                ],
            ],
        ]);
    }
}

