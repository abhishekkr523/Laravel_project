<?php

namespace App\Http\Controllers;

use App\Models\product_condition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $productConditions = product_condition::with('products')->get();
        return response()->json($productConditions);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_condition' => 'required|string|max:255',
        ]);

        $productCondition = Product_condition::create($validated);

        return response()->json($productCondition, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(product_condition $product_condition): JsonResponse
    {
        return response()->json($product_condition);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product_condition $product_condition): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, product_condition $product_condition): JsonResponse
    {

        // return response()->json($request);
        $validated = $request->validate([
            'product_condition' => 'required|max:255',
        ]);

        $product_condition->update($validated);

        return response()->json($product_condition);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(product_condition $product_condition): JsonResponse
    {
        $product_condition->delete();
        return response()->json(null, 204);
    }



}
