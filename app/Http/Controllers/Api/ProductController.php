<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List every product with its category.
     */
    public function index(): JsonResponse
    {
        $products = Product::query()->with('category:id,name')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'List of products',
            'data' => $products->map(fn (Product $product) => $this->detail($product))->all(),
        ]);
    }

    /**
     * Store a new product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::create($validated);
        $product->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Create product success',
            'data' => $this->detail($product),
        ]);
    }

    /**
     * Show a single product.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with('category:id,name')->find($id);

        if ($product === null) {
            return $this->notFound('Product not found');
        }

        return response()->json([
            'success' => true,
            'message' => 'Get product by ID',
            'data' => [$this->detail($product)],
        ]);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if ($product === null) {
            return $this->notFound('Product not found');
        }

        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
        ]);

        $product->update($validated);
        $product->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $this->detail($product),
        ]);
    }

    /**
     * Delete a product.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if ($product === null) {
            return $this->notFound('Product not found');
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function detail(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category' => $product->category === null ? null : [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ],
        ];
    }
}
