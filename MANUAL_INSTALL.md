# Addon Installation

## Note
The term `copy every file` means to look into this repository and find the file mentioned by that path and copy every file from there to that same path in your panel

E.g. if it says `copy every file` under `database/migrations`, you should go under `database/migrations` in this repository and copy every file from there into `database/migrations` under your panel's root directory

---

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
- after line 32
```php
@if(!empty($mcPasteData))
    <script>
        window.MCPasteData = {!! json_encode($mcPasteData) !!};
    </script>
@endif
```

resources/scripts/api/server
- copy every file

resources/scripts/components/server/console/McPaste.tsx
- copy the whole file

resources/scripts/components/server/console/ServerDetailsBlock.tsx
- at top of file
```tsx
import McPaste, { mcPasteData, mcPasteStyle } from '@/components/server/McPaste';
```
- after like 93
```tsx
{ mcPasteData.tokenValid && mcPasteStyle.buttonLocation === "component" && <McPaste position={'component'} /> }
```

resources/scripts/components/server/console/Console.tsx
- at top of file
```tsx
import McPaste, { mcPasteData, mcPasteStyle } from '@/components/server/console/McPaste';
```
- after line 225
```tsx
{ mcPasteData.tokenValid && mcPasteStyle.buttonLocation === "commandLine" &&
    <div className={classNames("flex items-center top-0 right-0 absolute z-10 select-none h-full px-3 transition-colors duration-100")}>
        <McPaste position={'commandLine'} />
    </div>
}
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
yarn add strip-ansi @types/strip-ansi
yarn install
yarn run build:production
```
