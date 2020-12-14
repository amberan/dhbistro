#!/usr/bin/env bash

sudo apt -qq -y install composer
composer global require symplify/easy-coding-standard jakub-onderka/php-parallel-lint \
	jakub-onderka/php-console-highlighter
chmod +x pre-commit
ln pre-commit ../.git/hooks/

