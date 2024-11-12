{
  description = "Bistro localy, services included";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
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
        formatter = nixpkgs-fmt;

        devShells.default = mkShell {
          buildInputs = [
            php
            php.packages.composer
            mariadb
          ];

          shellHook = ''
            MARIADB_BASEDIR=${pkgs.mariadb}
            MARIADB_HOME="$PWD/mariadb"
            MARIADB_UNIX_PORT="$MARIADB_HOME/mariadb.sock"
            export COMPOSER_HOME="$PWD/.composer"

            MARIADB_PID_FILE="$PWD/logs/.mariadb.pid"
            MARIADB_LOG_FILE="$PWD/logs/mysql.log"
            PHP_PID_FILE="$PWD/logs/.php.pid"
            PHP_LOG_FILE="$PWD/logs/php.log"

            alias mariadb='${pkgs.mariadb}/bin/mariadb -u root'
            alias mysql=mariadb

            if [ ! -d "$MARIADB_HOME" ]; then
                echo "Initializing MariaDB database..."
                mkdir -p "$MARIADB_HOME"
                ${pkgs.mariadb}/bin/mysql_install_db --no-defaults --auth-root-authentication-method=normal \
                    --datadir="$MARIADB_HOME" --basedir="$MARIADB_BASEDIR" \
                    --pid-file="$MARIADB_PID_FILE"
                echo "Database initialized!"
            fi

            # Start MariaDB
            echo "Starting MariaDB..."
            ${pkgs.mariadb}/bin/mariadbd-safe --datadir=$MARIADB_HOME \
                                        --socket=$MARIADB_UNIX_PORT \
                                        --pid-file=$MARIADB_PID_FILE \
                                        --log-error=$MARIADB_LOG_FILE \
                                        --port=3306 \
                                        &

            # Wait for MariaDB to start
            while ! ${pkgs.mariadb}/bin/mysqladmin ping --socket=$MARIADB_UNIX_PORT --silent; do
                sleep 1
            done

            if [ ! -d ./mariadb/bistro/ ]; then
                echo "Creating database for bistro ..."
                mariadb -u root -e "CREATE DATABASE bistro; CREATE USER bistro@localhost IDENTIFIED BY 'bistro'; GRANT ALL PRIVILEGES ON bistro.* TO bistro@localhost; FLUSH PRIVILEGES;"
            fi

            # Function to stop MariaDB && PHP
            function cleanup {
                if [ -f "$MARIADB_PID_FILE" ]; then
                    echo "Stopping MariaDB..."
                    kill $(cat "$MARIADB_PID_FILE")
                    rm -f "$MARIADB_PID_FILE"
                fi
                if [ -f "$PHP_PID_FILE" ]; then
                    echo "Stopping PHP..."
                    kill $(cat "$PHP_PID_FILE")
                    rm -f "$PHP_PID_FILE"
                fi
            }
            # Register cleanup for interactive shells
            if [[ $- == *i* ]]; then
                trap cleanup EXIT
            fi




            composer -n -d source install

            ${pkgs.php}/bin/php -S localhost:8000 -t source > "$PHP_LOG_FILE" 2>&1 &
            echo $! > "$PHP_PID_FILE"

            echo ""
            echo "Commands available:"
            echo "  - mariadb or mysql : MySQL client"
            echo "Logs are in ./logs/"


          '';
        };
      }
    );
}
