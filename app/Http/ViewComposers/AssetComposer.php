<?php

namespace Pterodactyl\Http\ViewComposers;

use Illuminate\View\View;
use Pterodactyl\Services\Helpers\AssetHashService;
use Pterodactyl\Http\Controllers\Admin\MCPasteController;
use Pterodactyl\Repositories\Eloquent\MCPasteVariableRepository;

class AssetComposer
{
    /**
     * @var \Pterodactyl\Services\Helpers\AssetHashService
     */
    private $assetHashService;
    private MCPasteVariableRepository $pasteVariableRepository;

    /**
     * AssetComposer constructor.
     */
    public function __construct(AssetHashService $assetHashService, MCPasteVariableRepository $pasteVariableRepository)
    {
        $this->assetHashService = $assetHashService;
        $this->pasteVariableRepository = $pasteVariableRepository;
    }

    /**
     * Provide access to the asset service in the views.
     */
    public function compose(View $view)
    {
        $view->with('asset', $this->assetHashService);
        $view->with('siteConfiguration', [
            'name' => config('app.name') ?? 'Pterodactyl',
            'locale' => config('app.locale') ?? 'en',
            'recaptcha' => [
                'enabled' => config('recaptcha.enabled', false),
                'siteKey' => config('recaptcha.website_key') ?? '',
            ],
        ]);
        $view->with('mcPasteData', [
            'tokenValid' => $this->pasteVariableRepository->tokenValid(),
            'style' => MCPasteController::getStyle($this->pasteVariableRepository),
        ]);
    }
}
