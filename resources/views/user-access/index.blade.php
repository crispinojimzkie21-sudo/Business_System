<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Control - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-purple-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">🔐 User Access Control</h1>
                <p class="text-purple-200 text-sm mt-1">Manage Admin and Employee system access permissions</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Logout</button>
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/60 p-4 rounded-lg border border-purple-900/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-200 text-sm">Total Users</p>
                        <p class="text-2xl font-bold text-purple-400">{{ $totalUsers }}</p>
                    </div>
                    <div class="text-purple-400 text-2xl">👥</div>
                </div>
            </div>
            
            <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-200 text-sm">Enabled Access</p>
                        <p class="text-2xl font-bold text-green-400">{{ $enabledUsers }}</p>
                    </div>
                    <div class="text-green-400 text-2xl">✅</div>
                </div>
            </div>
            
            <div class="bg-black/60 p-4 rounded-lg border border-red-900/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm">Disabled Access</p>
                        <p class="text-2xl font-bold text-red-400">{{ $disabledUsers }}</p>
                    </div>
                    <div class="text-red-400 text-2xl">🚫</div>
                </div>
            </div>
            
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm">Admin Users</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $adminUsers->count() }}</p>
                    </div>
                    <div class="text-blue-400 text-2xl">👨‍💼</div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 p-4 mb-6">
            <h3 class="text-lg font-semibold text-purple-400 mb-4">Bulk Actions</h3>
            <form method="POST" action="{{ route('user-access.bulk-update') }}" class="flex flex-wrap gap-4 items-end">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-purple-200 mb-2">Select Users</label>
                    <select name="user_ids[]" multiple class="w-full bg-black/40 border border-purple-700 rounded px-3 py-2 text-white" size="3">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-purple-300 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-sm text-purple-200 mb-2">Action</label>
                    <select name="action" class="w-full bg-black/40 border border-purple-700 rounded px-3 py-2 text-white">
                        <option value="enable">Enable Access</option>
                        <option value="disable">Disable Access</option>
                    </select>
                </div>
                <div class="min-w-[200px]" id="reasonField">
                    <label class="block text-sm text-purple-200 mb-2">Reason (if disabling)</label>
                    <input type="text" name="reason" placeholder="Enter reason..." class="w-full bg-black/40 border border-purple-700 rounded px-3 py-2 text-white">
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded transition-colors">
                    Apply Action
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-purple-400 mb-4">User Access Status</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-purple-900/30">
                                <th class="text-left py-3 px-4 text-purple-300">User</th>
                                <th class="text-left py-3 px-4 text-purple-300">Role</th>
                                <th class="text-left py-3 px-4 text-purple-300">Status</th>
                                <th class="text-left py-3 px-4 text-purple-300">Last Changed</th>
                                <th class="text-left py-3 px-4 text-purple-300">Reason</th>
                                <th class="text-center py-3 px-4 text-purple-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-b border-purple-900/20 hover:bg-purple-900/10">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-sm font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">{{ $user->name }}</p>
                                                <p class="text-purple-300 text-sm">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($user->role === 'admin') bg-blue-900/50 text-blue-300
                                            @elseif($user->role === 'employee') bg-green-900/50 text-green-300
                                            @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($user->access_status === 'enabled')
                                            <span class="flex items-center gap-2 text-green-400">
                                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                                Enabled
                                            </span>
                                        @else
                                            <span class="flex items-center gap-2 text-red-400">
                                                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                                Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-purple-300 text-sm">
                                        @if($user->access_status === 'disabled' && $user->access_disabled_at)
                                            {{ $user->access_disabled_at->diffForHumans() }}
                                        @elseif($user->access_status === 'enabled' && $user->access_enabled_at)
                                            {{ $user->access_enabled_at->diffForHumans() }}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($user->access_restriction_reason)
                                            <span class="text-red-300 text-sm">{{ $user->access_restriction_reason }}</span>
                                        @else
                                            <span class="text-purple-300 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($user->access_status === 'enabled')
                                                <button onclick="disableAccess({{ $user->id }}, '{{ $user->name }}')" 
                                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                                    Disable
                                                </button>
                                            @else
                                                <button onclick="enableAccess({{ $user->id }}, '{{ $user->name }}')" 
                                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors">
                                                    Enable
                                                </button>
                                            @endif
                                            <a href="{{ route('user-access.history', $user->id) }}" 
                                               class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded transition-colors">
                                                History
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Access Modal -->
    <div id="disableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-red-400 mb-4">Disable User Access</h3>
            <p class="text-gray-300 mb-4">
                Are you sure you want to disable access for <span id="disableUserName" class="font-bold"></span>?
                This will prevent them from logging into the system.
            </p>
            <form method="POST" id="disableForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-2">Reason for disabling access *</label>
                    <textarea name="reason" rows="3" required 
                              class="w-full bg-black/40 border border-gray-600 rounded px-3 py-2 text-white"
                              placeholder="Enter reason for disabling access..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded transition-colors">
                        Disable Access
                    </button>
                    <button type="button" onclick="closeDisableModal()" 
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 rounded transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Enable Access Modal -->
    <div id="enableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-green-400 mb-4">Enable User Access</h3>
            <p class="text-gray-300 mb-4">
                Are you sure you want to enable access for <span id="enableUserName" class="font-bold"></span>?
                This will allow them to log into the system again.
            </p>
            <form method="POST" id="enableForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded transition-colors">
                        Enable Access
                    </button>
                    <button type="button" onclick="closeEnableModal()" 
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 rounded transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show/hide reason field based on action
        document.querySelector('select[name="action"]').addEventListener('change', function() {
            const reasonField = document.getElementById('reasonField');
            if (this.value === 'disable') {
                reasonField.style.display = 'block';
                document.querySelector('input[name="reason"]').required = true;
            } else {
                reasonField.style.display = 'none';
                document.querySelector('input[name="reason"]').required = false;
            }
        });

        // Disable access modal
        function disableAccess(userId, userName) {
            document.getElementById('disableUserName').textContent = userName;
            document.getElementById('disableForm').action = '/user-access/disable/' + userId;
            document.getElementById('disableModal').classList.remove('hidden');
        }

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
            document.getElementById('disableForm').reset();
        }

        // Enable access modal
        function enableAccess(userId, userName) {
            document.getElementById('enableUserName').textContent = userName;
            document.getElementById('enableForm').action = '/user-access/enable/' + userId;
            document.getElementById('enableModal').classList.remove('hidden');
        }

        function closeEnableModal() {
            document.getElementById('enableModal').classList.add('hidden');
        }

        // Set correct form action URLs
        document.addEventListener('DOMContentLoaded', function() {
            const currentHost = window.location.protocol + '//' + window.location.host;
            
            // Fix all form actions
            const forms = document.querySelectorAll('form[action]');
            forms.forEach(form => {
                if (form.action.includes('/user-access/')) {
                    form.action = currentHost + form.getAttribute('action');
                }
            });
        });
    </script>
</body>
</html>
