# TYPO3 DDEV-Base

## Requires Docker & DDEV
### Docker Installation
* Docker has to be installed. For installing Docker on Ubuntu see: https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository
* After that you need to install `docker-composer-v2`:
```
apt-get install docker-compose-v2
```
* It is important that the user with whom Docker is to be used is in the Docker group.
```
sudo usermod -aG docker $USER
```
Then check whether the user is now in the group.
IMPORTANT: If not, a restart is probably necessary!
```
groups
```
It should then be possible to call the following command without authorization errors:
```
docker ps -a
```

### DDEV
* For routing with SSL mkcert (https://github.com/FiloSottile/mkcert#installation) has to be installed
```
sudo apt install libnss3-tools
```
* Then we install DDEV (https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/#linux)
```
sudo sh -c 'echo ""'
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://pkg.ddev.com/apt/gpg.key | gpg --dearmor | sudo tee /etc/apt/keyrings/ddev.gpg > /dev/null
sudo chmod a+r /etc/apt/keyrings/ddev.gpg

# Add DDEV releases to your package repository
sudo sh -c 'echo ""'
echo "deb [signed-by=/etc/apt/keyrings/ddev.gpg] https://pkg.ddev.com/apt/ * *" | sudo tee /etc/apt/sources.list.d/ddev.list >/dev/null

# Update package information and install DDEV
sudo sh -c 'echo ""'
sudo apt update && sudo apt install -y ddev
```
* Finally, install the local certificate authority for certificates:
```
mkcert -install
```
* Now add some add-ons we need
```
ddev get ddev/ddev-phpmyadmin
```
## Getting started
- Clone this repository to your local machine.
- Start your containers
```
ddev start
```
- Fetch the current database from the server. Only tables without user-data will be fetched. **Make sure your RSA-public-key is stored on the server and your IP-address is on the firewall-whitelist**
```
ddev fetch-production-db
```
- A local backend user will be created automatically. Check the script output for the credentials.
- Check via TYPO3 backend that the extension `ichhabrecht/filefill` is installed. This extension will load all relevant static contents from the server or will at least load placeholder images.
- If you start a new project, rename `config/system/settings.php` to `_settings.php`
  and execute `./vendor/bin/typo3 setup` via console. Follow all steps (Database-Credentials: "db" for everything, do not create a site-config).
  After that delete the automatically generated `settings.php` and use you own again.
- Do a
```
ddev composer install
```
- That's it! Run `ddev status` to see which website-urls you can use.

### Installing Apache Solr

If needed, you may install Apache Solr via DDEV, too.

Get the appropriate DDEV package
```
ddev get b13/ddev-apache-solr
```
Adjust the configuration of the Apache Solr container to fit our needs by updating `.ddev/apache-solr/config.yaml` with the following configuration:

```
config: "public/typo3conf/ext/solr/Resources/Private/Solr/solr.xml"
configsets:
  - name: "ext_solr_11_2_0"
    path: "public/typo3conf/ext/solr/Resources/Private/Solr/configsets/ext_solr_11_2_0"
    cores:
      - name: "core_en"
        schema: "english/schema.xml"
      - name: "core_de"
        schema: "german/schema.xml"
```
You need to adjust the configuration of `.ddev/docker-compose.apache-solr.yaml`, too. Update

```
image:solr:9.4
```
to
```
image:solr:8
```

And finally add a hook to your `.ddev/config.yaml`:

```
hooks:
  post-start:
    - exec-host: ddev solrctl apply
```

Then restart the containers
```
ddev restart
```

Check, if the Apache Solr container is working by calling http://rkw-website.ddev.site:8983/solr/#/ in the browser. Notice, we are using the non-https connection and port here, as these are used within the containers, too.

If it is working, we need to set the Apache Solr container to be used in the desired TYPO3 sites by modifying the setting `solr_host_read` in `./config/sites/[website]/config.yaml` to

```solr_host_read: apache-solr```

You may check the status for the desired website through the TYPO3 backend by selecting the site within the site tree and clicking on the menu item `Apache Solr` > `Ìnfo`. You should see a green flash message with the info:
```
Following Apache Solr servers have been contacted:
http://apache-solr:8983/solr/core_de
```
Now you may go on and index events to Solr.
- change to `Index Queue`
- check `rkw_events` in `Index Queue Initialization`
- click button `Queue Selected Content for Indexing`
- run index queue by clicking button `Index now`

After a first run you may check, if there are any items contained within the new index.
- check `http://rkw-website.ddev.site:8983/solr/#/`
- select `core_de` from the menu item `Core Selector`
- switch to the menu item `Query` within the selected core
- run the query with the given parameters by clicking `Execute query`
- and you should receive a json list of entries

Further configurations may include configuring a schedule task to populate the index automatically.

# Deployment
* Before you deploy add your ssh-keys to your containers to allow access to GIT-repository for deployment
```
ddev auth ssh
```

# Commands for DDEV
- SSH into the web-container
```
ddev ssh
```
- Stop containers
```
ddev stop
```

