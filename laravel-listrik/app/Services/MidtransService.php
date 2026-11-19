<?php

namespace App\Services;

use Exception;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo');
        $this->clientKey = env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo');
        $this->isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
    }

    /**
     * Dapatkan Snap Token untuk checkout modal di web konsumen
     */
    public function getSnapToken(array $params): string
    {
        $url = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':')
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception("Midtrans cURL Error: " . $err);
        }

        $res = json_decode($response, true);
        return $res['token'] ?? ('demo-token-' . uniqid());
    }

    /**
     * Verifikasi Signature Key Notifikasi Webhook
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $hash = hash("sha512", $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($hash, $signatureKey);
    }
}

