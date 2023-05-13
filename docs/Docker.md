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
Before building the image, be sure to update the `NOTED_HOST` value in your `docker.env.local` file, to point to the
host where you'll be pulling notes from.

**To run the entire setup**
- Make sure you've created a `docker.env.local` file, which'd be based on `docker.env`.
```bash
docker-compose --env-file docker.env.local up -d --build
```

# Node Container
The container only needs to build the public assets, and it'll handle the npm depedency installation and all.  
It does not perform any post-build cleanup, so if any `node_modules` directory is created, it'll persist until manually
deleted. However, I would hold off since it operates as cache, facilitating subsequent build operations.

# Database
If you drop/change the database, or change either `DB_USER` or `DB_PASSWORD`, then you should remove the volume
(or clear the folder if using a bind mount), or else MySQL container won't recreate the database & user.
([reference](https://github.com/MariaDB/mariadb-docker/issues/68#issuecomment-231552691))

# Nginx SSL

You'll want to create a self-signed certificate for any local hosting/developement.

If you run this [Linux] command from the root of the application, then you don't need to make as many modification to the
relevant part of the `docker-compose.yml` file.
```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
   -keyout ./docker/nginx/config/self-gen.key \
   -out ./docker/nginx/config/self-sign-cert.crt \
   -subj "/C=US/ST=Florida/L=Pensacola/O=IAmATeapot/OU=ShortAndStout/CN=localhost"
```

**Note**: If trying to create an SSL Certificate on a Windows box, you'll need to preface the command with
`MSYS_NO_PATHCONV=1`. ([Reference](https://github.com/openssl/openssl/issues/8795#issuecomment-484946228))
