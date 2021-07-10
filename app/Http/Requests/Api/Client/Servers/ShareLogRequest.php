<?php

namespace Pterodactyl\Http\Requests\Api\Client\Servers;

use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class ShareLogRequest extends ClientApiRequest
{
    public function rules(): array
    {
        return [
            'data' => 'string|required',
        ];
    }
}
