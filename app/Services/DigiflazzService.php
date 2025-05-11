<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DigiflazzService
{
    protected $baseUrl;
    protected $username;
    protected $apiKey;
    protected $sign;

    public function __construct()
    {
        $this->baseUrl = config('services.digiflazz.url');
        $this->username = config('services.digiflazz.username');
        $this->apiKey = config('services.digiflazz.api_key');
        $this->sign = md5($this->username . $this->apiKey);
    }

    public function getProducts()
    {
        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/price-list', [
                    'cmd' => 'prepaid',
                    'username' => $this->username,
                    'sign' => $this->sign
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Digiflazz API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function checkBalance()
    {
        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/cek-saldo', [
                    'cmd' => 'deposit',
                    'username' => $this->username,
                    'sign' => $this->sign
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Digiflazz balance check error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz balance check exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function createTransaction($data)
    {
        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/transaction', [
                    'username' => $this->username,
                    'buyer_sku_code' => $data['product_id'],
                    'customer_no' => $data['customer_number'],
                    'ref_id' => $data['ref_id'],
                    'sign' => $this->sign
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Digiflazz transaction error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $data
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz transaction exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return null;
        }
    }

    public function checkTransactionStatus($refId)
    {
        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/transaction', [
                    'cmd' => 'status',
                    'username' => $this->username,
                    'ref_id' => $refId,
                    'sign' => $this->sign
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Digiflazz status check error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'ref_id' => $refId
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz status check exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ref_id' => $refId
            ]);

            return null;
        }
    }

    public function processTransaction($transaction)
    {
        try {
            $sign = md5($this->username . $this->apiKey . $transaction->order_id);

            $response = Http::post($this->baseUrl . '/transaction', [
                'cmd' => 'topup',
                'username' => $this->username,
                'sign' => $sign,
                'buyer_sku_code' => $transaction->product->sku,
                'customer_no' => $transaction->user_id,
                'ref_id' => $transaction->order_id,
                'amount' => $transaction->total_amount,
                'buyer_name' => $transaction->user->name,
                'buyer_email' => $transaction->user->email,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'message' => 'Transaction processed successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'data' => $data,
                        'message' => $data['message'] ?? 'Transaction failed'
                    ];
                }
            }

            return [
                'success' => false,
                'data' => $response->json(),
                'message' => 'Failed to connect to payment gateway'
            ];
        } catch (\Exception $e) {
            Log::error('Error in DigiflazzService@processTransaction: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while processing the transaction'
            ];
        }
    }

    public function getProductList()
    {
        try {
            $sign = md5($this->username . $this->apiKey . 'pricelist');

            $response = Http::post($this->baseUrl . '/price-list', [
                'cmd' => 'pricelist',
                'username' => $this->username,
                'sign' => $sign
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'data' => $response->json(),
                'message' => 'Failed to get product list'
            ];
        } catch (\Exception $e) {
            Log::error('Error in DigiflazzService@getProductList: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while getting product list'
            ];
        }
    }
}
