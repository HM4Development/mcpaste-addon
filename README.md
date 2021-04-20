# Pterodactyl Addon - MCPaste
Copy's the server console and sends to mcpaste.com


Place the " McPaste.tsx " in " /var/www/pterodactyl/resources/scripts/components/server "

ServerConsole.tsx -> add import 
```js
import McPaste from "@/components/server/McPaste";
```

ServerConsole.tsx -> replace
```html
<Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
   <PowerControls/>
</Can>
```

With

```html
<div>
	<Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
		<PowerControls/>
	</Can>
	<McPaste />
</div>
```

After all code inserted. Please run this commands:
- npm i -g yarn
- cd /var/www/pterodactyl
- yarn install
- yarn run build:production
