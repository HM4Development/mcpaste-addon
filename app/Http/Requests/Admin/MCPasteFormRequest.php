<?php

namespace Pterodactyl\Http\Requests\Admin;

class MCPasteFormRequest extends AdminFormRequest
{
    public function rules()
    {
        return [
            'token' => 'required|string',
            'postServerInfo' => 'nullable',
        ];
    }
}
