<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\AgeGroup;
use App\Models\Skill;
use App\Models\Need;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    // Homepage
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $ageGroups = AgeGroup::all();
        $skills = Skill::all();
        $needs = Need::all();

        // Get latest 10 products
        $newArrivals = Product::where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        // Get best sellers (marked with badge or high price for demonstration)
        $bestSellers = Product::where('is_active', true)
            ->where('badge', 'الأكثر مبيعاً')
            ->take(10)
            ->get();

        // Fallback if no specific bestseller badge exists
        if ($bestSellers->isEmpty()) {
            $bestSellers = Product::where('is_active', true)->take(10)->get();
        }

        // Get approved reviews
        $reviews = Review::where('is_approved', true)->latest()->take(6)->get();

        return view('storefront.index', compact(
            'categories',
            'ageGroups',
            'skills',
            'needs',
            'newArrivals',
            'bestSellers',
            'reviews'
        ));
    }

    // Product Detail Page
    public function product(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Fetch related products (same category or needs)
        $relatedProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'relatedProducts'));
    }

    // Catalog search and filter page
    public function search(Request $request)
    {
        $query = Product::where('is_active', true);

        // Keywords
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhereHas('skills', function($skillQ) use ($q) {
                        $skillQ->where('name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('needs', function($needQ) use ($q) {
                        $needQ->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Filter by Age
        if ($request->filled('age')) {
            $query->whereHas('ageGroups', function($q) use ($request) {
                $q->where('slug', $request->input('age'));
            });
        }

        // Filter by Need
        if ($request->filled('need')) {
            $query->whereHas('needs', function($q) use ($request) {
                $q->where('slug', $request->input('need'));
            });
        }

        // Filter by Skill
        if ($request->filled('skill')) {
            $query->whereHas('skills', function($q) use ($request) {
                $q->where('slug', $request->input('skill'));
            });
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $ageGroups = AgeGroup::all();
        $skills = Skill::all();
        $needs = Need::all();

        return view('storefront.search', compact(
            'products',
            'categories',
            'ageGroups',
            'skills',
            'needs'
        ));
    }

    // Dynamic XML Sitemap
    public function sitemap()
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::where('is_active', true)->get();
        $ageGroups = AgeGroup::all();
        $skills = Skill::all();
        $needs = Need::all();

        return response()->view('storefront.sitemap', compact(
            'products',
            'categories',
            'ageGroups',
            'skills',
            'needs'
        ))->header('Content-Type', 'text/xml');
    }
}
