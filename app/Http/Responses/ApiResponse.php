<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse implements Responsable
{
    /**
     * @var array
     */
    protected array $data = [
        'status_code' => Response::HTTP_OK,
        'success' => true,
        'data' => null,
        'message' => '',
        'errors' => [],
          'pagination' => null,
    ];

    /**
     * @var int
     */
    protected int $statusCode = Response::HTTP_OK;

    public static function make(): self
    {
        return new static();
    }
public function pagination($pagination): self
    {
        if ($pagination instanceof LengthAwarePaginator) {
            $this->data['pagination'] = [
                'total'        => $pagination->total(),
                'count'        => $pagination->count(),
                'per_page'     => $pagination->perPage(),
                'current_page' => $pagination->currentPage(),
                'total_pages'  => $pagination->lastPage(),
                'has_more'     => $pagination->hasMorePages(),
            ];
        } elseif (is_array($pagination)) {
            $this->data['pagination'] = $pagination;
        }

        return $this;
    }
    public function __construct()
    {
        $this->errors((object)[]);
    }

    public function data($data = []): self
    {
        $this->data['data'] = $data;

        return $this;
    }

    public function message($message = ''): self
    {
        $this->data['message'] = $message;

        return $this;
    }

    public function errors($errors = []): self
    {
        $this->data['errors'] = $errors;

        return $this;
    }

    public function success($success = true): self
    {
        $this->data['success'] = $success;

        return $this;
    }

    public function statusCode($code = 200): self
    {
        $this->statusCode = $code;
        $this->data['status_code'] = $code;

        return $this;
    }

    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->data, $this->statusCode);
    }
}
