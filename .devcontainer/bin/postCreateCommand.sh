#!/bin/bash

WORKSPACE_FOLDER="$(command pwd)"

TOOLS_FOLDER="${WORKSPACE_FOLDER}/tools"

function add-to-path() {
  [[ ${#} -ne 1 ]] && echo "Usage: add-to-path <path>" && return 1
  local path = "${1}"
  local vendor_path = "${path}/vendor/bin"

  # Check if path exists
  [[ -n "${path}" || -d "${path}" ]] || return 1

  # Check if vendor_path exists
  [[ ! -d "${vendor_path}" ]] && echo "No vendor/bin directory found in '${path}'" && return 1

  # Check if vendor_path is already in PATH
  [[ ":${PATH}:" != *":${vendor_path}:"* ]] || return 0

  # Add path to PATH in user profile
	echo "PATH=\"${path}:\$PATH\"" >> "${HOME}/.profile"

 # Add vendor_path to PATH
	export PATH="${vendor_path}:${PATH}"
}

# Add tools to PATH
add-to-path "${TOOLS_FOLDER}/phpstan"
add-to-path "${TOOLS_FOLDER}/psalm"
add-to-path "${TOOLS_FOLDER}/rector"
add-to-path "${TOOLS_FOLDER}/php-cs-fixer"
add-to-path "${TOOLS_FOLDER}/phpcs"
add-to-path "${TOOLS_FOLDER}/phpmd"

# Add project bin to PATH
add-to-path "${WORKSPACE_FOLDER}/bin"

# Clear cache and warmup
php bin/console cache:clear
php bin/console cache:warmup

# Create the database
php bin/console doctrine:database:create --if-not-exists

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Install nvm
# nvm install --lts
