<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
            'active' => true
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(99.99, $product->price);
        $this->assertTrue($product->active);
    }

    public function test_active_scope_returns_only_active_products()
    {
        Product::factory()->create(['active' => true]);
        Product::factory()->create(['active' => false]);

        $activeProducts = Product::active()->get();
        
        $this->assertEquals(1, $activeProducts->count());
        $this->assertTrue($activeProducts->first()->active);
    }

    public function test_search_scope_finds_products()
    {
        Product::factory()->create([
            'name' => 'iPhone 13',
            'brand' => 'Apple'
        ]);
        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'brand' => 'Samsung'
        ]);

        $results = Product::search('iPhone')->get();
        
        $this->assertEquals(1, $results->count());
        $this->assertEquals('iPhone 13', $results->first()->name);
    }

    public function test_caching_works()
    {
        $product = Product::factory()->create();
        
        // First call should hit the database
        $firstCall = Product::getCachedProduct($product->id);
        
        // Second call should use cache
        $secondCall = Product::getCachedProduct($product->id);
        
        $this->assertEquals($product->id, $firstCall->id);
        $this->assertEquals($product->id, $secondCall->id);
    }
} 