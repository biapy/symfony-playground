{
  config,
  pkgs,
  inputs,
  ...
}:

{
  name = "Symfony Playground";

  imports = [
    "${inputs.devenv-recipes}/devenv-scripts.nix"
    "${inputs.devenv-recipes}/git.nix"
    "${inputs.devenv-recipes}/devcontainer.nix"
    "${inputs.devenv-recipes}/markdown"
    "${inputs.devenv-recipes}/nix"
    "${inputs.devenv-recipes}/gitleaks.nix"
    "${inputs.devenv-recipes}/php"
    "${inputs.devenv-recipes}/php/symfony.nix"
    "${inputs.devenv-recipes}/database/postgresql.nix"
  ];

  # https://devenv.sh/basics/
  env = {
    POSTGRES_USER = "postgres";
    POSTGRES_PASSWORD = "password";
    POSTGRES_DB = "symfony-playground";
    POSTGRES_PORT = "5432";
    DATABASE_URL = "postgresql://${config.env.POSTGRES_USER}:${config.env.POSTGRES_PASSWORD}@127.0.0.1:${config.env.POSTGRES_PORT}/${config.env.POSTGRES_DB}?serverVersion=${config.services.postgres.package.version}&charset=utf8";
  };

  # https://devenv.sh/packages/
  packages = [ pkgs.git ];

  # https://devenv.sh/languages/
  # languages.rust.enable = true;

  # https://devenv.sh/processes/
  # processes.cargo-watch.exec = "cargo-watch";

  # https://devenv.sh/services/
  # services.postgres.enable = true;

  enterShell = ''
    git --version
  '';

  # https://devenv.sh/tasks/
  # tasks = {
  #   "myproj:setup".exec = "mytool build";
  #   "devenv:enterShell".after = [ "myproj:setup" ];
  # };

  # https://devenv.sh/tests/
  enterTest = ''
    echo "Running tests"
    git --version | grep --color=auto "${pkgs.git.version}"
  '';

  # https://devenv.sh/git-hooks/
  git-hooks.hooks.commitizen.enable = true;

  # See full reference at https://devenv.sh/reference/options/
}
