<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * List every category.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'List of categories',
            'data' => Category::query()->orderBy('id')->get(),
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Create form success',
            'data' => $this->detail($category),
        ]);
    }

    /**
     * Show a single category.
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::find($id);

        if ($category === null) {
            return $this->notFound('Category not found');
        }

        return response()->json([
            'success' => true,
            'message' => 'Get category by ID',
            'data' => [$category],
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);

        if ($category === null) {
            return $this->notFound('Category not found');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);

        if ($category === null) {
            return $this->notFound('Category not found');
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function detail(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ];
    }
}
