<?php

namespace Synerise\SyliusIntegrationPlugin\Loguzz\Formatter;

use Loguzz\Formatter\RequestCurlFormatter;
use Psr\Http\Message\RequestInterface;

class RequestCurlSanitizedFormatter extends RequestCurlFormatter
{
    /**
     * Parse data
     *
     * @param RequestInterface $request
     * @param array $options
     * @return array
     */
    protected function parseData(RequestInterface $request, array $options): array
    {
        $data = parent::parseData($request, $options);
        if (isset($data['headers']['authorization'])) {
            $authorizationString = $data['headers']['authorization'];
            if ($authorizationString) {
                $data['headers']['authorization'] = preg_replace(
                    '/(Basic |Bearer )(.*)/',
                    '$1{TOKEN}',
                    $authorizationString
                );
            }
        }

        return $data;
    }
}
