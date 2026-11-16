<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponses;

    public function index()
    {
        return $this->ok('Categories retrieved successfully', Category::paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return $this->ok('Category created successfully', Category::create($validated));
    }

    public function show(Category $category)
    {
        return $this->ok('Category retrieved successfully', $category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return $this->ok('Category updated successfully', $category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $this->ok('Category deleted successfully');
    }
}
