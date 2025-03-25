<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="{{ mix('/js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Real-Time Products</h1>
        <div class="mt-3">
            <form id="product-form">
                @csrf
                <input type="hidden" id="product-id">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" id="price" name="price" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
        <hr>
        <h2>Product List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="product-list">
                @foreach ($products as $product)
                    <tr id="product-{{ $product->id }}">
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editProduct({{ $product }})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct({{ $product->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Konfigurasi Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('products');

        // Listen for events
        channel.bind('ProductUpdated', function(data) {
            const product = data.product;
            const row = document.getElementById(`product-${product.id}`);
            if (row) {
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.description}</td>
                    <td>${product.price}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editProduct(${JSON.stringify(product)})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                `;
            } else {
                document.getElementById('product-list').insertAdjacentHTML('beforeend', `
                    <tr id="product-${product.id}">
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td>${product.description}</td>
                        <td>${product.price}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editProduct(${JSON.stringify(product)})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                        </td>
                    </tr>
                `);
            }
        });

        channel.bind('ProductDeleted', function(data) {
            const productId = data.productId;
            const row = document.getElementById(`product-${productId}`);
            if (row) {
                row.remove(); // Hapus baris produk dari tabel
            }
        });

        // Tambahkan Produk
        document.getElementById('product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('product-id').value;
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const price = document.getElementById('price').value;

            fetch(`/api/products${id ? '/' + id : ''}`, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify({ name, description, price })
            }).then(response => response.json())
              .then(data => {
                  document.getElementById('product-form').reset();
                  document.getElementById('product-id').value = '';
              });
        });

        // Edit Produk
        function editProduct(product) {
            document.getElementById('product-id').value = product.id;
            document.getElementById('name').value = product.name;
            document.getElementById('description').value = product.description;
            document.getElementById('price').value = product.price;
        }

// Hapus Produk
function deleteProduct(id) {
    fetch(`/api/products/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Product deleted successfully');
          }
      });
}

// Mendengarkan event 'ProductCreated' yang diterima dari server
channel.bind('ProductUpdated', function(data) {
    console.log(data); // Debug data yang diterima dari Pusher

        // Menampilkan Toast
        showToast(data.message);

    // Tampilkan detail produk jika diperlukan
    console.log('New product: ', data.product);
    });

    // Fungsi untuk menampilkan Toast dan memutar suara
    function showToast(message) {
        // Memutar suara notifikasi
        const audio = new Audio('/sounds/notification.wav');  // Ganti dengan path file suara Anda
        audio.play();

        // Membuat elemen toast HTML
        const toastElement = document.createElement('div');
        toastElement.classList.add('toast');
        toastElement.classList.add('align-items-center');
        toastElement.classList.add('text-white');
        toastElement.classList.add('bg-success');
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        toastElement.style.position = 'fixed';
        toastElement.style.top = '20px';
        toastElement.style.right = '20px';
        toastElement.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        // Menambahkan toast ke DOM
        document.body.appendChild(toastElement);

        // Membuat objek Toast baru dan menampilkan toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Hapus toast setelah beberapa detik
        setTimeout(() => {
            toastElement.remove();
        }, 5000);  // Hapus setelah 5 detik
    }
</script>
</body>
</html>
