<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Servers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Pterodactyl\Models\Server;
use Illuminate\Support\Facades\Http;
use Pterodactyl\Repositories\Wings\DaemonServerRepository;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Repositories\Eloquent\MCPasteVariableRepository;
use Pterodactyl\Http\Requests\Api\Client\Servers\ShareLogRequest;

class MCPasteController extends ClientApiController
{
    private MCPasteVariableRepository $variableRepository;
    private DaemonServerRepository $daemonRepository;

    public function __construct(MCPasteVariableRepository $variableRepository, DaemonServerRepository $daemonRepository)
    {
        parent::__construct();

        $this->variableRepository = $variableRepository;
        $this->daemonRepository = $daemonRepository;
    }

    public function humanFileSize($size, $unit = '')
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2) . 'GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2) . 'MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2) . 'KB';
        }

        return number_format($size) . ' bytes';
    }

    public function index(ShareLogRequest $request, Server $server): array
    {
        $data = $request->input('data');
        $serverDetails = $this->daemonRepository->setServer($server)->getDetails();

        if ($this->variableRepository->getValue('postServerInfo') ?? false) {
            $data = '--------------------------------------------------------------------
Uploaded on: ' . Carbon::now()->toDateTimeString() . '
Server name: ' . $server->name . '
Server ID: ' . $server->uuid . '
Server node: ' . $server->node->name . '(' . $server->node_id . ')
Server state: ' . Arr::get($serverDetails, 'state', 'stopped') . '
Server CPU: ' . Arr::get($serverDetails, 'utilization.cpu_absolute', 0) . '%
Server RAM: ' . $this->humanFileSize(Arr::get($serverDetails, 'utilization.memory_bytes', 0)) . '
Server Disk: ' . $this->humanFileSize(Arr::get($serverDetails, 'utilization.disk_bytes', 0)) . '
Server docker image: ' . $server->image . '
Server egg: ' . $server->egg->name . '
Server owner: ' . $server->user->username . ' (' . $server->user->email . ')
--------------------------------------------------------------------
' . $data;
        }

        return Http::asForm()->post('https://api.mcpaste.com', [
            'token' => $this->variableRepository->getValue('token'),
            'data' => $data,
        ])->json();
    }
}
