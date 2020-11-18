# Laravel + Hasura JWT Stack (November 2020)

This is created for people who want to try **GraphQL** with [**Laravel**](https://laravel.com) and want to experience [**Hasura GraphQL Builder**](https://hasura.io).

In this boilerplate, Laravel has two kinds of API: (1) [`RESTful`](https://restfulapi.net) and (2) [`GraphQL`](https://graphql.org). GraphQL API is made possible by the [**Laravel Lighthouse**](https://lighthouse-php.com).

## Laravel Features

- [x] GraphQL-ready
- [x] GraphQL JWT Authentication System (Login, Register, Social Login, etc.)
- [x] Added JWT Claims for Hasura GraphQL Server
- [x] Multi-Roles for Users with Approval and Access Level Logic
- [x] Anonymous Role for Newly Registered Users with Role Under Approval
- [x] MinIO File Storage Integration
- [x] RESTful API route for Photo Upload
- [x] RESTful API route for Multiple Photo Upload
- [x] RESTful API route for Document Upload
- [x] RESTful API route for Multiple Document Upload
- [ ] Stripe Integration
- [ ] Frontend (Planning to add Gatsby React)

## Boilerplate Structure

![Laravel Hasura Stack](./images/LaravelHasura.png)

Hasura _requires_ 3rd party authentication which can be complemented by Laravel's `built-in authentication system`. Hasura supports two modes of authentication: (1) via [`webook`](https://hasura.io/docs/1.0/graphql/core/auth/authentication/index.html#webhook) and (2) via [`JWT`](https://hasura.io/docs/1.0/graphql/core/auth/authentication/index.html#jwt-json-web-token). In this boilerplate, I chose **JWT mode** to reduce roundtrips and to make [`subscriptions`](https://www.apollographql.com/docs/react/data/subscriptions/) possible.

![Hasura's JWT Mode Diagram](./images/HasuraJWTMode.jpg)

Thanks to [**Jose Luis Fonseca**](https://twitter.com/Joselfonseca)'s package ([_joselfonseca/lighthouse-graphql-passport-auth_](https://lighthouse-php-auth.com)), I was able to create Login, Register, and other authentication-related mutations instantly. You can follow his step-by-step tutorial from [here](https://dev.to/joselfonseca/graphql-auth-with-passport-and-lighthouse-php-14g5).

Also, thanks to [**Cor Bosman**](https://github.com/corbosman)'s package ([_corbosman/laravel-passport-claims_](https://github.com/corbosman/laravel-passport-claims)), I was able to add [`custom claims`](https://hasura.io/docs/1.0/graphql/core/auth/authentication/jwt.html#the-spec) to Laravel's JWT which are _required_ by Hasura.

## Directory Structure

- `/laravel` - Laravel application
- `/nginx` - Configuration files for nginx to serve PHP
- `laravel.dockerfile` - Dockerfile to boostrap Laravel app
- `nginx.dockerfile` - Dockerfile to serve nginx website
- `hasura.dockerfile` - Dockerfile for Hasura GraphQL engine
- `docker-compose.yaml` - Composition of nginx + laravel + postgres to work together

## Running locally

**Requires Docker v2.x or higher**

In the root of this repository run:

```
docker-compose up -d
```

It will initialize **PostgreSQL** and **Hasura**. As for Laravel, run it normally and make sure to use the existing PostreSQL database with Hasura. Also, if you have MinIO, replace all MinIO related environment variables on Laravel's env file.

If your Postgres and Hasura Server are up and running, follow these instructions:

**Step 1**: Make sure to `generate your own app key` before anything else. App Key will be used for all sorts of encryption within Laravel.

```
php artisan key:generate
```

![Generate New App Key](./images/01_AppKey.png)
**Step 2**: Run fresh migrations to create all necessary tables and seed the roles table as well.

```
php artisan migrate:fresh --seed
```

![Run Fresh Migrations](./images/02_Migrate.png)
**Step 3**: You have to generate your own OAuth Keys as well. To remove mine, just use `--force`. Make sure to paste the new grant client id and secret to Laravel's env file.

```
php artisan passport:install --force
```

![Generate New OAuth Keys](./images/03_OAuthKeys.png)
**Step 4**: Make sure to refresh config cache to utilize the new keys.

```
php artisan config:cache
```

![Clear Configs](./images/04_ClearConfigs.png)
**Step 5**: Run the app to test our GraphQL API.

```
php artisan serve
```

![Serve the application](./images/05_Serve.png)
**Step 6**: If we will try to register, the role id is required. We can look for the role id's via Laravel Tinker or by querying the roles on Laravel's GraphQL Playground.

### Via Laravel Tinker

![Roles on Tinker](./images/06_Tinker.png)

### Via GraphQL Playground Roles Query

```
http://localhost:8000/graphql-playground
```

![Roles on Playground](./images/07_PlaygroundQuery.png)
**Step 7**: Since we now have the role of the user (which will be approved immediately), we can proceed to register. To register, we have to use the `register` mutation. Since this is GraphQL, we can specify what we need easily which is the JWT access token in this case.
![Register Mutation](./images/08_PlaygroundRegister.png)
**Step 8**: Since we already registered the user above, we can now use it to login as well. To login, we have to use the `login` mutation. It will also return the JWT access token since we only asked for that.
![Login Mutation](./images/07_PlaygroundLogin.png)
**Step 9**: These JWT access tokens can be decoded to get important information like expiration for cookie creation, user information for front-end users, and custom claims for Hasura server. You can try at [jwt.io](https://jwt.io) and paste the JWT access token.
![JWT.io](./images/09_JWTDecode.png)

## Before Deploying to [KintoHub](https://www.kintohub.com) (optional)

Since **KintoHub**'s databases can only be accessed within their environment (which makes it really secure), I usually just deploy this setup as it is. I then connect to my Postgres database via [Kinto CLI](https://github.com/kintohub/kinto-cli), and then redeploy my Laravel application and Hasura server with updated settings.

**Note**: You can always deploy this setup on other platforms like [Heroku](https://www.heroku.com), [DigitalOcean](https://www.digitalocean.com), etc. I just use KintoHub since this is where I first deployed it and since it uses Docker by default.

## Deploying on KintoHub (optional)

If you do not have an account, [signup](https://www.kintohub.com) first.

### Deploy a PostgreSQL Server (optional)

1. Click **Create Service** at the top right of your environment
2. Click **From Catalog** and then select **PostgreSQL**
3. Fill or generate your `username`, `password` and `root password` for your database
4. Click **Deploy** At the top right.

Once complete, go to the **Access** tab and copy the **Root User Connection String**. This will take several minutes to complete.

**Note**: You can deploy your PostgreSQL database elsewhere but make sure to install `pgcrypto` extension.

### Deploy Laravel PHP App (optional)

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

### Deploy Nginx Php Proxy (optional)

We need to deploy web host proxy to serve the php app on KintoHub.

1. Click **Create Service** at the top right of your environment
2. Choose **Web App** from the list
3. Change the **Dockerfile Name** to `nginx.dockerfile`
4. Change the **Port** to `80`
5. Open the Environment Variables tab to copy and paste the following into the **key** textbox:
6. Click **Deploy** at the top right

When complete, open the **Access** tab and open the external URL to see your Laravel app!

### Deploy Hasura GraphQL Engine (optional)

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
