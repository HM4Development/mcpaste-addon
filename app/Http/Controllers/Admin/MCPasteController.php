<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Http\Requests\Admin\MCPasteFormRequest;
use Pterodactyl\Repositories\Eloquent\MCPasteVariableRepository;

class MCPasteController extends Controller
{
    private static array $styleDefaults = [
        'buttonLocation' => 'component',
        'textButtonText' => 'Send to McPaste.com',
        'textColor' => '#cad1d8',
        'textButtonColor' => '#3f4d5a',
        'textButtonHoverColor' => '#515f6c',
        'boxColor' => '#3f4d5a',
        'textSize' => 'text-xs',
        'buttonSize' => 'xsmall',
        'shadow' => true,
        'icon' => 'clipboard',
        'iconColor' => '#e5e8eb',
        'iconHoverColor' => '#3f4d5a',
        'toastTextColor' => '#ffffff',
        'toastBoxColor' => '#1f2933',
        'toastBorderColor' => '#000000',
        'toastOpacity' => 75,
        'toastText' => 'Copied https://mcpaste.com/%key% to clipboard',
        'toastErrorText' => "Couldn't share log. Error: %error%",
    ];

    private MCPasteVariableRepository $variableRepository;
    private AlertsMessageBag $alert;

    public function __construct(MCPasteVariableRepository $variableRepository, AlertsMessageBag $alert)
    {
        $this->variableRepository = $variableRepository;
        $this->alert = $alert;
    }

    public static function getStyle(MCPasteVariableRepository $variableRepository): array
    {
        $style = $variableRepository->getValue('style');
        if (is_null($style)) {
            $style = self::$styleDefaults;
        } else {
            $style = json_decode($style, true); // tried putting in ternary operators but PHP is weird
        }

        return $style;
    }

    public function index(Request $request)
    {
        $token = $this->variableRepository->getValue('token');
        if (!$this->variableRepository->specificTokenValid($token, false)) {
            $this->alert->danger('Your current token is invalid!')->flash();
        }

        $style = self::getStyle($this->variableRepository);

        return view('admin.mcpaste.index', [
            'config' => [
                'token' => $this->variableRepository->getValue('token'),
                'postServerInfo' => (bool) ($this->variableRepository->getValue('postServerInfo') ?? false),
                'design' => $style,
            ],
        ]);
    }

    public function update(MCPasteFormRequest $request)
    {
        $token = $request->input('token');
        $postServerInfo = (bool) ($request->input('postServerInfo') ?? false);

        $tokenValid = $this->variableRepository->specificTokenValid($token, false);
        if ($tokenValid) {
            $this->variableRepository->setValue('token', $token);
        } else {
            $this->alert->danger('Invalid token specified!')->flash();
        }

        $this->variableRepository->setValue('postServerInfo', $postServerInfo ?? 0);
        $this->alert->success('Configuration successfully updated!')->flash();

        $style = $this->extractInputFromRequest($request, ['buttonLocation', 'textButtonText', 'textColor', 'textButtonColor', 'textButtonHoverColor', 'boxColor', 'textSize', 'buttonSize', 'icon', 'iconColor', 'iconHoverColor', 'toastTextColor', 'toastBoxColor', 'toastBorderColor', 'toastText', 'toastErrorText']);

        // non-string values
        $style['shadow'] = (bool) ($request->input('shadow') ?? false);
        $style['toastOpacity'] = (int) ($request->input('toastOpacity') ?? 75);

        $this->variableRepository->setValue('style', json_encode($style));

        return view('admin.mcpaste.index', [
            'config' => [
                'token' => $token,
                'postServerInfo' => $postServerInfo,
                'design' => $style,
            ],
        ]);
    }

    public function reset(Request $request)
    {
        $this->variableRepository->setValue('style', json_encode(self::$styleDefaults));

        return view('admin.mcpaste.index', [
            'config' => [
                'token' => $this->variableRepository->getValue('token'),
                'postServerInfo' => (bool) ($this->variableRepository->getValue('postServerInfo') ?? false),
                'design' => self::getStyle($this->variableRepository),
            ],
        ]);
    }

    private function extractInputFromRequest(Request $request, array $values): array
    {
        $ret = [];

        foreach ($values as $value) {
            $ret[$value] = $request->input($value);
        }

        return $ret;
    }
}
