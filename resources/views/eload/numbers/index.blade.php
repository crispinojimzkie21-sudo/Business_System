<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Load Numbers - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">📡 E-Load Numbers</h1>
                <p class="text-purple-200 text-sm mt-1">Gateway numbers for sending load (View Only)</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/dashboard/super-admin') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Dashboard</a>
                <a href="{{ route('eload.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">E-Load Products</a>
                <a href="{{ route('eload.categories.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Categories</a>
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

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('eload.numbers.index') }}" class="bg-black/60 p-4 rounded-lg border border-purple-900/30 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-purple-200 mb-2 text-sm">Search Number</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="Search number...">
                </div>
                <div>
                    <label class="block text-purple-200 mb-2 text-sm">Network</label>
                    <select name="network" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500">
                        <option value="">All Networks</option>
                        @foreach($networks as $network)
                            <option value="{{ $network }}" {{ request('network') === $network ? 'selected' : '' }}>{{ $network }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-purple-200 mb-2 text-sm">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded">Search</button>
                    <a href="{{ route('eload.numbers.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">Clear</a>
                </div>
            </div>
        </form>

        <!-- Info -->
        <div class="mb-6 bg-yellow-900/30 border border-yellow-700 rounded-md p-4">
            <p class="text-yellow-200">🔒 View Only - Super Admin cannot add, edit, or delete E-Load numbers.</p>
        </div>

        <!-- E-Load Numbers Table -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden">
            <table class="w-full">
                <thead class="bg-purple-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-purple-200">ID</th>
                        <th class="px-4 py-3 text-left text-purple-200">E-Load Number</th>
                        <th class="px-4 py-3 text-left text-purple-200">Network</th>
                        <th class="px-4 py-3 text-left text-purple-200">Description</th>
                        <th class="px-4 py-3 text-left text-purple-200">Status</th>
                        <th class="px-4 py-3 text-left text-purple-200">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/30">
                    @forelse($eloadNumbers as $number)
                        <tr class="hover:bg-purple-900/20">
                            <td class="px-4 py-3">{{ $number->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $number->number }}</td>
                            <td class="px-4 py-3">
                                @if($number->network === 'Smart')
                                    <span class="text-yellow-400">🟡 Smart</span>
                                @elseif($number->network === 'Globe')
                                    <span class="text-green-400">🟢 Globe</span>
                                @else
                                    <span class="text-blue-400">🔵 DITO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $number->description ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                @if($number->status === 'active')
                                    <span class="px-2 py-1 bg-green-900/50 text-green-400 rounded text-sm">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-900/50 text-red-400 rounded text-sm">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $number->created_at ? $number->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">No E-Load numbers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

