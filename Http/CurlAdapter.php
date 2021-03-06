<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Http;

use Apisearch\Exception\ConnectionException;

/**
 * Class CurlAdapter.
 */
class CurlAdapter implements HttpAdapter
{
    /**
     * Get.
     *
     * @param string       $host
     * @param string       $method
     * @param RequestParts $requestParts
     *
     * @return array
     *
     * @throws ConnectionException
     */
    public function getByRequestParts(
        string $host,
        string $method,
        RequestParts $requestParts
    ): array {
        $json = json_encode($requestParts->getParameters()['json']);
        $formattedUrl = rtrim($host, '/').'/'.ltrim($requestParts->getUrl(), '/');
        $method = strtoupper($method);
        $headers = [];
        foreach ($requestParts->getParameters()['headers'] ?? [] as $header => $value) {
            $headers[] = "$header: $value";
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $formattedUrl);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate');

        if (!in_array($method, ['GET', 'HEAD'])) {
            curl_setopt($c, CURLOPT_POSTFIELDS, $json);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: '.strlen($json);
        }

        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        $content = curl_exec($c);
        $responseCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        if (
            'HEAD' !== $method &&
            false === $content
        ) {
            throw ConnectionException::buildConnectExceptionByUrl($requestParts->getUrl());
        }

        return [
            'code' => (int) $responseCode,
            'body' => empty($content)
                ? []
                : json_decode($content, true),
        ];
    }
}
