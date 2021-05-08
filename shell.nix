{ pkgs ? (import <nixpkgs> {}), ... }:

let
  php' = pkgs.php80;

in pkgs.mkShell {
  buildInputs = [
    # Install PHP and composer
    php'
    php'.packages.composer

    # Install GNU Make for shorthands
    pkgs.gnumake

    # Install docker-compose
    pkgs.docker-compose

    # Install nodejs for PHP LSP
    pkgs.nodejs
  ];
}
