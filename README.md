# Laravel Example

This example has been referenced and rebuilt from [Aschmelyun/docker-compose-laravel](https://github.com/aschmelyun/docker-compose-laravel).
You can use this repository to deploy a Laravel app on KintoHub.
Additionally, it can be used to quickly setup Laravel locally on your machine.

## Directory Structure

* `/laravel` - Generic installation of Laravel
* `/nginx` - Configuration files for nginx to serve PHP
* `laravel.dockerfile` - Dockerfile to boostrap Laravel app
* `nginx.dockerfile` - Dockerfile to serve nginx website
* `docker-compose.yaml` - Composition of nginx + laravel + mysql to work together

## Running locally

**Requires Docker v2.x or higher**

In the root of this repository use `docker-compose up -d`

Once complete, go to `localhost:8080` to access the website!
If you're interested in using MySQL, find the database credentials in the `docker-compose.yaml` file.

## Setup a new project

Click on the **Use this Template** button at the top right

Begin to modify the files as you please!

## Deploying on KintoHub

If you do not have an account, first [signup](https://www.kintohub.com)
This example requires a MySQL server which requires you to activate [pay-as-you-go](https://docs.kintohub.com/anatomy/billing#activate-pay-as-you-go-billing) billing on KintoHub.

### Deploy a MySQL Server

1. Click **Create Service** at the top right of your environment
2. Click **From Catalog** and then select **MySQL**
3. Fill or generate your `username`, `password` and `root password` for your database
4. Click **Deploy** At the top right.

Once complete, go to the **Access** tab and copy the **Root User Connection String**

This will take several minutes to complete

### Deploy Laravel PHP App

1. Click **Create Service** at the top right of your environment
2. Choose **Backend API** from the list
3. Change the **Dockerfile Name** to `laravel.dockerfile`
4. Change the **Port** to `9000`
5. Open the **Environment Variables** tab and enter the key `DATABASE_URL` and paste the connection string value you copied in the **Deploy a MySQL Server Step**
6. Additionally, paste the following into the **key** textbox:

```
APP_NAME=Laravel
APP_ENV=dev
APP_KEY=base64:PoStwuoIPBnH+W2znwpmQbZvCJZPdou1DedUu+3F7mI=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
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
