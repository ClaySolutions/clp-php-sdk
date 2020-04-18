<?php
declare(strict_types=1);

namespace Clay\CLP\Contracts;

use Clay\CLP\Http\HttpResponse;

interface HttpClient {

	public function post(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse;

	public function put(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse;

	public function patch(string $path, array $payload, array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse;

	public function get(string $path, array $headers = [], bool $skipDefaultHeaders = false): HttpResponse;

	public function delete(string $path, array $headers = [], bool $skipDefaultHeaders = false): HttpResponse;

}