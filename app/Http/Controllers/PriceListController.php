<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    /**
     * Show the price list.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('price-list.index');
    }

    /**
     * Get the price list data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $query      = PriceList::query();
        $total_data = $query->count();

        // Handle searching.
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('number', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('bottle_size', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->orWhere('price_gbp', 'LIKE', "%{$search}%")
                ->orWhere('order_amount', 'LIKE', "%{$search}%")
                ->orWhere('updated_at', 'LIKE', "%{$search}%");
            });
        }

        $total_filtered = $query->count();

        // Handle sorting.
        if ($request->order) {
            $columns = ['number', 'name', 'bottle_size', 'price', 'price_gbp', 'order_amount', 'updated_at'];
            $columnIndex = $request->order[0]['column'];
            $query->orderBy($columns[$columnIndex], $request->order[0]['dir']);
        }

        // Handle pagination.
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $data = $query->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($total_data),
            'recordsFiltered' => intval($total_filtered),
            'data' => $data
        ]);
    }
}
