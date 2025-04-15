# [Choice] PHP version (use -bookworm variants on local arm64/Apple Silicon): 8, 8.4, 7, 7.4, 7.3, 8-bookworm, 8.4-bookworm
ARG VARIANT=8.4
ARG SYMFONY_CLI_VERSION=5.11.0
ARG NODE_VERSION=22


FROM mcr.microsoft.com/devcontainers/php:${VARIANT}

ARG SYMFONY_CLI_VERSION
ARG NODE_VERSION

# Install command-line tools
ADD https://github.com/dandavison/delta/releases/download/0.18.2/git-delta_0.18.2_amd64.deb /tmp/git-delta.deb
RUN apt-get 'update' && export DEBIAN_FRONTEND=noninteractive \
	&& apt-get --assume-yes install --no-install-recommends  'git' 'wget' 'gosu' \
	'ca-certificates' 'curl' 'fd-find' 'ripgrep' 'jq' 'unzip' 'zip' 'fzf' \
	'neovim' 'supervisor' \
	&& apt-get --assume-yes install --no-install-recommends '/tmp/git-delta.deb' \
	&& apt-get 'clean' \
	&& rm -rf '/var/lib/apt/lists/'* '/tmp/git-delta.deb'

# Install uv
# see https://docs.astral.sh/uv/guides/integration/docker/
COPY --from=ghcr.io/astral-sh/uv:latest /uv /uvx /bin/

# PHP configuration
COPY ./docker/php-conf.d/memory-limit.ini /usr/local/etc/php/conf.d/memory-limit.ini
COPY ./docker/php-conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Apache2 hosts
COPY ./docker/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache2/sites-available/pgadmin.conf /etc/apache2/sites-available/pgadmin.conf
COPY ./docker/apache2/sites-available/mailpit.conf /etc/apache2/sites-available/mailpit.conf
COPY ./docker/apache2/sites-available/symfony-playground.conf /etc/apache2/sites-available/symfony-playground.conf

RUN a2enmod proxy proxy_http \
	&& a2ensite pgadmin.conf mailpit.conf symfony-playground.conf

# Supervisor configuration
COPY ./docker/supervisord/apache2.conf /etc/supervisor/conf.d/apache2.conf

# Custom entrypoint
COPY ./docker/bin/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

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

# Add vscode user to adm group for access to nginx logs
RUN usermod --append --groups 'adm' 'vscode'

USER vscode

# install Posting (will also quickly install Python 3.13 if needed)
# see https://github.com/darrenburns/posting
RUN uv 'tool' 'install' --python '3.13' 'posting'

# Add Composer global bin to PATH
ENV PATH="/home/vscode/.composer/vendor/bin:${PATH}"
