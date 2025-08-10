<?php

namespace CodebarAg\LaravelEventLogs\Actions;

use CodebarAg\LaravelEventLogs\Models\EventLog;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AzureEventHubAction
{
    private string $endpoint;

    private string $hub;

    private string $policy;

    private string $primaryKey;

    public function __construct()
    {
        $providerConfig = (array) config('laravel-event-logs.providers.azure_event_hub', []);
        $endpointConfig = $providerConfig['endpoint'] ?? null;
        $hubConfig = $providerConfig['path'] ?? null;
        $policyConfig = $providerConfig['policy_name'] ?? null;
        $keyConfig = $providerConfig['primary_key'] ?? null;

        $this->endpoint = is_string($endpointConfig) ? rtrim($endpointConfig, '/') : '';
        $this->hub = is_string($hubConfig) ? trim($hubConfig, '/') : '';
        $this->policy = is_string($policyConfig) ? $policyConfig : '';
        $this->primaryKey = is_string($keyConfig) ? $keyConfig : '';
    }

    private static function resource(string $endpoint, string $hub): string
    {
        $parts = parse_url($endpoint);
        $scheme = $parts['scheme'] ?? 'https';
        $host = strtolower($parts['host'] ?? '');
        $hub = trim($hub, '/');

        return "{$scheme}://{$host}/{$hub}";
    }

    public function resourceUrl(): string
    {
        return self::resource($this->endpoint, $this->hub);
    }

    public function buildToken(): string
    {
        $resource = $this->resourceUrl();
        $encodedResource = rawurlencode($resource);
        $expiry = time() + 7200; // 1 hour
        $stringToSign = $encodedResource."\n".$expiry;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $stringToSign, $this->primaryKey, true)));

        return "SharedAccessSignature sr={$encodedResource}&sig={$signature}&se={$expiry}&skn={$this->policy}";
    }

    public function send(EventLog $eventLog): Response
    {
        $resource = $this->resourceUrl();
        $postUrl = "{$resource}/messages?api-version=2014-01";

        $response = Http::retry(3, 500)
            ->withHeaders([
                'Authorization' => $this->buildToken(),
                'Content-Type' => 'application/json',
            ])
            ->withBody(json_encode($eventLog->toArray(), JSON_UNESCAPED_SLASHES) ?: '{}', 'application/json')
            ->post($postUrl);

        return $response;
    }
}
