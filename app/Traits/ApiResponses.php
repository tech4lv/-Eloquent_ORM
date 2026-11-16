<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

trait ApiResponses
{
    protected function ok($message, $data = [])
    {
        return $this->success($message, $data, 200);
    }

    protected function success(
        $message,
        $data = [],
        $statusCode = 200
    ) {
        $payload = [
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ];

        if ($data instanceof AnonymousResourceCollection
            && $data->resource instanceof AbstractPaginator) {
            $paginator = $data->resource;
            $payload['meta'] = [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ];
            $payload['links'] = [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ];
        }

        return response()->json($payload, $statusCode);
    }

    protected function error($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
