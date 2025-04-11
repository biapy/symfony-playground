#!/bin/bash

WORKSPACE_FOLDER="$(command pwd)"

TOOLS_FOLDER="${WORKSPACE_FOLDER}/tools"

function add-to-path() {
  [[ ${#} -ne 1 ]] && echo "Usage: add-to-path <path>" && return 1
  local path="${1}"

  # Check if path exists
  [[ -n "${path}" || -d "${path}" ]] || return 1

  # Check if vendor_path is already in PATH
  [[ ":${PATH}:" != *":${path}:"* ]] || return 0

  # Add path to PATH in user profile and bashrc
  echo "PATH=\"${path}:\$PATH\"" >>"${HOME}/.profile"
  echo "PATH=\"${path}:\$PATH\"" >>"${HOME}/.bashrc"

  # Add vendor_path to PATH
  export PATH="${path}:${PATH}"
}


function add-vendor-bin-to-path() {
  [[ ${#} -ne 1 ]] && echo "Usage: add-vendor-bin-to-path <path>" && return 1
  local path="${1}"
  local vendor_path="${path}/vendor/bin"

  # Check if path exists
  [[ -n "${path}" || -d "${path}" ]] || return 1

  # Check if vendor_path exists
  [[ ! -d "${vendor_path}" ]] && echo "No vendor/bin directory found in '${path}'" && return 1

  # add to path
  add-to-path "${vendor_path}" || return 1
}



# Add tools to PATH
add-vendor-bin-to-path "${TOOLS_FOLDER}/phpstan"
add-vendor-bin-to-path "${TOOLS_FOLDER}/psalm"
add-vendor-bin-to-path "${TOOLS_FOLDER}/rector"
add-vendor-bin-to-path "${TOOLS_FOLDER}/php-cs-fixer"
add-vendor-bin-to-path "${TOOLS_FOLDER}/phpcs"
add-vendor-bin-to-path "${TOOLS_FOLDER}/phpmd"

# Add project bin to PATH
add-vendor-bin-to-path "${WORKSPACE_FOLDER}"
add-to-path "${WORKSPACE_FOLDER}/bin"

# Clear cache and warmup
php bin/console cache:clear
php bin/console cache:warmup

# Create the database
php bin/console doctrine:database:create --if-not-exists

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Install nvm
# nvm install --lts
