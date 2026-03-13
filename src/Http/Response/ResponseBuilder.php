<?php

namespace AppHttpResponse;

class ResponseBuilder
{
    /**
     * Handle null payload gracefully
     * Returns 422 with validation error instead of 500
     */
    public function build( = null, int  = 200): array
    {
        if ( === null &&  === 200) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Payload cannot be null',
                'data' => null
            ];
        }

        return [
            'success' =>  >= 200 &&  < 300,
            'code' => ,
            'message' => 'OK',
            'data' =>         ];
    }
}
