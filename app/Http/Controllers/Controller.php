<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function schema(array $data, array $schema)
    {
        $validator = Validator::make($data, $schema);

        if ($validator->fails()) {
            $this->badRequest($validator->errors());
        }

        return $validator->validated();
    }

    protected function badRequest($response)
    {
        throw new HttpResponseException(new Response($response, 400));
    }
}
