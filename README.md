# Pterodactyl Addon - MCPaste
Copy's the server console and sends to mcpaste.com

Support Discord: https://discord.gg/gBJfdzaBQb

## Before installing
1. Create an account on https://hm4.dev
2. Enable the MCPaste API module
3. Click on the MCPaste API tab on the left
4. Add a new site and enter the domain of your panel (e.g. if your panel is at "https://panel.amazing.host", you would enter "panel.amazing.host")
5. You'll get the token to use later

## Installation

### Preconditions
- NodeJS and NPM installed
- git installed

NOTE: Remember to back up your panel before installing any addons!

### Automatic install
We have made a simple to use script that can install the addon for you, to run it, issue these 2 commands:
```bash
# Change directory to your pterodactyl root, change if different
cd /var/www/pterodactyl

# Run the installer
bash <(curl -sL https://github.com/HM4Development/mcpaste-addon/releases/download/v2.1.0/install.sh)
```

### [Manual install](https://github.com/HM4Development/mcpaste-addon/blob/development/MANUAL_INSTALL.md)

## After installing
1. Copy the token created on https://hm4.dev
2. Go to the Admin portion of your panel
3. Click on the MCPaste tab on the left
4. Enter the token and click update!

### DONE
