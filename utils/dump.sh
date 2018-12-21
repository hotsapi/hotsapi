#!/usr/bin/env bash
set -e
set -x

# sudo apt install -y mysql-client

# Generated files:
# https://storage.googleapis.com/hotsapi/db/schema/heroes.sql.gz
# https://storage.googleapis.com/hotsapi/db/schema/schema.sql
# https://storage.googleapis.com/hotsapi/db/data/replays.csv.gz
# https://storage.googleapis.com/hotsapi/db/data/bans.csv.gz
# https://storage.googleapis.com/hotsapi/db/data/players.csv.gz
# https://storage.googleapis.com/hotsapi/db/data/scores.csv.gz
# https://storage.googleapis.com/hotsapi/db/data/player_talent.csv.gz
# https://storage.googleapis.com/hotsapi/db/data/max_parsed_id

DB_HOST=slave.gc.db.hotsapi.net
DB_USER=root

FROM=$(gsutil cp gs://hotsapi/db/data/max_parsed_id -)
TO=$(mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -sse "select max(parsed_id) from hotsapi.replays")

# TODO handle timeout on tasks
gcloud sql export csv hotsapi-master-3-replica --database=hotsapi gs://hotsapi/db/export/replays.csv       --query="select * from replays where parsed_id > $FROM and parsed_id <= $TO"
gcloud sql export csv hotsapi-master-3-replica --database=hotsapi gs://hotsapi/db/export/bans.csv          --query="select * from bans where replay_id in (select id from replays where parsed_id > $FROM and parsed_id <= $TO)"
gcloud sql export csv hotsapi-master-3-replica --database=hotsapi gs://hotsapi/db/export/players.csv       --query="select * from players where replay_id in (select id from replays where parsed_id > $FROM and parsed_id <= $TO)"
gcloud sql export csv hotsapi-master-3-replica --database=hotsapi gs://hotsapi/db/export/scores.csv        --query="select * from scores where id in (select id from players where replay_id in (select id from replays where parsed_id > $FROM and parsed_id <= $TO))"
gcloud sql export csv hotsapi-master-3-replica --database=hotsapi gs://hotsapi/db/export/player_talent.csv --query="select * from player_talent where player_id in (select id from players where replay_id in (select id from replays where parsed_id > $FROM and parsed_id <= $TO))"

for file in replays bans players scores player_talent; do

  # Download exported file, fix null values and compress, upload the result
  # Double sed because it doesn't support backtracking and rescans, to handle `,"N,"N,`
  gsutil cp gs://hotsapi/db/export/$file.csv - | sed 's/,"N,/,\\N,/g' | sed 's/,"N,/,\\N,/g' | sed 's/^"N,/\\N,/g' | sed 's/,"N$/,\\N/g' | gzip | gsutil cp - gs://hotsapi/db/processed/$file.csv.gz

  # Combine previous dump with incremental result
  gsutil compose gs://hotsapi/db/data/$file.csv.gz gs://hotsapi/db/processed/$file.csv.gz gs://hotsapi/db/stage/$file.csv.gz

  # Remove temp files
  gsutil rm gs://hotsapi/db/export/$file.csv gs://hotsapi/db/processed/$file.csv.gz

done

# Upload new max parsed id
echo $TO | gsutil cp - gs://hotsapi/db/stage/max_parsed_id

# Replace old seed data with newly generated one
gsutil rm -r gs://hotsapi/db/data
gsutil mv gs://hotsapi/db/stage gs://hotsapi/db/data
gsutil acl ch -r -u AllUsers:R gs://hotsapi/db/data

# Update schema files
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD --add-drop-table hotsapi maps map_translations heroes abilities talents hero_talent hero_translations | gzip | gsutil cp -a public-read - gs://hotsapi/db/schema/heroes.sql.gz
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD --no-data hotsapi replays bans players scores player_talent | gzip | gsutil cp -a public-read - gs://hotsapi/db/schema/schema.sql.gz
