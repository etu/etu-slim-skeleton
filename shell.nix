{ pkgs ? (import <nixpkgs> {}), ... }:

let
  php' = pkgs.php80;

in pkgs.mkShell {
  buildInputs = [
    # Install PHP and composer
    php'
    php'.packages.composer

    # Install docker-compose
    pkgs.docker-compose
  ];
}
