<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
@if (session('success'))
    <div 
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg transition-all"
    >
        {{ session('success') }}
    </div>
@endif
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto mt-8">
        @yield('content')
    </div>
</body>

</html>