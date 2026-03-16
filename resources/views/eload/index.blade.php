<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Load Management - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">📱 E-Load Products</h1>
                <p class="text-purple-200 text-sm mt-1">Manage electronic load products</p>
            </div>
            <div class="flex items-center gap-4">
                @if(Auth::user()->role === 'super_admin')
                    <a href="{{ route('dashboard.superadmin') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Dashboard</a>
                @else
                    <a href="{{ route('dashboard.admin') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Dashboard</a>
                @endif
                <a href="{{ route('eload.categories.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Categories</a>
                <a href="{{ Auth::user()->isSuperAdmin() ? route('eload.add-load') : route('admin.eload.add-load') }}" class="px-3 py-2 bg-purple-600 hover:bg-purple-500 rounded">➕ Add Load</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Logout</button>
                </form>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">E-Load List</h2>
            <a href="{{ route('eload.create') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded-lg">➕ Add E-Load</a>
        </div>

        <!-- E-Load Table -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden">
            <table class="w-full">
                <thead class="bg-purple-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-purple-200">ID</th>
                        <th class="px-4 py-3 text-left text-purple-200">Name</th>
                        <th class="px-4 py-3 text-left text-purple-200">Network</th>
                        <th class="px-4 py-3 text-left text-purple-200">Category</th>
                        <th class="px-4 py-3 text-left text-purple-200">Price</th>
                        <th class="px-4 py-3 text-left text-purple-200">Status</th>
                        <th class="px-4 py-3 text-left text-purple-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/30">
                    @forelse($eloads as $eload)
                        <tr class="hover:bg-purple-900/20">
                            <td class="px-4 py-3">{{ $eload->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $eload->name }}</td>
                            <td class="px-4 py-3">
                                @if($eload->network === 'Smart')
                                    <span class="text-yellow-400">🟡 Smart</span>
                                @elseif($eload->network === 'Globe')
                                    <span class="text-green-400">🟢 Globe</span>
                                @else
                                    <span class="text-blue-400">🔵 DITO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $eload->category->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">₱{{ number_format($eload->price, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($eload->status === 'active')
                                    <span class="px-2 py-1 bg-green-900/50 text-green-400 rounded text-sm">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-900/50 text-red-400 rounded text-sm">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('eload.edit', $eload->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-500 rounded text-sm">Edit</a>
                                    @if(Auth::user()->isSuperAdmin())
                                        <form method="POST" action="{{ route('eload.destroy', $eload->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-500 rounded text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No E-Load products found. <a href="{{ route('eload.create') }}" class="text-purple-400 underline">Add one now</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(Auth::user()->isSuperAdmin())
        <!-- Super Admin Info -->
        <div class="mt-6 text-center text-sm text-purple-300">
            <p>🔐 You have full access as Super Admin (can delete)</p>
        </div>
        @endif
    </div>
</body>
</html>

