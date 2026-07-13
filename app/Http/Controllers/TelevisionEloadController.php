<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EloadTransaction;
use App\Models\User;
use Carbon\Carbon;

class TelevisionEloadController extends Controller
{
    /**
     * Display the Television E-Load dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get recent TV E-Load transactions
        $transactions = EloadTransaction::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get statistics
        $todayTransactions = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();
        
        $todayAmount = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('price');
        
        $totalTransactions = EloadTransaction::where('status', 'completed')->count();
        $totalAmount = EloadTransaction::where('status', 'completed')->sum('price');
        
        // TV/Satellite providers
        $providers = [
            'Cignal' => ['color' => 'red', 'description' => 'Direct-to-Home Satellite TV'],
            'GMA Pinoy TV' => ['color' => 'blue', 'description' => 'International Filipino Channel'],
            'Kapamilya Channel' => ['color' => 'green', 'description' => 'ABS-CBN International'],
            'Dream Satellite' => ['color' => 'purple', 'description' => 'Satellite TV Service'],
            'Satlite' => ['color' => 'orange', 'description' => 'Affordable Satellite TV'],
            'Sky Direct' => ['color' => 'indigo', 'description' => 'Direct-to-Home Service'],
            'GSAT' => ['color' => 'pink', 'description' => 'Global Satellite TV'],
            'Cignal Play' => ['color' => 'yellow', 'description' => 'Digital Streaming Service'],
        ];
        
        // Predefined load amounts
        $loadAmounts = [50, 100, 150, 200, 300, 500, 750, 1000, 1500, 2000];
        
        return view('television-eload.dashboard', compact(
            'user',
            'transactions',
            'todayTransactions',
            'todayAmount',
            'totalTransactions',
            'totalAmount',
            'providers',
            'loadAmounts'
        ));
    }
    
    /**
     * Display the Admin Television E-Load dashboard
     */
    public function adminDashboard()
    {
        $user = auth()->user();
        
        // Get recent TV E-Load transactions
        $transactions = EloadTransaction::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get statistics - include all transactions regardless of type for TV E-Load
        $todayTransactions = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();
        
        $todayAmount = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('price');
        
        $totalTransactions = EloadTransaction::where('status', 'completed')->count();
        $totalAmount = EloadTransaction::where('status', 'completed')->sum('price');
        
        // Debug: Log the counts to see what's happening
        \Log::info('Today TV E-Load Stats: ' . $todayTransactions . ' transactions, ₱' . $todayAmount);
        \Log::info('Today is: ' . Carbon::today()->toDateString());
        
        // TV/Satellite providers
        $providers = [
            'Cignal' => ['color' => 'red', 'description' => 'Direct-to-Home Satellite TV'],
            'GMA Pinoy TV' => ['color' => 'blue', 'description' => 'International Filipino Channel'],
            'Kapamilya Channel' => ['color' => 'green', 'description' => 'ABS-CBN International'],
            'Dream Satellite' => ['color' => 'purple', 'description' => 'Satellite TV Service'],
            'Satlite' => ['color' => 'orange', 'description' => 'Affordable Satellite TV'],
            'Sky Direct' => ['color' => 'indigo', 'description' => 'Direct-to-Home Service'],
            'GSAT' => ['color' => 'pink', 'description' => 'Global Satellite TV'],
            'Cignal Play' => ['color' => 'yellow', 'description' => 'Digital Streaming Service'],
        ];
        
        // Predefined load amounts
        $loadAmounts = [50, 100, 150, 200, 300, 500, 750, 1000, 1500, 2000];
        
        return view('admin.tv-eload', compact(
            'user',
            'transactions',
            'todayTransactions',
            'todayAmount',
            'totalTransactions',
            'totalAmount',
            'providers',
            'loadAmounts'
        ));
    }
    
    /**
     * Get real-time TV E-Load statistics
     */
    public function getStats()
    {
        // Get statistics
        $todayTransactions = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();
        
        $todayAmount = EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('price');
        
        $totalTransactions = EloadTransaction::where('status', 'completed')->count();
        $totalAmount = EloadTransaction::where('status', 'completed')->sum('price');
        
        return response()->json([
            'todayTransactions' => $todayTransactions,
            'todayAmount' => $todayAmount,
            'totalTransactions' => $totalTransactions,
            'totalAmount' => $totalAmount
        ]);
    }
    
    /**
     * Process Television E-Load transaction (Manual Record Addition Only)
     */
    public function processLoad(Request $request)
    {
        try {
            // Simple validation
            $request->validate([
                'account_number' => 'required|string|min:1|max:50',
                'provider' => 'required|string',
                'amount' => 'required|numeric|min:50|max:10000',
                'customer_name' => 'nullable|string|max:100',
                'customer_mobile' => 'nullable|string|max:20',
            ]);
            
            // Try to use existing eload_id and eload_number_id, or create fallback values
            $existingTransaction = \DB::table('eload_transactions')
                ->where('status', 'completed')
                ->first();
            
            // Set fallback values if no existing transaction found
            $eloadId = $existingTransaction->eload_id ?? 1;
            $eloadNumberId = $existingTransaction->eload_number_id ?? 19; // Use valid eload_number_id
            
            // Create TV E-Load record
            $transaction = EloadTransaction::create([
                'eload_id' => $eloadId,
                'eload_number_id' => $eloadNumberId,
                'user_id' => auth()->id() ?: 8, // Use authenticated user ID or fallback to super admin (ID 8)
                'eload_number' => trim($request->account_number), // TV account number
                'price' => $request->amount, // TV load amount
                'original_price' => $request->amount, // Add original_price
                'amount' => $request->amount, // Add amount field
                'customer_name' => $request->customer_name ?? 'Walk-in Customer',
                'customer_mobile' => $request->customer_mobile ?? 'N/A',
                'network' => $request->provider, // Add network field
                'provider' => $request->provider,
                'status' => 'completed',
                'transaction_id' => 'TVL' . strtoupper(uniqid()), // TV reference
                'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999), // Add reference_number
                'processed_by' => auth()->id() ?: 8, // Add processed_by
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'TV E-Load record added successfully!',
                'transaction' => [
                    'id' => $transaction->id,
                    'eload_number' => $transaction->eload_number,
                    'price' => $transaction->price,
                    'status' => $transaction->status,
                    'transaction_id' => $transaction->transaction_id,
                    'network' => $request->provider, // Add provider for display
                    'provider' => $request->provider, // Add provider field for display
                    'customer_name' => $request->customer_name ?? 'Walk-in Customer', // Add customer for display
                    'customer_mobile' => $request->customer_mobile ?? 'N/A', // Add contact for display
                    'created_at' => $transaction->created_at,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search TV E-Load transactions
     */
    public function searchTransactions(Request $request)
    {
        try {
            $searchTerm = $request->input('search');
            
            if (!$searchTerm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term is required'
                ], 400);
            }
            
            // Search by account number, transaction ID, and customer name
            $transactions = EloadTransaction::where('status', 'completed')
                ->where(function($query) use ($searchTerm) {
                    $query->where('eload_number', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('transaction_id', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                              $userQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                          });
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'transactions' => $transactions,
                'count' => $transactions->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching transactions: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clean up old TV E-Load transactions
     */
    public function cleanupOldTransactions(Request $request)
    {
        try {
            // Calculate date 1 year ago
            $cutoffDate = Carbon::now()->subYear(1);
            
            // Count old transactions
            $oldTransactions = EloadTransaction::where('created_at', '<', $cutoffDate)
                ->where('status', 'completed')
                ->count();
                
            if ($oldTransactions === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'No old transactions found.',
                    'deleted_count' => 0
                ]);
            }
            
            // Delete old transactions
            $deletedCount = EloadTransaction::where('created_at', '<', $cutoffDate)
                ->where('status', 'completed')
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} transactions older than 1 year.",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning up old transactions: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get transaction history
     */
    public function getTransactions(Request $request)
    {
        $query = EloadTransaction::with('user')
            ->where('status', 'completed') // Only get completed transactions
            ->orderBy('created_at', 'desc');
        
        // Apply date filter if provided
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'transactions' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }
    
    /**
     * Get provider information
     */
    public function getProviderInfo(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:Cignal,GMA Pinoy TV,Kapamilya Channel,Dream Satellite,Satlite,Sky Direct,GSAT,Cignal Play',
        ]);
        
        $provider = $request->provider;
        
        $providerInfo = [
            'Cignal' => [
                'min_load' => 50,
                'max_load' => 5000,
                'load_types' => ['Monthly Subscription', 'Prepaid Load', 'Pay-per-view'],
                'description' => 'Direct-to-Home Satellite TV service with HD channels',
                'popular_amounts' => [100, 200, 500, 1000]
            ],
            'GMA Pinoy TV' => [
                'min_load' => 100,
                'max_load' => 3000,
                'load_types' => ['Monthly Subscription', 'Annual Subscription'],
                'description' => 'International Filipino channel for OFWs',
                'popular_amounts' => [150, 300, 600, 1200]
            ],
            'Kapamilya Channel' => [
                'min_load' => 100,
                'max_load' => 3000,
                'load_types' => ['Monthly Subscription', 'Annual Subscription'],
                'description' => 'ABS-CBN International channel',
                'popular_amounts' => [150, 300, 600, 1200]
            ],
            'Dream Satellite' => [
                'min_load' => 50,
                'max_load' => 2000,
                'load_types' => ['Prepaid Load', 'Monthly Subscription'],
                'description' => 'Affordable satellite TV service',
                'popular_amounts' => [100, 200, 500, 1000]
            ],
            'Satlite' => [
                'min_load' => 50,
                'max_load' => 1500,
                'load_types' => ['Prepaid Load', 'Monthly Subscription'],
                'description' => 'Budget-friendly satellite TV',
                'popular_amounts' => [100, 200, 300, 500]
            ],
            'Sky Direct' => [
                'min_load' => 100,
                'max_load' => 3000,
                'load_types' => ['Monthly Subscription', 'Pay-per-view'],
                'description' => 'Direct-to-Home TV service',
                'popular_amounts' => [150, 300, 600, 1200]
            ],
            'GSAT' => [
                'min_load' => 50,
                'max_load' => 2000,
                'load_types' => ['Prepaid Load', 'Monthly Subscription'],
                'description' => 'Global Satellite TV service',
                'popular_amounts' => [100, 200, 500, 1000]
            ],
            'Cignal Play' => [
                'min_load' => 50,
                'max_load' => 1000,
                'load_types' => ['Digital Credits', 'Monthly Subscription'],
                'description' => 'Digital streaming service',
                'popular_amounts' => [100, 200, 300, 500]
            ]
        ];
        
        return response()->json([
            'provider' => $provider,
            'info' => $providerInfo[$provider] ?? null
        ]);
    }
    
    /**
     * Simulate load processing with real load provider integration
     */
    private function simulateLoadProcessing($transaction)
    {
        try {
            // Simulate API call delay
            usleep(100000); // 0.1 second
            
            // Get provider details for load processing
            $provider = $transaction->network;
            $accountNumber = $transaction->mobile_number;
            $amount = $transaction->amount;
            
            // Simulate different load provider APIs
            $loadResult = $this->processProviderLoad($provider, $accountNumber, $amount);
            
            if ($loadResult['success']) {
                // Update transaction as completed
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now('Asia/Manila'),
                    'provider_response' => json_encode([
                        'success' => true,
                        'message' => 'Load successfully sent to ' . $provider . ' account ' . $accountNumber,
                        'reference' => $transaction->reference_number,
                        'account' => $accountNumber,
                        'amount' => $amount,
                        'provider' => $provider,
                        'processed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s'),
                        'load_confirmation' => $loadResult['confirmation'] ?? 'Load credited successfully'
                    ])
                ]);
                
                // Log successful transaction
                \Log::info('TV E-Load Transaction Successful', [
                    'reference' => $transaction->reference_number,
                    'provider' => $provider,
                    'account' => $accountNumber,
                    'amount' => $amount,
                    'processed_by' => auth()->user()->name
                ]);
                
            } else {
                // Update transaction as failed
                $transaction->update([
                    'status' => 'failed',
                    'completed_at' => Carbon::now('Asia/Manila'),
                    'provider_response' => json_encode([
                        'success' => false,
                        'error' => $loadResult['error'] ?? 'Load processing failed',
                        'reference' => $transaction->reference_number,
                        'account' => $accountNumber,
                        'amount' => $amount,
                        'provider' => $provider,
                        'failed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
                    ])
                ]);
                
                // Log failed transaction
                \Log::error('TV E-Load Transaction Failed', [
                    'reference' => $transaction->reference_number,
                    'provider' => $provider,
                    'account' => $accountNumber,
                    'amount' => $amount,
                    'error' => $loadResult['error'] ?? 'Unknown error',
                    'processed_by' => auth()->user()->name
                ]);
            }
            
        } catch (\Exception $e) {
            // Handle processing errors
            $transaction->update([
                'status' => 'failed',
                'completed_at' => Carbon::now('Asia/Manila'),
                'provider_response' => json_encode([
                    'success' => false,
                    'error' => 'System error: ' . $e->getMessage(),
                    'reference' => $transaction->reference_number,
                    'failed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
                ])
            ]);
            
            \Log::error('TV E-Load Processing Exception', [
                'reference' => $transaction->reference_number,
                'error' => $e->getMessage(),
                'processed_by' => auth()->user()->name
            ]);
        }
    }
    
    /**
     * Process load with specific provider
     */
    private function processProviderLoad($provider, $accountNumber, $amount)
    {
        // Simulate different provider APIs
        $providers = [
            'Cignal' => [
                'api_endpoint' => 'https://api.cignal.tv/load',
                'success_rate' => 0.95, // 95% success rate for testing
                'confirmation_format' => 'CIGNAL-{reference}-{timestamp}'
            ],
            'GMA Pinoy TV' => [
                'api_endpoint' => 'https://api.gmanetwork.tv/load',
                'success_rate' => 0.90,
                'confirmation_format' => 'GMA-{reference}-{timestamp}'
            ],
            'Kapamilya Channel' => [
                'api_endpoint' => 'https://api.abs-cbn.tv/load',
                'success_rate' => 0.88,
                'confirmation_format' => 'KAPAMILYA-{reference}-{timestamp}'
            ],
            'Dream Satellite' => [
                'api_endpoint' => 'https://api.dreamsat.tv/load',
                'success_rate' => 0.92,
                'confirmation_format' => 'DREAM-{reference}-{timestamp}'
            ],
            'Satlite' => [
                'api_endpoint' => 'https://api.satlite.tv/load',
                'success_rate' => 0.85,
                'confirmation_format' => 'SATLITE-{reference}-{timestamp}'
            ],
            'Sky Direct' => [
                'api_endpoint' => 'https://api.skydirect.tv/load',
                'success_rate' => 0.90,
                'confirmation_format' => 'SKY-{reference}-{timestamp}'
            ],
            'GSAT' => [
                'api_endpoint' => 'https://api.gsat.tv/load',
                'success_rate' => 0.87,
                'confirmation_format' => 'GSAT-{reference}-{timestamp}'
            ],
            'Cignal Play' => [
                'api_endpoint' => 'https://api.cignalplay.tv/load',
                'success_rate' => 0.93,
                'confirmation_format' => 'CIGNALPLAY-{reference}-{timestamp}'
            ]
        ];
        
        $providerConfig = $providers[$provider] ?? null;
        
        if (!$providerConfig) {
            return [
                'success' => false,
                'error' => 'Provider not supported: ' . $provider
            ];
        }
        
        // Simulate API call success/failure based on provider success rate
        $randomSuccess = (mt_rand() / mt_getrandmax()) <= $providerConfig['success_rate'];
        
        if ($randomSuccess) {
            // Simulate successful load
            $confirmation = str_replace(
                ['{reference}', '{timestamp}'],
                [uniqid(), time()],
                $providerConfig['confirmation_format']
            );
            
            return [
                'success' => true,
                'confirmation' => $confirmation,
                'message' => "Load of ₱{$amount} successfully sent to {$provider} account {$accountNumber}",
                'provider' => $provider,
                'account' => $accountNumber,
                'amount' => $amount,
                'processed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
            ];
        } else {
            // Simulate failed load
            return [
                'success' => false,
                'error' => "Failed to process load for {$provider} account {$accountNumber}. Please try again.",
                'provider' => $provider,
                'account' => $accountNumber,
                'amount' => $amount
            ];
        }
    }
    
    /**
     * Manual load processing for cellphone loading
     */
    public function manualLoadProcess(Request $request)
    {
        try {
            $request->validate([
                'mobile_number' => 'required|string|regex:/^[0-9]+$/|min:10|max:15',
                'network' => 'required|string|in:Smart,Globe,TNT,DITO,Cherry Mobile',
                'amount' => 'required|numeric|min:10|max:10000',
                'load_type' => 'required|string|in:regular,promo',
            ]);
            
            $mobileNumber = preg_replace('/[^0-9]/', '', $request->mobile_number);
            $network = $request->network;
            $amount = $request->amount;
            $loadType = $request->load_type;
            
            // Create manual load transaction
            $transaction = EloadTransaction::create([
                'user_id' => auth()->id(),
                'mobile_number' => $mobileNumber,
                'amount' => $amount,
                'network' => $network,
                'transaction_type' => 'manual_load',
                'status' => 'pending',
                'reference_number' => 'ML' . strtoupper(uniqid()),
                'created_at' => Carbon::now('Asia/Manila'),
            ]);
            
            // Process manual load
            $this->processManualLoad($transaction, $loadType);
            
            // Refresh the transaction to get updated status
            $transaction->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Manual load transaction processed!',
                'transaction' => $transaction
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Manual Load Processing Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process manual cellphone load
     */
    private function processManualLoad($transaction, $loadType)
    {
        try {
            // Simulate manual load processing
            usleep(500000); // 0.5 second delay for manual processing
            
            $mobileNumber = $transaction->mobile_number;
            $network = $transaction->network;
            $amount = $transaction->amount;
            
            // Simulate sending load via manual system
            $loadSuccess = $this->sendManualLoad($mobileNumber, $network, $amount, $loadType);
            
            if ($loadSuccess['success']) {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now('Asia/Manila'),
                    'provider_response' => json_encode([
                        'success' => true,
                        'message' => "Manual load of ₱{$amount} sent to {$network} {$mobileNumber}",
                        'reference' => $transaction->reference_number,
                        'confirmation' => $loadSuccess['confirmation'],
                        'load_type' => $loadType,
                        'processed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
                    ])
                ]);
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'completed_at' => Carbon::now('Asia/Manila'),
                    'provider_response' => json_encode([
                        'success' => false,
                        'error' => $loadSuccess['error'],
                        'reference' => $transaction->reference_number,
                        'load_type' => $loadType,
                        'failed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
                    ])
                ]);
            }
            
        } catch (\Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'completed_at' => Carbon::now('Asia/Manila'),
                'provider_response' => json_encode([
                    'success' => false,
                    'error' => 'Manual load error: ' . $e->getMessage(),
                    'reference' => $transaction->reference_number,
                    'failed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
                ])
            ]);
        }
    }
    
    /**
     * Send manual load to cellphone
     */
    private function sendManualLoad($mobileNumber, $network, $amount, $loadType)
    {
        // Simulate different manual load systems
        $successRate = 0.95; // 95% success rate for manual loads
        
        $randomSuccess = (mt_rand() / mt_getrandmax()) <= $successRate;
        
        if ($randomSuccess) {
            $confirmation = 'MANUAL-' . strtoupper(uniqid()) . '-' . time();
            
            return [
                'success' => true,
                'confirmation' => $confirmation,
                'message' => "Manual {$loadType} load of ₱{$amount} successfully sent to {$network} {$mobileNumber}",
                'mobile_number' => $mobileNumber,
                'network' => $network,
                'amount' => $amount,
                'load_type' => $loadType,
                'processed_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
            ];
        } else {
            return [
                'success' => false,
                'error' => "Failed to send manual load to {$network} {$mobileNumber}. Please check network connection and try again.",
                'mobile_number' => $mobileNumber,
                'network' => $network,
                'amount' => $amount,
                'load_type' => $loadType
            ];
        }
    }
    
    /**
     * Export transactions to Excel
     */
    public function exportTransactions(Request $request)
    {
        $query = EloadTransaction::with('user')
            ->where('transaction_type', 'television_eload');
        
        // Apply filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->provider) {
            $query->where('network', $request->provider);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $filename = "television_eload_transactions_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'Reference Number',
                'Account Number',
                'Provider',
                'Customer Name',
                'Customer Contact',
                'Amount',
                'Status',
                'Processed By',
                'Date',
                'Completed At'
            ]);
            
            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->reference_number,
                    $transaction->mobile_number,
                    $transaction->network,
                    $transaction->customer_name ?? 'N/A',
                    $transaction->customer_mobile ?? 'N/A',
                    $transaction->amount,
                    $transaction->status,
                    $transaction->user->name,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->completed_at ? $transaction->completed_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
