# Pterodactyl Addon - MCPaste
Copy's the server console and sends to mcpaste.com


Place the " McPaste.tsx " in " resources/components/server "

ServerConsole.tsx -> add import
import McPaste from "@/components/server/McPaste";

ServerConsole.tsx -> replace
<Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
   <PowerControls/>
</Can>

With

<div>
	<Can action={[ 'control.start', 'control.stop', 'control.restart' ]} matchAny>
		<PowerControls/>
	</Can>
	<McPaste />
</div>
