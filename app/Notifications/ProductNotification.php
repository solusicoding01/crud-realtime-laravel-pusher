<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ProductNotification extends Notification
{
    // Data produk yang akan dikirimkan dalam notifikasi
    public $product;

    // Konstruktor untuk menerima objek produk
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    // Menentukan saluran notifikasi
    public function via($notifiable)
    {
        // Mengirimkan notifikasi melalui database dan broadcast (real-time)
        return ['database', 'broadcast'];
    }

    // Menentukan format notifikasi untuk database
    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'description' => $this->product->description,
            'price' => $this->product->price,
        ];
    }

    // Menentukan format notifikasi untuk broadcast (real-time)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'product' => $this->product,  // Mengirimkan seluruh objek produk
            'message' => 'A new product has been added!'  // Pesan tambahan
        ]);
    }

    // (Optional) Mengatur nama channel untuk broadcast
    public function broadcastOn()
    {
        return ['products']; // Channel untuk mengirimkan notifikasi
    }
}
