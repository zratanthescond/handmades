{ pkgs ? import <nixpkgs> {} }:
let
  yarnNoLicense = pkgs.yarn.overrideAttrs (old: {
    postInstall = (old.postInstall or "") + ''
      rm -f $out/LICENSE
    '';
  });

  composerNoLicense = pkgs.composer.overrideAttrs (old: {
    postInstall = (old.postInstall or "") + ''
      rm -f $out/LICENSE
    '';
  });
in
pkgs.mkShell {
  buildInputs = [
    pkgs.php
    pkgs.phpExtensions.curl
    pkgs.phpExtensions.mbstring
    pkgs.phpExtensions.intl
    pkgs.phpExtensions.pdo_mysql
    pkgs.phpExtensions.xml
    pkgs.phpExtensions.zip
    pkgs.phpExtensions.gd
    composerNoLicense
    pkgs.nodejs
    yarnNoLicense
  ];
}
