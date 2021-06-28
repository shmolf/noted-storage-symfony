# noted-storage-symfony
Symfony application for storing Note'd markdown notes

This is still a WIP.

## Summary

This repo should be a fairly complete representation of the application. But there are [instructions](docs/App-Setup.md)
for setting up this application from the ground, prior to any application coding.

## Objective

A user should be able to host their own instance of this application, for storing their notes.  
Using OAuth, and workspace management, each instance of this storage application will be be available from within https://note-d.app.

The host of Note'd does not need knowledge or access to any storage application, since only the user's browser is the client.  
This implies that a storage applition can be hosted behind a firewall, and so long as the user is behind the same firewall, they'll have
unrestricted access to their own notes api host. This would be considered an additional layer of protection, on top of OAuth token
authorization.

## Docker

Docker configurations are based on @GhazanfarMir's [setup](https://github.com/GhazanfarMir/dockercompose-laravel-nginx).
_Thank you!_

I highly recommend using [Portainer](https://documentation.portainer.io/quickstart/) to manage your Docker Containers.

### Environment Variables
Copy the environment file to `docker.env.local`, and update the variables to match your setup.
```bash
cp docker.env docker.env.local
```

To verify your config, run
```bash
docker-compose --env-file docker.env.local config
```

### Build the Images
Until I figure out how to `npm install` before `npm run dev`, it's broken out into multiple image configs.  
And, need to run the `node-install` image first.
([reference](https://hackernoon.com/a-better-way-to-develop-node-js-with-docker-cd29d3a0093))

```bash
docker-compose --env-file docker.env.local run --rm node-install; docker-compose --env-file docker.env.local up -d --build; docker-compose --env-file docker.env.local run --rm php-doctrine-migrate;
```
