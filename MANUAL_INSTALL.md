# Addon Installation

routes/api-client.php
- after line 68
```php
Route::post('/share-log', [Client\Servers\MCPasteController::class, 'index']);
```

routes/admin.php
- at the end of file
```php
Route::group(['prefix' => 'mcpaste'], function () {
    Route::get('/', [Admin\MCPasteController::class, 'index'])->name('admin.mcpaste');
    Route::post('/', [Admin\MCPasteController::class, 'update']);
    Route::delete('/', [Admin\MCPasteController::class, 'reset']);
});
```

database/migrations
- copy every file

resources/views/admin/mcpaste
- create directory resources/views/admin/mcpaste(`mkdir -p resources/views/admin/mcpaste`)
- copy every file

resources/views/layouts/admin.blade.php
- after line 118
```tsx
<li class="{{ ! starts_with(Route::currentRouteName(), 'admin.mcpaste') ?: 'active' }}">
    <a href="{{ route('admin.mcpaste') }}">
        <i class="fa fa-clipboard"></i> <span>MCPaste</span>
    </a>
</li>
```

resources/views/templates/wrapper.blade.php
- after line 31
```php
@if(!empty($mcPasteData))
    <script>
        window.MCPasteData = {!! json_encode($mcPasteData) !!};
    </script>
@endif
```

resources/scripts/api/server
- copy every file

resources/scripts/components/server/McPaste.tsx
- copy the whole file

resources/scripts/components/server/ServerConsole.tsx
- at top of file
```
import McPaste, { mcPasteData, mcPasteStyle } from '@/components/server/McPaste';
```
- replace
```tsx
<Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
    <PowerControls/>
</Can>
```
with
```tsx
<div>
    <Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
        <PowerControls/>
    </Can>
    { mcPasteData.tokenValid && mcPasteStyle.buttonLocation === "component" && <McPaste position={'component'} /> }
</div>
```

resources/scripts/components/server/Console.tsx
- at top of file
```tsx
import McPaste, { mcPasteData, mcPasteStyle } from './McPaste';
```
- after line 228
```tsx
{ mcPasteData.tokenValid && mcPasteStyle.buttonLocation === "commandLine" && <McPaste position={'commandLine'} /> }
```

app/Repositories/Eloquent
- copy every file

app/Models
- copy every file

app/Http/ViewComposers/AssetComposer.php
- after line 6
```php
use Pterodactyl\Http\Controllers\Admin\MCPasteController;
use Pterodactyl\Repositories\Eloquent\MCPasteVariableRepository;
```
- after line 15
```php
private MCPasteVariableRepository $pasteVariableRepository;
```
- after line 39
```php
$view->with('mcPasteData', [
    'tokenValid' => $this->pasteVariableRepository->tokenValid(),
    'style' => MCPasteController::getStyle($this->pasteVariableRepository),
]);
```
- replace
```php
public function __construct(AssetHashService $assetHashService)
{
    $this->assetHashService = $assetHashService;
}
```
with
```php
public function __construct(AssetHashService $assetHashService, MCPasteVariableRepository $pasteVariableRepository)
{
    $this->assetHashService = $assetHashService;
    $this->pasteVariableRepository = $pasteVariableRepository;
}
```

app/Http/Controllers/Admin
- copy every file

app/Http/Controllers/Api/Client/Servers
- copy every file

app/Http/Requests/Admin
- copy every file

app/Http/Requests/Api/Client/Servers
- copy every file

# After Installation

Run these commands to apply database changes.
```
php artisan view:clear
php artisan config:clear
php artisan migrate --force

php artisan queue:restart
php artisan up

npm i -g yarn
yarn install
yarn add strip-ansi @types/strip-ansi
yarn run build:production
```
