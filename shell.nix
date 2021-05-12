{ pkgs ? (import <nixpkgs> {}), ... }:

let
  php' = pkgs.php80;

in pkgs.mkShell {
  buildInputs = [
    # Install PHP and composer
    php'
    php'.packages.composer

    # Install code style tools
    php'.packages.phpcbf
    php'.packages.phpcs

    # Install phpstan
    php'.packages.phpstan

    # Install GNU Make for shorthands
    pkgs.gnumake

    # Install yaml lint
    pkgs.yamllint

    # Install docker-compose
    pkgs.docker-compose

    # Install nodejs for PHP LSP
    pkgs.nodejs
  ];
}
