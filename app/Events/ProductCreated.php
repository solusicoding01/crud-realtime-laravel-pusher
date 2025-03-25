<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    // Constructor untuk menerima produk yang baru dibuat
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    // Menentukan channel yang digunakan untuk broadcasting
    public function broadcastOn()
    {
        return new Channel('products');  // Menggunakan channel publik 'products'
    }

    public function broadcastAs()
    {
        return 'ProductUpdated';

    }
    // Menentukan data yang akan dipancarkan
    public function broadcastWith()
    {
        return [
            'message' => "Produk baru telah ditambahkan: {$this->product->name}",
            'product' => $this->product,
        ];
    }
}
