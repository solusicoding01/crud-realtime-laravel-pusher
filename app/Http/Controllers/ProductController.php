<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ProductDeleted;
use App\Notifications\ProductNotification;
use Illuminate\Support\Facades\Notification;


class ProductController extends Controller
{
    public function showProducts()
    {
        $products = Product::all();
        return view('products', compact('products'));
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());

        broadcast(new ProductUpdated($product));
        return response()->json($product);

    }

// Setelah update produk
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
    ]);

    // Siarkan event setelah produk diperbarui
    broadcast(new ProductUpdated($product));

    return response()->json(['success' => true]);
}

    public function destroy($id)
    {
    // Temukan produk berdasarkan ID
    $product = Product::findOrFail($id);

    // Hapus produk
    $product->delete();

    // Kirim event ProductDeleted setelah penghapusan
    broadcast(new ProductDeleted($product));

    return response()->json(['success' => true]);
    }
}
