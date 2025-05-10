<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected $baseUrl;
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.digiflazz.url');
        $this->username = config('services.digiflazz.username');
        $this->apiKey = config('services.digiflazz.api_key');
    }

    public function getProducts()
    {
        try {
            $sign = md5($this->username . $this->apiKey . 'pricelist');

            $response = Http::post($this->baseUrl . '/price-list', [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $sign
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['data'];
                }
            }

            Log::error('Digiflazz API Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Digiflazz Service Error: ' . $e->getMessage());
            return [];
        }
    }

    public function checkBalance()
    {
        try {
            $sign = md5($this->username . $this->apiKey . 'deposit');

            $response = Http::post($this->baseUrl . '/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $sign
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['data']['deposit'];
                }
            }

            Log::error('Digiflazz Balance Check Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz Balance Check Error: ' . $e->getMessage());
            return null;
        }
    }

    public function purchase($productId, $buyerNumber)
    {
        try {
            $sign = md5($this->username . $this->apiKey . $productId);

            $response = Http::post($this->baseUrl . '/transaction', [
                'username' => $this->username,
                'buyer_sku_code' => $productId,
                'customer_no' => $buyerNumber,
                'ref_id' => 'INV-' . time(),
                'sign' => $sign
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['data'];
                }
            }

            Log::error('Digiflazz Purchase Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz Purchase Error: ' . $e->getMessage());
            return null;
        }
    }

    public function checkStatus($refId)
    {
        try {
            $sign = md5($this->username . $this->apiKey . $refId);

            $response = Http::post($this->baseUrl . '/transaction', [
                'cmd' => 'status',
                'username' => $this->username,
                'ref_id' => $refId,
                'sign' => $sign
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['data'];
                }
            }

            Log::error('Digiflazz Status Check Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz Status Check Error: ' . $e->getMessage());
            return null;
        }
    }
}
