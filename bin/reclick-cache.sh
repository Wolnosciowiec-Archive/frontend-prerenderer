#!/bin/bash

find_urls() {
    sqlite3 ./data/storage.sqlite3 "SELECT * FROM urls_visited_by_crawlers;" | cut -d"|" -f2
}

call_url() {
    curl -s -X GET \
      "$1" \
      -H 'cache-control: no-cache' \
      -H 'X-Frontend-Prerenderer: no-cache' \
      -A "curl/reclick-cache" > /dev/null
}

main() {
    urls=$(find_urls)
    total=$(echo $urls | wc -l)
    current=0

    echo "Total: $total url(s) visited by crawlers"
    echo "Revisiting all of those urls"
    echo "========================================"
    echo ""

    for url in ${urls}; do
        current=$((current+1))
        echo "[$current/$total] $url"
        call_url "$url"
    done
}

main
