# [Choice] PHP version (use -bookworm variants on local arm64/Apple Silicon): 8, 8.4, 7, 7.4, 7.3, 8-bookworm, 8.4-bookworm
ARG VARIANT=8.4
ARG SYMFONY_CLI_VERSION=5.11.0
ARG NODE_VERSION=22


FROM mcr.microsoft.com/devcontainers/php:${VARIANT}

ARG SYMFONY_CLI_VERSION
ARG NODE_VERSION

COPY ./php-conf.d/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY ./php-conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install command-line tools
RUN apt-get 'update' && export DEBIAN_FRONTEND=noninteractive \
	&& apt-get --assume-yes install --no-install-recommends  'git' 'wget' \
	'ca-certificates' 'curl' 'fd-find' 'ripgrep' 'jq' 'unzip' 'zip' 'fzf' \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

# Install Symfony CLI
RUN set -eux \
	&& wget --quiet --output-document="/tmp/symfony-cli.deb" \
	"https://github.com/symfony-cli/symfony-cli/releases/download/v${SYMFONY_CLI_VERSION}/symfony-cli_${SYMFONY_CLI_VERSION}_amd64.deb" \
	&& apt-get install --assume-yes --no-install-recommends "/tmp/symfony-cli.deb" \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/* \
	&& rm "/tmp/symfony-cli.deb"

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest \
	'/usr/bin/install-php-extensions' '/usr/bin/install-php-extensions'

# Add PHP extensions
RUN set -eux \
	&& install-php-extensions @composer "intl" "pcov" "apcu" "zip" "opcache" \
	"pdo_pgsql"

# Add PHPDoc
COPY --from=phpdoc/phpdoc:stable "/opt/phpdoc" "/opt/phpdoc"
ENV PATH="/opt/phpdoc/bin:${PATH}"

# Install PHP CS Fixer
COPY --link --from=ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3 "/fixer" "/fixer"
ENV PHP_CS_FIXER_IGNORE_ENV=1
RUN set -eux \
	&& ln -s /fixer/php-cs-fixer /usr/local/bin/php-cs-fixer

# Install PHPUnit
ADD "https://phar.phpunit.de/phpunit-11.5.phar" \
	"/usr/local/bin/phpunit"

RUN chmod ugo+rx "/usr/local/bin/phpunit"

# Persist command history
RUN SNIPPET="export PROMPT_COMMAND='history -a' && export HISTFILE='/commandhistory/vscode/.bash_history'" \
	&& mkdir --parent "/commandhistory/vscode" \
	&& touch "/commandhistory/vscode/.bash_history" \
	&& chown -R "vscode" "/commandhistory/vscode" \
	&& echo "${SNIPPET}" >> "/home/vscode/.bashrc" \
	&& chown "vscode" "/home/vscode/.bashrc"

USER vscode

# Add Composer global bin to PATH
ENV PATH="/home/vscode/.composer/vendor/bin:${PATH}"
