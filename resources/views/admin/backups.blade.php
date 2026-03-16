<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup Management - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-400">🗄️ Backup Management</h1>
                <p class="text-red-200 text-sm mt-1">View and manage database backups</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-red-200 hover:bg-red-900/30 rounded">← Dashboard</a>
                <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-medium">
                        💾 Create New Backup
                    </button>
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

        <!-- Backup Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-black/60 p-4 rounded-lg border border-red-900/30">
                <h3 class="text-red-400 font-semibold">Total Backups</h3>
                <p class="text-2xl font-bold">{{ count($backups) }}</p>
                <p class="text-xs text-red-200">Database files</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-red-900/30">
                <h3 class="text-red-400 font-semibold">Latest Backup</h3>
                @if(!empty($backups))
                    <p class="text-sm">{{ $backups[0]['date']->format('M d, Y h:i A') }}</p>
                    <p class="text-xs text-red-200">{{ $backups[0]['size'] }}</p>
                @else
                    <p class="text-sm text-yellow-400">No backups</p>
                    <p class="text-xs text-red-200">Create one now</p>
                @endif
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-red-900/30">
                <h3 class="text-red-400 font-semibold">Auto-Backup</h3>
                <p class="text-sm text-green-400">✅ Every 6 hours</p>
                <p class="text-xs text-red-200">Scheduled</p>
            </div>
        </div>

        <!-- Backup List -->
        <div class="bg-black/60 rounded-lg border border-red-900/30 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-red-400">📋 Backup History</h2>
                
                @if(!empty($backups))
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-red-900/30">
                                    <th class="text-left py-3 px-4 text-red-300">Date & Time</th>
                                    <th class="text-left py-3 px-4 text-red-300">File Name</th>
                                    <th class="text-left py-3 px-4 text-red-300">Size</th>
                                    <th class="text-left py-3 px-4 text-red-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr class="border-b border-red-900/20 hover:bg-red-900/10">
                                        <td class="py-3 px-4">
                                            <div>
                                                <p class="text-sm font-medium">{{ $backup['date']->format('M d, Y') }}</p>
                                                <p class="text-xs text-gray-400">{{ $backup['date']->format('h:i A') }}</p>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <code class="text-xs bg-gray-800 px-2 py-1 rounded">{{ $backup['filename'] }}</code>
                                        </td>
                                        <td class="py-3 px-4 text-sm">{{ $backup['size'] }}</td>
                                        <td class="py-3 px-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm"
                                                   title="Download backup">
                                                    📥 Download
                                                </a>
                                                <form action="{{ route('admin.backup.delete', $backup['filename']) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this backup? This action cannot be undone.')"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm"
                                                            title="Delete backup">
                                                        🗑️ Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🗄️</div>
                        <h3 class="text-xl font-semibold text-red-300 mb-2">No Backups Found</h3>
                        <p class="text-gray-400 mb-6">Create your first backup to protect your data</p>
                        <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                                💾 Create First Backup
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Backup Information -->
        <div class="mt-8 bg-black/60 p-6 rounded-lg border border-red-900/30">
            <h3 class="text-lg font-semibold mb-3 text-red-400">ℹ️ Backup Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-red-300 font-medium mb-2">What gets backed up:</p>
                    <ul class="text-gray-300 space-y-1">
                        <li>• Complete database (SQLite file)</li>
                        <li>• All user accounts and data</li>
                        <li>• Products and inventory</li>
                        <li>• Sales and transactions</li>
                        <li>• Attendance records</li>
                    </ul>
                </div>
                <div>
                    <p class="text-red-300 font-medium mb-2">Backup Schedule:</p>
                    <ul class="text-gray-300 space-y-1">
                        <li>• Automatic: Every 6 hours</li>
                        <li>• Manual: On-demand via dashboard</li>
                        <li>• Retention: Last 10 backups</li>
                        <li>• Location: storage/backups/</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
