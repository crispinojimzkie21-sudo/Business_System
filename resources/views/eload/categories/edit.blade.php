<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Category - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-2xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">✏️ Edit Category</h1>
                <p class="text-purple-200 text-sm mt-1">Update E-Load category</p>
            </div>
            <a href="{{ route('eload.categories.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Back</a>
        </header>

        @if ($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <ul class="list-disc list-inside text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('eload.categories.update', $category->id) }}" class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Category Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="e.g., Mobile Load, Data Load, Gaming Load" required>
            </div>

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="Optional description">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-purple-200 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                    <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-500 rounded">Update Category</button>
                <a href="{{ route('eload.categories.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

