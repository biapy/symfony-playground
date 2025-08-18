{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

{
  name = "Symfony Playground";

  imports = [
    "${inputs.devenv-recipes}/devenv-scripts.nix"
    "${inputs.devenv-recipes}/git.nix"
    "${inputs.devenv-recipes}/devcontainer.nix"
    "${inputs.devenv-recipes}/markdown.nix"
    "${inputs.devenv-recipes}/nix.nix"
    "${inputs.devenv-recipes}/gitleaks.nix"
    "${inputs.devenv-recipes}/php"
    "${inputs.devenv-recipes}/php/symfony.nix"
  ];

  # https://devenv.sh/basics/
  env.GREET = "devenv";

  # https://devenv.sh/packages/
  packages = [ pkgs.git ];

  # https://devenv.sh/languages/
  # languages.rust.enable = true;

  # https://devenv.sh/processes/
  # processes.cargo-watch.exec = "cargo-watch";

  # https://devenv.sh/services/
  # services.postgres.enable = true;

  # https://devenv.sh/scripts/
  scripts.hello.exec = ''
    echo hello from $GREET
  '';

  enterShell = ''
    hello
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
  # git-hooks.hooks.shellcheck.enable = true;

  # See full reference at https://devenv.sh/reference/options/
}
