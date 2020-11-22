# Laravel + Hasura + MinIO GraphQL JWT Stack with Docker (November 2020) 🎉🎉🎉

This backend-only boilerplate is created for people who are _really really curious_ about **GraphQL** with [**Laravel 7.x**](https://laravel.com) and want to experience the wonders of [**Hasura GraphQL Builder**](https://hasura.io). 😎 This is also for the **_frontend developers_** (yep, you, guys 😉) who just want to have a ready-to-use backend with all the basic features like _JWT authentication_ 🔐, _multi-role system_ 👔, and _file upload feature_ 💾.

![Boilerplate Features](./images/Features.png)

Btw, if you have cool suggestions, you can fork this repo or discuss it with me directly ([Facebook](https://www.facebook.com/luchmewep)). 😁 If you have a job offering, I am up for an interview! (_Yes, I need MONEY!_ 💰💰💰)

## Laravel 7.x Features

- [x] GraphQL-ready ⚡
- [x] GraphQL JWT Authentication System 🔐 (Login, Register, Social Login, etc.)
- [x] JWT Claims for Hasura GraphQL Server 💡
- [x] Multi-Roles for Users with Approval and Access Level Logic 👔
- [x] Anonymous Role for Newly Registered Users with Role Under Approval 👀
- [x] MinIO Object Storage Integration 💾
- [x] RESTful API route for Photo Upload 📷
- [x] RESTful API route for Multiple Photo Upload 📷
- [x] RESTful API route for Document Upload 📚
- [x] RESTful API route for Multiple Document Upload 📚
- [ ] Payment Gateway Integration (_Paymongo/Stripe_) 💰
- [ ] SMS Gateway Integration (_Nexmo_) 📮

## To Be Added in the Future

- [ ] Frontend (Gatsby React with Authentication Scaffolding) (**LET'S GO FULLSTACK SOON! 🎉**)

## Boilerplate Structure

![Laravel Hasura Stack](./images/LaravelHasura.png)

## Technologies Used

- **GraphQL**
- **Hasura**
- **Postgres**
- **Laravel**
- **JWT**
- **MinIO**
- **Docker**

---

## Directory Structure

- `/laravel` - Laravel application
- `/nginx` - Configuration files for nginx to serve PHP
- `laravel.dockerfile` - Dockerfile to boostrap Laravel app
- `nginx.dockerfile` - Dockerfile to serve nginx website
- `hasura.dockerfile` - Dockerfile for Hasura GraphQL engine
- `docker-compose.yaml` - Composition of nginx + laravel + postgres to work together

---

## **How It Started (TL;DR)** 🥱😴

I came upon an article about GraphQL while browsing the web for some cool tech stuff. I got excited all of a sudden after reading that it addresses **over-fetching** (_getting more data than you need_). For most, it's not big of a deal, but for backend developers, it is sort of a challenge. Yes, I know that we can solve it by adding _more logic_ to our existing RESTful API's, but then again, that means more work for the backend people. 😭 So yeah, I like making my life easier so I chose GraphQL. 🤣 You can read more about other issues addressed by GraphQL [here](<[https://link](https://hasura.io/learn/graphql/intro-graphql/graphql-vs-rest/)>).

### RESTful API Call

![RESTful API Call](images/hasura_restvsgraph_1.png)

### GraphQL API Call

![GraphQL API Call](images/hasura_restvsgraph_2.gif)

And so I tried researching how to create a GraphQL server with Laravel since I am comfortable with it. There I found the [Laravel Lighthouse](https://lighthouse-php.com). Consequently, by pure luck, I found [**Jose Luis Fonseca**](https://twitter.com/Joselfonseca)'s package ([_joselfonseca/lighthouse-graphql-passport-auth_](https://lighthouse-php-auth.com)) which gave me instant authentication-related GraphQL mutations such as _login_, _register_, and _logout_! Thanks, Mr. Fonseca! 👏

I then studied how to create my own queries, mutations, and subscriptions but the amount of stuff to learn is **A LOT**! And so my lazy self looked for an easier route and there I found [**Hasura GraphQL API Builder**](https://hasura.io), the best friend of all GraphQL enthusiasts! 🤩

With Hasura, all you need to do is create a database table via _Hasura Console_ and all queries, mutations, and subscriptions are _automagically_ created for you!

### The **Problem** with Hasura 😱

Hasura is _almost_ perfect really. But it lacks two very important features that most developers are looking for. And those are **authentication** 🔐 and **non-database related computations** 📈📉📊.

Hasura does not have a built-in authentication system. It is by design, according to them. It _needs_ a 3rd party authentication provider like [**Auth0**](https://auth0.com), [**AWS Cognito**](https://aws.amazon.com/cognito/), [**AuthGuardian**](https://www.onegraph.com/auth-guardian/), etc. Adding custom logic on top of Hasura such as complicated computations is also not possible with Hasura. It is also by design. Either build a serverless function or create a RESTful API somewhere.

### The **Solution** to Hasura's Predicament 💡

I almost gave up with Hasura but then a question popped out of my head...

_Is it possible for Laravel and Hasura to fulfill each other's weaknesses?_

I mean just think about it...

- Hasura needs a 3rd party authentication service. Laravel can provide that easily (_via Laravel Passport_)!
- Hasura needs 3rd party serverless functions for custom computations. Laravel can provide that as well (_via RESTful APIs_)!
- GraphQL development with Laravel is really slow for beginners. Hasura can make it faster!

So yep, _theoretically_, they are match made in heaven. 😍 So the goal became how to make them work with each other.

### **The Final Product** ✨

So it took me a while (_months really since I have too much on my plate_) to finally figure out how to make 'em work with each other.

Before, Laravel can create JWT access tokens but that _isn't enough_ for Hasura since it requires `custom claims`. So I looked for a way to add some custom claims to Laravel's default JWT, and there I found the **final piece** of the puzzle, [**Cor Bosman**](https://github.com/corbosman)'s package ([_corbosman/laravel-passport-claims_](https://github.com/corbosman/laravel-passport-claims)). Thanks, Mr. Bosman! 👏

Below is the custom claims added using the package:

![Hasura Custom Claims](./images/CustomClaims.png)

Below is the [`JWT mode`](https://hasura.io/docs/1.0/graphql/core/auth/authentication/index.html#jwt-json-web-token) of authentication for the whole boilerplate:

![Hasura's JWT Mode Diagram](./images/HasuraJWTMode.jpg)

---

## 🌄 **Local Deployment** 🌄

**Requires Docker v2.x or higher**

To download and install **PostgreSQL**, **MinIO** and **Hasura** services via Docker, you have to run this command.

```
docker-compose up -d
```

Make sure to be on the root folder of the cloned boilerplate as this command will look for the `docker-compose.yaml`. If successful, you should see the 3 services.

![Boilerplate](./images/Docker_Apps.png)

---

### Part 1: **MinIO** Configuration 🚧

To configure MinIO to work with Laravel, follow the GIF instruction below.

![MinIO config](images/config_minio_1.gif)

**Note**: You can always choose another name for the bucket but make sure to add `Read and Write` Policy to that bucket and update the `MINIO_BUCKET` variable on Laravel's `.env` file.

---

### Part 2: **Laravel** Configuration 🚧

Laravel's files are located on the `laravel` folder. Do not rename the folder since Nginx will look for this specific folder during online deployment.

**Step 1**: Since Laravel requires `.env` file, rename the `.env.example` to `.env`. Make sure to install all Laravel dependencies by running this command:

```
composer install
```

**Step 2**: Make sure to `generate your own app key` before anything else. App Key will be used for all sorts of encryption within Laravel.

```
php artisan key:generate
```

![Generate New App Key](./images/01_AppKey.png)

**Step 3**: Run fresh migrations to create all necessary tables and seed the roles table as well. To add or customize the roles, look for the `RolesSeeder.php` file.

```
php artisan migrate:fresh --seed
```

![Run Fresh Migrations](./images/02_Migrate.png)

**Step 4**: You have to generate your own OAuth Keys as well. To remove mine, just use `--force`. Make sure to paste the new grant client id and secret to Laravel's env file.

```
php artisan passport:install --force
```

![Generate New OAuth Keys](./images/03_OAuthKeys.png)

**Step 5**: Make sure to refresh config cache to utilize the new keys.

```
php artisan config:cache
```

![Clear Configs](./images/04_ClearConfigs.png)
**Step 6**: Run the app to test our GraphQL API. By default, the GraphQL endpoint will be at `http://localhost:8000/graphql`. You can download [**GraphiQL**](https://link) or [**GraphQL Playground**](https://link) to test this endpoint.

```
php artisan serve
```

![Serve the application](./images/05_Serve.png)

**Step 7**: If we will try to register, the role id is **required**. We can look for the role id's via Laravel Tinker.

### Via Laravel Tinker

![Roles on Tinker](./images/06_Tinker.png)

**Step 8**: Since we now have the role id for the user, we can proceed to register an account. To register, we have to use the `register` mutation and supply all information needed. For this demo, we just need the `access_token` so we only need to specify that as the output.

![Register Mutation](./images/08_PlaygroundRegister.png)

**Step 9**: Since we already registered the user above, we can now use it to login as well. To login, we have to use the `login` mutation and specify `access_token` to be the output.

![Login Mutation](./images/07_PlaygroundLogin.png)

**Step 10**: The JWT `access_token` can be _decoded_ to get important information like expiration date, user information, and custom claims (_required by Hasura_). You can try at [jwt.io](https://jwt.io) and paste the `access_token`.

![JWT.io](./images/09_JWTDecode.png)

---

### Part 3: **Hasura** Configuration 🚧

For Hasura to utilize the tokens generated by Laravel, it needs to have the OAuth public key. Look for `HASURA_GRAPHQL_JWT_SECRET` on the `docker-compose.yaml` file and add the single-line public key. Follow the GIF instruction below for the single-line public key.

![Add Public Key to docker-compose](images/config_hasura_1.gif)

Once finished, run the following command again:

```
docker-compose up -d
```

---

## 📷 **How to Upload Photo and Document** 📚

As mentioned, one vital part of this boilerplate is the storage service (**MinIO**). Before you can upload anything, you have to have a valid `access_token` and you can only get one if you have registered already. To demonstrate, **PostMan** is used. Configure `Authorization` by choosing `Bearer Token` and pasting the `access_token`.

![Set Authorization Token](images/MinIO_Upload_0.png)

### Upload Single Photo 📷

- Endpoint: `http://localhost:8000/api/photos`
- Key: `photo`
- Validations: `images|max:5120` (max of 5MB)

![Upload Single Photo](images/MinIO_Upload_1.png)

### Upload Multiple Photos 📷

- Endpoint: `http://localhost:8000/api/photos`
- Key: `photos[]`
- Validations: `images|max:5120` (max of 5MB each)

![Upload Multiple Photos](images/MinIO_Upload_2.png)

### Upload Single Document 📚

- Endpoint: `http://localhost:8000/api/documents`
- Key: `document`
- Validations: `mimes:doc,docx,pdf,txt|max:5120` (max of 5MB)

![Upload Single Document](images/MinIO_Upload_3.png)

### Upload Multiple Documents 📚

- Endpoint: `http://localhost:8000/api/documents`
- Key: `documents[]`
- Validations: `mimes:doc,docx,pdf,txt|max:5120` (max of 5MB each)

![Upload Multiple Documents](images/MinIO_Upload_4.png)

---

## 🌎 **Online Deployment at [KintoHub](https://www.kintohub.com) (optional)** 🌎

Since **KintoHub**'s databases can only be accessed within their environment (which makes it really secure), I usually just deploy this setup as it is. I then connect to my Postgres database via [Kinto CLI](https://github.com/kintohub/kinto-cli), and then redeploy my Laravel application and Hasura server with updated settings.

**Note**: You can always deploy this setup on other platforms like [Heroku](https://www.heroku.com), [DigitalOcean](https://www.digitalocean.com), etc. I just use KintoHub since this is where I first deployed it and since it uses Docker by default.

## Deploying on KintoHub (optional)

If you do not have an account yet, [signup](https://www.kintohub.com) first.

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
HASURA_GRAPHQL_JWT_SECRET={   "type":"RS256",   "key": "" }
HASURA_GRAPHQL_UNAUTHORIZED_ROLE=anonymous
```

When complete, open the **Access** tab and open the external URL to see your Hasura app!
