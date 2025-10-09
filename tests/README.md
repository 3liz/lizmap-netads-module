# Run Lizmap stack with docker-compose
Steps:

- Launch Lizmap with docker-compose

```bash
# Clean previous versions (optional)
make clean

# Run the different services
make run
```
- If you want to use a specific version of Lizmap (for example a local docker image), indicate the version of the docker image into LIZMAP_VERSION_TAG:

```bash
make run LIZMAP_VERSION_TAG=3.8.0-rc.4
```

- Install the module and default rights

```bash
# install the module
make install-module

# Set default rights
make import-lizmap-acl
```

- import data

```bash
make import-data
```

- Open your browser at `http://localhost:9090`

For more information, refer to the [docker-compose documentation](https://docs.docker.com/compose/)

## Access to the dockerized PostgreSQL instance

You can access the docker PostgreSQL test database `lizmap` from your host by configuring a
[service file](https://docs.qgis.org/latest/en/docs/user_manual/managing_data_source/opening_data.html#postgresql-service-connection-file).
The service file can be stored in your user home `~/.pg_service.conf` and should contain this section

```ini
[lizmap-netads]
dbname=lizmap
host=localhost
port=9033
user=lizmap
password=lizmap1234!
```

Then you can use any PostgreSQL client (psql, QGIS, PgAdmin, DBeaver) and use the `service`
instead of the other credentials (host, port, database name, user and password).

```bash
psql service=lizmap-netads
```
## Tests manuels simples (vérification compatibilité Lizmap)

Le js chargé par le module dans lizmap est testable :

 - http://localhost:9090/index.php/view/map?repository=netads&project=netads&parcelle=&IDU=800016000LZ0018 : doit centrer la carte sur une parcelle

- http://localhost:9090/index.php/view/map?repository=netads&project=netads&parcelle=&IDU=800016000LZabcd : affiche un message "Aucune parcelle trouvée"

- le click sur une parcelle doit afficher dans la popup , les informations de la parcelle, une liste d'impacts
 et un message "
    ````
    Erreur lors de l'interrogation de l'API : "netADS API call error"
    Consulter le journal d'erreurs pour plus d'information.
    `````
    Le fichier de log `lizmap-admin.log` doit contenir plus de détail
