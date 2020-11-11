# Laravel-Hasura Stack with MinIO Integration for Photo and Document Uploads

This is created for people who want to try `GraphQL` with [**Laravel**](https://laravel.com) and want to experience [**Hasura GraphQL Builder**](https://hasura.io).

In this boilerplate, Laravel has two kinds of API: (1) [`RESTful`](https://restfulapi.net) and (2) [`GraphQL`](https://graphql.org). GraphQL API is made possible by the [**Lighthouse**](https://lighthouse-php.com).

![Laravel Hasura Stack](../GatsbyLaravelHasuraStack/images/LaravelHasura.png)

Hasura requires 3rd party authentication which can be complemented by Laravel's `built-in authentication system`. Hasura supports two modes of authentication: (1) via [**webook**](https://hasura.io/docs/1.0/graphql/core/auth/authentication/index.html#webhook) and (2) via [**JWT**](https://hasura.io/docs/1.0/graphql/core/auth/authentication/index.html#jwt-json-web-token). In this boilerplate, I chose **JWT** mode to reduce roundtrips and to make [`subscriptions`](https://www.apollographql.com/docs/react/data/subscriptions/) possible.

![Hasura's JWT Mode Diagram](../GatsbyLaravelHasuraStack/images/HasuraJWTMode.jpg)

Thanks to [`Jose Luis Fonseca`](https://twitter.com/Joselfonseca)'s package ([*joselfonseca/lighthouse-graphql-passport-auth*](https://lighthouse-php-auth.com)), I was able to create Login, Register, and other authentication-related mutations instantly. You can follow his step-by-step tutorial from [here](https://dev.to/joselfonseca/graphql-auth-with-passport-and-lighthouse-php-14g5).

Also, thanks to [`Cor Bosman`](https://github.com/corbosman)'s package ([*corbosman/laravel-passport-claims*](https://github.com/corbosman/laravel-passport-claims)), I was able to add [custom claims](https://hasura.io/docs/1.0/graphql/core/auth/authentication/jwt.html#the-spec) to Laravel's JWT which are *required* by Hasura.

## Directory Structure

* `/laravel` - Laravel application
* `/nginx` - Configuration files for nginx to serve PHP
* `laravel.dockerfile` - Dockerfile to boostrap Laravel app
* `nginx.dockerfile` - Dockerfile to serve nginx website
* `hasura.dockerfile` - Dockerfile for Hasura GraphQL engine
* `docker-compose.yaml` - Composition of nginx + laravel + postgres to work together

## Running locally

**Requires Docker v2.x or higher**

In the root of this repository use `docker-compose up -d`. Once complete, go to `localhost:8080` to access the website!

As for Hasura, make sure to use the existing PostreSQL database. Follow this [instruction](https://hasura.io/docs/1.0/graphql/core/deployment/deployment-guides/docker.html#deployment-docker).

## Before Deploying to [KintoHub](https://www.kintohub.com)
**IMPORTANT**: Make sure to follow Fonseca's [tutorial](https://dev.to/joselfonseca/graphql-auth-with-passport-and-lighthouse-php-14g5) to generate `new app key`, run `fresh migrations` and to `update OAuth keys`. The security of your Laravel application depends on these keys so make sure to replace my keys. I usually update those stuff after I deployed everything to KintoHub so that I can connect to the database via [Kinto CLI](https://github.com/kintohub/kinto-cli). I then generate new app key (`php artisan key:generate`), run the migrations (`php artisan migrate:fresh --seed`), update OAuth keys (`php artisan passport:install`), then redeploy.

**Note**: You can always deploy this setup on other platforms like [Heroku](https://www.heroku.com), [DigitalOcean](https://www.digitalocean.com), etc. I just use KintoHub since this is where I first deployed it and since it uses Docker by default.

## Deploying on KintoHub

If you do not have an account, [signup](https://www.kintohub.com) first.

### Deploy a PostgreSQL Server

1. Click **Create Service** at the top right of your environment
2. Click **From Catalog** and then select **PostgreSQL**
3. Fill or generate your `username`, `password` and `root password` for your database
4. Click **Deploy** At the top right.

Once complete, go to the **Access** tab and copy the **Root User Connection String**. This will take several minutes to complete.

**Note**: You can deploy your PostgreSQL database elsewhere but make sure to install `pgcrypto` extension

### Deploy Laravel PHP App

1. Click **Create Service** at the top right of your environment
2. Choose **Backend API** from the list
3. Change the **Dockerfile Name** to `laravel.dockerfile`
4. Change the **Port** to `9000`
5. Open the **Environment Variables** tab and paste these values:

```
APP_DEBUG=true
APP_KEY=insert_new_app_key
APP_NAME=Laravel
DB_CONNECTION=pgsql
DB_PORT=5432
DB_DATABASE=insert_db_name
DB_HOST=insert_db_host
DB_USERNAME=insert_db_username
DB_PASSWORD=insert_db_password
LOG_CHANNEL=errorlog
MINIO_BUCKET=demo
MINIO_ENDPOINT=http://storage.jarcalculator.me:9000
MINIO_KEY=minioadmin
MINIO_REGION=us-east-1
MINIO_SECRET=minioadmin
PASSPORT_CLIENT_ID=insert_grant_client_id
PASSPORT_CLIENT_SECRET=insert_grant_client_secret
```

### Deploy Nginx Php Proxy

We need to deploy web host proxy to serve the php app on KintoHub.

1. Click **Create Service** at the top right of your environment
2. Choose **Web App** from the list
3. Change the **Dockerfile Name** to `nginx.dockerfile`
4. Change the **Port** to `80`
5. Open the Environment Variables tab to copy and paste the following into the **key** textbox:
6. Click **Deploy** at the top right

When complete, open the **Access** tab and open the external URL to see your Laravel app!

### Deploy Hasura GraphQL Engine

1. Click **Create Service** at the top right of your environment
2. Choose **Backend API** from the list
3. Change the **Dockerfile Name** to `hasura.dockerfile`
4. Change the **Port** to `8080`
5. Open the **Environment Variables** tab and paste these values:

```
HASURA_GRAPHQL_ADMIN_SECRET=12345678
HASURA_GRAPHQL_DATABASE_URL=postgresql://username:password@host:5432/database
HASURA_GRAPHQL_ENABLED_LOG_TYPES=startup,http-log,query-log,websocket-log,webhook-log
HASURA_GRAPHQL_ENABLE_CONSOLE=true
HASURA_GRAPHQL_JWT_SECRET={   "type":"RS256",   "key": "-----BEGIN PUBLIC KEY-----\nINSERT_PUBLIC_KEY_HERE\n-----END PUBLIC KEY-----" }
HASURA_GRAPHQL_UNAUTHORIZED_ROLE=anonymous
```

When complete, open the **Access** tab and open the external URL to see your Hasura app!