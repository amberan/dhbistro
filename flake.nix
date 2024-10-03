{
    description = "Bistro localy, services included";
    inputs = {
        nixpkgs.url      = "github:NixOS/nixpkgs/nixos-unstable";
        flake-utils.url  = "github:numtide/flake-utils";
        phps.url = "github:loophp/nix-shell";
    };

    outputs = { self, nixpkgs, flake-utils, phps, ... }:
        flake-utils.lib.eachDefaultSystem (system:
        let
            pkgs = import nixpkgs {
                inherit system;
                overlays = [
                    phps.overlays.default
                ];
            };
            php = pkgs.php83;
        in
        with pkgs;
        {

            devShells.default = mkShell {
                buildInputs = [
                    php
                    php.packages.composer
                    apacheHttpd
                    # TODO apache2 + mods
                        # - https://github.com/totten/locolamp/blob/master/default.nix
                        # - https://discourse.nixos.org/t/tutorial-for-setting-up-the-lamp-stack-on-a-nixos-server/12508/4
                    mariadb # https://jeancharles.quillet.org/posts/2022-01-30-Local-mariadb-server-with-nix-shell.html
                ];

                shellHook = ''
                    MARIADB_BASEDIR=${pkgs.mariadb}
                    MARIADB_HOME="$PWD/mariadb"
                    MARIADB_DATADIR="$MARIADB_HOME/data"
                    export MARIADB_UNIX_PORT="$MARIADB_HOME/mariadb.sock"
                    MARIADB_PID_FILE="$MARIADB_HOME/mariadb.pid"
                    alias mariadb='mariadb -u root'
                    alias mysql=mariadb

                    if [ ! -d "$MARIADB_HOME" ]; then
                        mariadb-install-db --no-defaults --auth-root-authentication-method=normal \
                        --datadir="$MARIADB_DATADIR" --basedir="$MARIADB_BASEDIR" \
                        --pid-file="$MARIADB_PID_FILE"
                    fi

                    mariadbd --no-defaults --datadir="$MARIADB_DATADIR" --pid-file="$MARIADB_PID_FILE" \
                    --socket="$MARIADB_UNIX_PORT" 2> "$MARIADB_HOME/mariadb.log" &
                    MARIADB_PID=$!

                    finish()
                    {
                        mariadb-admin -u root --socket="$MARIADB_UNIX_PORT" shutdown
                        pkill php
                        kill $MARIADB_PID
                        wait $MARIADB_PID
                    }
                    trap finish EXIT

                    composer -n -d source install

                    mariadb -u root -e "CREATE DATABASE bistro; CREATE USER bistro@localhost IDENTIFIED BY 'bistro'; GRANT ALL PRIVILEGES ON bistro.* TO bistro@localhost; FLUSH PRIVILEGES;"

                    # https://www.php.net/manual/en/features.commandline.webserver.php
                    php -S localhost:8000 -t source &
                '';
            };
        }
    );
}
