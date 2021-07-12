<?php

namespace Pterodactyl\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Pterodactyl\Models\MCPasteVariable;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class MCPasteVariableRepository extends EloquentRepository
{
    private CacheRepository $cache;

    public function __construct(Application $application, CacheRepository $cache)
    {
        parent::__construct($application);

        $this->cache = $cache;
    }

    public function model()
    {
        return MCPasteVariable::class;
    }

    public function getValue($key): ?string
    {
        $result = $this->getBuilder()->select(['value'])->where(['name' => $key])->first();
        if ($result == null) {
            return null;
        }

        $resultValue = $result->toArray()['value'];

        return $resultValue == '' ? null : $resultValue;
    }

    public function setValue(string $key, string $value)
    {
        $this->getBuilder()->updateOrInsert(['name' => $key], ['name' => $key, 'value' => $value]);
    }

    public function tokenValid(): bool
    {
        return $this->specificTokenValid($this->getValue('token'), true);
    }

    public function specificTokenValid(?string $token, bool $useCache): bool
    {
        if (is_null($token)) {
            return false;
        }
        if ($useCache && $this->cache->has('mcpaste_token_valid_' . $token)) {
            return $this->cache->get('mcpaste_token_valid_' . $token);
        }

        $tokenValid = Http::get("https://api.mcpaste.com/auth/token/$token")->status() == 200;
        if ($useCache) {
            $this->cache->set('mcpaste_token_valid_' . $token, $tokenValid, Carbon::now()->addMinutes(5));
        }

        return $tokenValid;
    }
}
