<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductPaginationResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $per_page = $request->per_page ?? 10;
        $search = $request->search ?? "";
        $sort_field = $request->sort_field ?? "id";
        $sort_direction = $request->sort_direction ?? "asc";

        $paginate_products = Product::where('name', 'LIKE', "%$search%")->orderBy($sort_field, $sort_direction)->paginate($per_page);

        return ProductResource::collection($paginate_products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $productCreateRequest)
    {
        $data = $productCreateRequest->validated();

        $data['stock'] = $data['stock'] ?? 0;
        $data['price'] = $data['price'] ?? 0;
        $data['status'] = $data['status'] ?? 0;
        $data['created_by'] = auth()->user()->id;
        $data['updated_by'] = auth()->user()->id;

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResource
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $productUpdateRequest, Product $product)
    {
        $data = $productUpdateRequest->validated();

        $data['updated_by'] = auth()->user()->id;

        $product->update($data);

        return new ProductResource($product);
    }
}
