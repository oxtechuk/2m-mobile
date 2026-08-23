{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Home -->
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Categories -->
    @foreach($categories as $category)
        <url>
            <loc>{{ route('search', ['category' => $category->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    <!-- Age Groups -->
    @foreach($ageGroups as $age)
        <url>
            <loc>{{ route('search', ['age' => $age->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    <!-- Needs -->
    @foreach($needs as $need)
        <url>
            <loc>{{ route('search', ['need' => $need->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    <!-- Skills -->
    @foreach($skills as $skill)
        <url>
            <loc>{{ route('search', ['skill' => $skill->slug]) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    <!-- Products -->
    @foreach($products as $product)
        <url>
            <loc>{{ route('product', $product->slug) }}</loc>
            <lastmod>{{ $product->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach
</urlset>
