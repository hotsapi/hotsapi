@extends('template')
@section('title', 'HotsApi')

@section('content')
    <h3>There are 3 primary Hotsapi usage scenarios:</h3>

    <ol>
        <li>
            <p><strong>A public dataset on Google BigQuery.</strong> If you want to quickly run some queries on Hotsapi
                dataset without setting up your own database instance it can be using this dataset. Those queries are
                done in a familiar SQL and can be of any complexity without worrying about server performance.
                It functions in "requester pays" mode where Hotsapi pays only for data storage and users pay for the
                queries performed. Most casual users will probably fit into 1Tb/month free usage tier (though will still
                need to activate google cloud account to use it). This can be useful for ad hoc queries, posts
                like "patch ... 7 days later", and similar data mining efforts.</p>
        </li>

        <li>
            <p><strong>A stream of parsed match details objects.</strong> If you only need the data that is already
                extracted by Hotsapi you can save on parsing replay files. You will need to keep your own database with
                downloaded replay data, and run all their queries against it. First you need to download a seed database
                dump and import it into your sql server. Then periodically poll <code>/replays/parsed</code> endpoint
                with <code>min_parsed_id</code> to get newly parsed replay data.</p></li>

        <li><p><strong>A stream of raw replay files.</strong> If you need to extract some advanced data from replay
                files you can parse them yourself. First you will need to do batch download and parse existing files
                from our AWS S3 storage. The storage functions in "requester pays" mode where Hotsapi pays only for data
                storage and users pay for file downloads, downloads are free within the same AWS region (eu-west-1).
                Then you need to periodically poll <code>/repays</code> endpoint to get new replays. </p></li>
    </ol>

    <h3>Interacting with BigQuery</h3>

    <p>Our public dataset has id <code>cloud-project-179020:hotsapi</code> and can be found <a
            href="https://bigquery.cloud.google.com/dataset/cloud-project-179020:hotsapi">here</a>. You will need to
        create Google Cloud account to use it. Queries against it cost $5 per Tb of data processed and the first
        Tb/month is free (most likely all your queries will fit into free tier). BigQuery like all data warehouses uses
        columnar storage format so it doesn't matter much how complex your query is but it does matter how many <em>columns</em>
        you are looking at.</p>

    <p>Using BigQuery doesn't require you to install any software/servers, you can perform all queries from the web UI.
        It uses a dialect of SQL so it's easy to quickly start querying hotsapi data.</p>

    <p>Hotsapi dataset contains denormalized data: a whole replay info is stored in a single table using nested columns.
        In a way it is similar to document (json) databases like Mongo. A human readable yaml schema of tables can be
        found <a href="https://github.com/poma/hotsapi/blob/master/utils/schema/schema.yml">here</a>.</p>

    <h3>Database dumps</h3>

    <p>Since importing a full database dump in .sql format can take days or weeks, we split the dump into few parts:</p>

    <ul>
        <li><code>heroes.sql</code> that contains small tables with data like maps, heroes, talents, and translations
        </li>

        <li><code>schema.sql</code> that contains schema for big tables but no data</li>

        <li>A set of <code>.csv</code> files that contain data for big tables. Those files are updated daily.</li>
    </ul>

    <p>The CSV files contain only parsed replays and are append-only since the data after parsing is mostly immutable
        (except some unimportant flags like <code>deleted</code> that will be out of sync with hotsapi current state).
        CSV files can be imported into a MySQL isntance using <code>LOAD DATA INFILE</code> statement, which works
        significantly faster than loading .sql files. Keep in mind that uncompressed data for MySQL instance can take
        more than 10x size of compressed .csv files. <code>max_parsed_id</code> file contains maximum
        <code>parsed_id</code> contained in this dump. Here's a full list of files for seed DB:</p>

    <pre><code>https://storage.googleapis.com/hotsapi/db/schema/heroes.sql.gz
https://storage.googleapis.com/hotsapi/db/schema/schema.sql.gz
https://storage.googleapis.com/hotsapi/db/data/replays.csv.gz
https://storage.googleapis.com/hotsapi/db/data/bans.csv.gz
https://storage.googleapis.com/hotsapi/db/data/players.csv.gz
https://storage.googleapis.com/hotsapi/db/data/scores.csv.gz
https://storage.googleapis.com/hotsapi/db/data/player_talent.csv.gz
https://storage.googleapis.com/hotsapi/db/data/max_parsed_id
</code></pre>

    <h3>Downloading replay files</h3>

    <p>All files are stored on <a href="https://aws.amazon.com/s3/">Amazon S3</a> service. Currently it is in <a
            href="http://docs.aws.amazon.com/AmazonS3/latest/dev/RequesterPaysBuckets.html">"Requester pays" mode</a>
        which means that traffic fees are payed by clients that download files instead of website owner. This allows us
        to keep server costs low and run service even with low donations. S3 traffic is <em>free</em> if you download to
        an AWS EC2 instance in EU (Ireland) (eu-west-1) region or $0.09/GB if you download to non-amazon server. A good
        way for you to avoid costs is to launch a free tier EC2 instance, use it to download and analyze replays, and
        then stream results to your main website. In any case you will need an AWS account and authenticate every
        request to download files. Further documentation can be found <a
            href="http://docs.aws.amazon.com/AmazonS3/latest/dev/RequesterPaysBuckets.html">here</a>. If your downloads
        fail make sure you didn't forget to include <code>x-amz-request-payer</code> in your request header or make
        corresponding setting in your SDK.</p>

    <p>To save costs we delete the old replay files from storage. The metadata in database is stored forever. We
        currently retain files fitting at least one of the following criteria:</p>

    <ul>
        <li>1 week after upload</li>

        <li>1 month after the game took place</li>

        <li>3 months after the game took place for Ranked games</li>
    </ul>

    <h3>API reference</h3>

    <p>Interactive API documentation can be found on <a href="{{ url('swagger') }}">this</a> page</p>
@endsection
