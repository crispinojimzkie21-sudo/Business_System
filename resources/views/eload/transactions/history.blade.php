<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction History - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">📜 Load Transaction History</h1>
                <p class="text-purple-200 text-sm mt-1">View all electronic load transactions</p>
            </div>
            <div class="flex items-center gap-4">
                @if(Auth::user()->role === 'super_admin')
                    <a href="{{ route('dashboard.superadmin') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Back to Dashboard</a>
                @else
                    <a href="{{ route('dashboard.admin') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Back to Dashboard</a>
                @endif
                <a href="{{ route('eload.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">E-Load Products</a>
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

        <!-- Filters -->
        <form method="GET" action="{{ Auth::user()->isSuperAdmin() ? route('eload.transactions.history') : route('admin.eload.transactions.history') }}" class="bg-black/60 p-4 rounded-lg border border-purple-900/30 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-purple-200 mb-2 text-sm">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500">
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
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="not_completed" {{ request('status') === 'not_completed' ? 'selected' : '' }}>Not Completed</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded">Filter</button>
                    <a href="{{ Auth::user()->isSuperAdmin() ? route('eload.transactions.history') : route('admin.eload.transactions.history') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">Clear</a>
                </div>
            </div>
        </form>

        <!-- Transactions Table -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden">
            <table class="w-full">
                <thead class="bg-purple-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-purple-200">ID</th>
                        <th class="px-4 py-3 text-left text-purple-200">Transaction ID</th>
                        <th class="px-4 py-3 text-left text-purple-200">Load Name</th>
                        <th class="px-4 py-3 text-left text-purple-200">Network</th>
                        <th class="px-4 py-3 text-left text-purple-200">Recipient Number</th>
                        <th class="px-4 py-3 text-left text-purple-200">Gateway Number</th>
                        <th class="px-4 py-3 text-left text-purple-200">Price</th>
                        <th class="px-4 py-3 text-left text-purple-200">Status</th>
                        <th class="px-4 py-3 text-left text-purple-200">Processed By</th>
                        <th class="px-4 py-3 text-left text-purple-200">Date & Time</th>
                        <th class="px-4 py-3 text-left text-purple-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/30">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-purple-900/20">
                            <td class="px-4 py-3">{{ $transaction->id }}</td>
                            <td class="px-4 py-3 font-mono text-sm">{{ $transaction->transaction_id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $transaction->eload->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                @if($transaction->eload && $transaction->eload->network === 'Smart')
                                    <span class="text-yellow-400">🟡 Smart</span>
                                @elseif($transaction->eload && $transaction->eload->network === 'Globe')
                                    <span class="text-green-400">🟢 Globe</span>
                                @else
                                    <span class="text-blue-400">🔵 DITO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $transaction->eload_number }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $transaction->eloadNumber->number ?? 'N/A' }}</td>
                            <td class="px-4 py-3">₱{{ number_format($transaction->price, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($transaction->status === 'completed')
                                    <span class="px-2 py-1 bg-green-900/50 text-green-400 rounded text-sm">Completed</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-900/50 text-yellow-400 rounded text-sm">Not Completed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $transaction->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $transaction->created_at ? $transaction->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('eload.transactions.update-status', $transaction->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 bg-gray-800 border border-purple-900/30 rounded text-sm text-white focus:outline-none">
                                        <option value="not_completed" {{ $transaction->status === 'not_completed' ? 'selected' : '' }}>Not Completed</option>
                                        <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-gray-400">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($transactions) > 0)
        <!-- Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-black/60 p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-200 text-sm">Total Transactions</h3>
                <p class="text-2xl font-bold">{{ count($transactions) }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-200 text-sm">Completed</h3>
                <p class="text-2xl font-bold text-green-400">{{ $transactions->where('status', 'completed')->count() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-200 text-sm">Total Amount</h3>
                <p class="text-2xl font-bold">₱{{ number_format($transactions->sum('price'), 2) }}</p>
            </div>
        </div>
        @endif
    </div>
</body>
</html>

