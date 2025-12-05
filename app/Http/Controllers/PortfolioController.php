<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request, $category = null)
    {
        // Sample portfolio data - nanti bisa diganti dengan database
        $portfolios = [
            [
                'id' => 1,
                'title' => 'Kanopi Carport Minimalis',
                'category' => 'Kanopi',
                'image' => 'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?w=800',
                'location' => 'Jakarta Selatan',
                'year' => '2024',
                'material' => 'Stainless Steel + Polycarbonate',
                'size' => '15 m²'
            ],
            [
                'id' => 2,
                'title' => 'Pagar Minimalis Modern',
                'category' => 'Pagar',
                'image' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=800',
                'location' => 'Tangerang',
                'year' => '2024',
                'material' => 'Stainless Steel',
                'size' => '27 m²'
            ],
            [
                'id' => 3,
                'title' => 'Railing Tangga Industrial',
                'category' => 'Railing',
                'image' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=800',
                'location' => 'BSD City',
                'year' => '2024',
                'material' => 'Hollow Galvanis + Kaca',
                'size' => '12 m'
            ],
            [
                'id' => 4,
                'title' => 'Tralis Jendela Klasik',
                'category' => 'Tralis',
                'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800',
                'location' => 'Bekasi',
                'year' => '2023',
                'material' => 'Hollow Galvanis',
                'size' => '8 m²'
            ],
            [
                'id' => 5,
                'title' => 'Kanopi Teras Rumah',
                'category' => 'Kanopi',
                'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800',
                'location' => 'Depok',
                'year' => '2024',
                'material' => 'Baja Ringan + Spandek',
                'size' => '20 m²'
            ],
            [
                'id' => 6,
                'title' => 'Balkon Minimalis',
                'category' => 'Balkon',
                'image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=800',
                'location' => 'Jakarta Pusat',
                'year' => '2024',
                'material' => 'Stainless Steel + Kaca Tempered',
                'size' => '6 m²'
            ],
            [
                'id' => 7,
                'title' => 'Custom Gerbang Rumah',
                'category' => 'Custom',
                'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800',
                'location' => 'Bogor',
                'year' => '2023',
                'material' => 'Besi Tempa + Cat Powder Coating',
                'size' => '10 m²'
            ],
            [
                'id' => 8,
                'title' => 'Pagar Minimalis Abu-Abu',
                'category' => 'Pagar',
                'image' => 'https://images.unsplash.com/photo-1600607687644-aac4c3eac7f4?w=800',
                'location' => 'Serpong',
                'year' => '2024',
                'material' => 'Hollow Galvanis',
                'size' => '18 m²'
            ],
            [
                'id' => 9,
                'title' => 'Railing Balkon Modern',
                'category' => 'Railing',
                'image' => 'https://images.unsplash.com/photo-1600566753151-384129cf4e3e?w=800',
                'location' => 'Cikarang',
                'year' => '2023',
                'material' => 'Stainless Steel',
                'size' => '8 m'
            ],
        ];

        // Get search query
        $search = $request->input('search');

        // Filter by search if provided
        if ($search) {
            $portfolios = array_filter($portfolios, function($item) use ($search) {
                $searchLower = strtolower($search);
                return 
                    str_contains(strtolower($item['title']), $searchLower) ||
                    str_contains(strtolower($item['category']), $searchLower) ||
                    str_contains(strtolower($item['location']), $searchLower) ||
                    str_contains(strtolower($item['material']), $searchLower);
            });
        }

        // Filter by category if provided
        if ($category && $category !== 'semua') {
            $portfolios = array_filter($portfolios, function($item) use ($category) {
                return strtolower($item['category']) === strtolower($category);
            });
        }

        $categories = ['Semua', 'Kanopi', 'Pagar', 'Railing', 'Tralis', 'Balkon', 'Custom'];

        return view('portfolio.index', compact('portfolios', 'categories', 'category'));
    }
}
