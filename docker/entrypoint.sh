#!/bin/sh

cd /app

path_file_md5_composer='/app/composer.json.md5'

function update_composer () {
    composer update
    composer dump-autoload
    echo $(md5sum /app/composer.json) > $path_file_md5_composer
}

[ -e $path_file_md5_composer ] && {
    update_compose
}

md5_actual=$(md5sum /app/composer.json)
md5_expect=$(cat $path_file_md5_composer)

[ "${md5_actual}" = "${md5_expect}" ] || {
    update_composer
}

/bin/sh
