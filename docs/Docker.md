# Summary
Docker configurations are based on @GhazanfarMir's [setup](https://github.com/GhazanfarMir/dockercompose-laravel-nginx).
_Thank you!_

I highly recommend using [Portainer](https://documentation.portainer.io/quickstart/) to manage your Docker Containers.

# Environment Variables
Copy the docker environment file (`docker.env`) to  a new file (`docker.env.local`), and update the variables to match your setup.
```bash
cp docker.env docker.env.local
```

Review the populated config:
```bash
docker-compose --env-file docker.env.local config
```

# Build the Images
Until I figure out how to `npm install` before `npm run dev`, it's broken out into multiple services.  
And, need to run the `node-install` image first.
([reference](https://hackernoon.com/a-better-way-to-develop-node-js-with-docker-cd29d3a0093))

**To run the Yarn Install independently**
```bash
docker-compose --env-file docker.env.local run --rm node-install
```

Can manually run the Doctrine Migration via
```bash
docker-compose --env-file docker.env.local run --rm php-doctrine-migrate
```

**To run the entire setup**
- Make sure you've created a `docker.env.local` file, which'd be based on `docker.env`.
```bash
docker-compose --env-file docker.env.local up -d --build
```

# Database
If you drop/change the database, or change either `DB_USER` or `DB_PASSWORD`, then you should remove the volume
(or clear the folder if using a bind mount), or else MySQL container won't recreate the database & user.
([reference](https://github.com/MariaDB/mariadb-docker/issues/68#issuecomment-231552691))
