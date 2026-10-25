<?php

namespace App\Modules\Integration\Core;

use App\Modules\Integration\Enums\IntegrationDirection;
use App\Modules\Integration\Enums\IntegrationLogStatus;
use App\Modules\Integration\Exceptions\IntegrationAuthException;
use App\Modules\Integration\Exceptions\IntegrationException;
use App\Modules\Integration\Exceptions\IntegrationRejectedException;
use App\Modules\Integration\Exceptions\IntegrationTimeoutException;
use App\Modules\Integration\Exceptions\IntegrationUnavailableException;
use App\Modules\Integration\Services\IntegrationLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class AbstractHttpClient
{
    protected IntegrationLogger $logger;

    public function __construct(?IntegrationLogger $logger = null)
    {
        $this->logger = $logger ?? app(IntegrationLogger::class);
    }

    abstract public function baseUrl(): string;

    abstract public function authenticate(array $credentials): array;

    /**
     * @param bool $idempotent If true or if idempotencyKey provided, retry allowed on retryable failures
     */
    protected function send(string $method, string $path, array $options = [], bool $idempotent = false, ?string $idempotencyKey = null): Response
    {
        $start = microtime(true);
        $config = config('integrations.http', []);
        $timeout = $options['timeout'] ?? ($config['timeout'] ?? 30);
        $retries = $options['retries'] ?? ($config['retries'] ?? 0);
        $retrySleep = $options['retry_sleep_ms'] ?? ($config['retry_sleep_ms'] ?? 200);

        $url = rtrim($this->baseUrl(), '/').'/'.ltrim($path, '/');
        $methodUpper = strtoupper($method);
        $canRetry = $idempotent || !empty($idempotencyKey);

        $attempt = 0;
        $lastException = null;

        while ($attempt <= $retries) {
            try {
                $request = Http::timeout($timeout);
                if ($idempotencyKey) {
                    $request = $request->withHeaders(['Idempotency-Key' => $idempotencyKey]);
                }
                if (!empty($options['headers'])) {
                    $request = $request->withHeaders($options['headers']);
                }

                $payload = $options['body'] ?? $options['json'] ?? [];
                if (isset($options['json'])) {
                    $response = $request->withHeaders(array_merge($options['headers'] ?? [], ['Accept' => 'application/json']))
                        ->send($methodUpper, $url, ['json' => $options['json']]);
                } elseif ($methodUpper === 'GET') {
                    $response = $request->get($url, $options['query'] ?? []);
                } else {
                    $response = $request->send($methodUpper, $url, $options);
                }

                $durationMs = (int) round((microtime(true) - $start) * 1000);

                $this->logger->log([
                    'provider' => $options['provider'] ?? null,
                    'connection_id' => $options['connection_id'] ?? null,
                    'operation' => $options['operation'] ?? $path,
                    'direction' => IntegrationDirection::OUTBOUND,
                    'method' => $methodUpper,
                    'url' => $url,
                    'status_code' => $response->status(),
                    'duration_ms' => $durationMs,
                    'request' => $options['json'] ?? $options['body'] ?? ($options['query'] ?? []),
                    'response' => $response->json() ?? $response->body(),
                    'status' => $response->successful() ? IntegrationLogStatus::SUCCESS : IntegrationLogStatus::ERROR,
                ]);

                if ($response->successful()) {
                    return $response;
                }

                $exception = $this->mapHttpError($response);
                if (!$exception->isRetryable() || !$canRetry || $attempt >= $retries) {
                    throw $exception;
                }
                $lastException = $exception;
                $attempt++;
                usleep($retrySleep * 1000);
                continue;
            } catch (ConnectionException $e) {
                $durationMs = (int) round((microtime(true) - $start) * 1000);
                $exception = (new IntegrationTimeoutException('Integração: timeout/connection error.', 0, $e))->setRetryable(true);
                $this->logger->log([
                    'provider' => $options['provider'] ?? null,
                    'connection_id' => $options['connection_id'] ?? null,
                    'operation' => $options['operation'] ?? $path,
                    'direction' => IntegrationDirection::OUTBOUND,
                    'method' => $methodUpper,
                    'url' => $url,
                    'status_code' => null,
                    'duration_ms' => $durationMs,
                    'request' => $options['json'] ?? $options['body'] ?? ($options['query'] ?? []),
                    'response' => ['error' => $e->getMessage()],
                    'status' => IntegrationLogStatus::TIMEOUT,
                    'error_class' => get_class($exception),
                ]);
                if (!$canRetry || $attempt >= $retries) {
                    throw $exception;
                }
                $lastException = $exception;
                $attempt++;
                usleep($retrySleep * 1000);
                continue;
            } catch (RequestException $e) {
                $durationMs = (int) round((microtime(true) - $start) * 1000);
                $response = $e->response;
                if ($response) {
                    $exception = $this->mapHttpError($response);
                } else {
                    $exception = new IntegrationException('Erro na requisição.', 0, $e);
                }
                $this->logger->log([
                    'provider' => $options['provider'] ?? null,
                    'connection_id' => $options['connection_id'] ?? null,
                    'operation' => $options['operation'] ?? $path,
                    'direction' => IntegrationDirection::OUTBOUND,
                    'method' => $methodUpper,
                    'url' => $url,
                    'status_code' => $response?->status(),
                    'duration_ms' => $durationMs,
                    'request' => $options['json'] ?? $options['body'] ?? ($options['query'] ?? []),
                    'response' => $response ? ($response->json() ?? $response->body()) : ['error' => $e->getMessage()],
                    'status' => IntegrationLogStatus::ERROR,
                    'error_class' => get_class($exception),
                ]);
                throw $exception;
            } catch (IntegrationException $e) {
                throw $e;
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        throw new IntegrationException('Falha inesperada na integração.');
    }

    protected function mapHttpError(Response $response): IntegrationException
    {
        $status = $response->status();
        if ($status === 401 || $status === 403) {
            return (new IntegrationAuthException('Integração: não autorizado.', $status))->setRetryable(false);
        }
        if ($status === 422 || $status === 400) {
            return (new IntegrationRejectedException('Integração: requisição rejeitada.', $status))->setRetryable(false);
        }
        if ($status === 408 || $status === 425) {
            return (new IntegrationTimeoutException('Integração: timeout.', $status))->setRetryable(true);
        }
        if ($status >= 500) {
            return (new IntegrationUnavailableException('Integração indisponível.', $status))->setRetryable(true);
        }
        return (new IntegrationException('Erro na integração.', $status))->setRetryable(false);
    }
}
