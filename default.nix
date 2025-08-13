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
    pkgs.nodejs
    yarnNoLicense
    pkgs.php
    composerNoLicense
  ];
}
