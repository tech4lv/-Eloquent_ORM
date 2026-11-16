<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $products = Product::with('category')
            ->paginate(10);

        return $this->ok('Products retrieved successfully',
            ProductResource::collection($products));
    }

    public function show(Product $product)
    {
        $product->load('category');

        return $this->ok('Product retrieved successfully', new ProductResource($product));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validated);
        $product->load('category');

        return $this->ok('Product created successfully', new ProductResource($product));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $product->update($validated);
        $product->load('category');

        return $this->ok('Product updated successfully', new ProductResource($product));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->ok('Product deleted successfully');
    }

    public function stats()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_units_in_stock' => Product::count(),
            'total_units_ordered' => DB::table('order_products')->sum('quantity'),
            'inventory_value' => Product::sum('price'),
            'low_stock_count' => Product::whereDoesntHave('orders')->count(),
            'recent_products' => Product::latest()->limit(5)->get(['id', 'name', 'price', 'created_at']),
        ];

        return $this->ok('Product stats retrieved successfully', $stats);
    }

    public function queries()
    {
        // =========================================================
        // A. QUERYING TABLES
        // =========================================================
        // Get the name and price columns from the table.
        $nameAndPrice = Product::select('name', 'price')->get();

        // Load products with their category (avoids N+1 queries).
        $productsWithCategory = Product::with('category')->get();

        // Get the product id and name, ordered by price ascending.
        $productsOrderedByPrice = Product::select('id', 'name')
            ->orderBy('price', 'asc')
            ->get();

        // Get the first 5 rows from the products table.
        $firstFiveProducts = Product::limit(5)->get();

        // =========================================================
        // B. FILTERING DATA
        // =========================================================
        // Get products with a price more than or equal to 3.
        $priceGreaterThanOrEqual = Product::where('price', '>=', 3)->get();

        // Get products in the 'Electronics' category.
        $electronics = Product::whereHas('category', function ($q) {
            $q->where('name', 'Electronics');
        })->get();

        // =========================================================
        // C. AGGREGATING DATA
        // =========================================================
        // Get the total price across all products.
        $totalPrice = Product::sum('price');

        // Get the average price per product.
        $averagePrice = Product::avg('price');

        // Get the product with the highest price.
        $highestPrice = Product::max('price');

        // Get the product with the lowest price.
        $lowestPrice = Product::min('price');

        // =========================================================
        // D. GROUPING, FILTERING, AND SORTING
        // =========================================================
        // Get the total price of products for each category.
        $totalPricePerCategory = Product::join(
            'categories',
            'products.category_id',
            '=',
            'categories.id'
        )
            ->select(
                'categories.name',
                DB::raw('SUM(products.price) as total_price')
            )
            ->groupBy('categories.name')
            ->get();

        // Get the average price for each category.
        $averagePricePerCategory = Product::join(
            'categories',
            'products.category_id',
            '=',
            'categories.id'
        )
            ->select(
                'categories.name',
                DB::raw('AVG(products.price) as avg_price')
            )
            ->groupBy('categories.name')
            ->get();

        // Get the product with the lowest price per category.
        $lowestPricePerCategory = Product::join(
            'categories',
            'products.category_id',
            '=',
            'categories.id'
        )
            ->select(
                'categories.name',
                DB::raw('MIN(products.price) as min_price')
            )
            ->groupBy('categories.name')
            ->get();

        // Get the average price per category, sorted ascending.
        $averagePriceSorted = Product::join(
            'categories',
            'products.category_id',
            '=',
            'categories.id'
        )
            ->select(
                'categories.name',
                DB::raw('AVG(products.price) as avg_price')
            )
            ->groupBy('categories.name')
            ->orderBy('avg_price', 'asc')
            ->get();

        // Get the average price for 'Electronics' and 'Books' categories.
        $electronicsAndBooks = Product::join(
            'categories',
            'products.category_id',
            '=',
            'categories.id'
        )
            ->whereIn('categories.name', ['Electronics', 'Books'])
            ->select(
                'categories.name',
                DB::raw('AVG(products.price) as avg_price')
            )
            ->groupBy('categories.name')
            ->get();

        // =========================================================
        // RETURN ALL RESULTS AS JSON
        // =========================================================
        $queryResults = [
            'name_and_price' => $nameAndPrice,
            'products_with_category' => $productsWithCategory,
            'products_ordered_by_price' => $productsOrderedByPrice,
            'first_five_products' => $firstFiveProducts,
            'price_greater_than_or_equal' => $priceGreaterThanOrEqual,
            'electronics' => $electronics,
            'total_price' => $totalPrice,
            'average_price' => $averagePrice,
            'highest_price' => $highestPrice,
            'lowest_price' => $lowestPrice,
            'total_price_per_category' => $totalPricePerCategory,
            'average_price_per_category' => $averagePricePerCategory,
            'lowest_price_per_category' => $lowestPricePerCategory,
            'average_price_sorted' => $averagePriceSorted,
            'electronics_and_books' => $electronicsAndBooks,
        ];

        // Use the APIResponse Traits here.
        return $this->success('Query results retrieved successfully', $queryResults);
    }
}
