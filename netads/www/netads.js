const NOM_COUCHE_PARCELLES = 'parcelles';

lizMap.events.on({
    uicreated: () => {
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());

        if (params && params.IDU) {

            let parcellesParam = params.IDU.split(',');
            let parcelles = [];
            for(let parcelleIDU of parcellesParam) {
                let ok = true;
                if (parcelleIDU.length < 12) {
                    console.log('Le paramètre IDU doit contenir 12 ou 15 caractères !');
                        ok = false;
                }

                if (parcelleIDU.length == 12) {
                    parcelleIDU = netAdsConfig.prefixParcelle + parcelleIDU;
                }

                if (parcelleIDU.length != 15) {
                    console.log('Le paramètre IDU doit contenir 12 ou 15 caractères !');
                        ok = false;
                    }
                if (ok) {
                    parcelles.push(parcelleIDU);
                }
            }
            if (parcelles.length == 0) {
                console.log('Le paramètre IDU ne contient aucun ID valide');
                return;
            }
            // Construction de la requête de récupération de
            // l'emprise pour la/les parcelles en paramètre
            const sentFormData = new FormData();

            sentFormData.append('SERVICE', 'WFS');
            sentFormData.append('VERSION', '1.1.0');
            sentFormData.append('REQUEST', 'GetFeature');
            sentFormData.append('TYPENAME', NOM_COUCHE_PARCELLES);
            sentFormData.append('OUTPUTFORMAT', 'GeoJSON');
            sentFormData.append('GEOMETRYNAME', 'extent');

            // Transformation du paramètre d'URL parcelles en EXP_FILTER
            let filter = '';
            for(let parcelleIDU of parcelles) {
                filter += `"ident" = '${parcelleIDU}' OR `;
            };
            sentFormData.append('EXP_FILTER', filter.substring(0, filter.length - 3));

            fetch(`${lizUrls.wms}?repository=${lizUrls.params.repository}&project=${lizUrls.params.project}`, {
                body: sentFormData,
                method: "POST"
            }).then(response => {
                return response.json();
            }).then(data => {
                if (data?.features.length > 0) {
                    let extent = data.features[0].bbox;

                    // Conversion de l'extent de 4326 vers la projection de la carte
                    // TODO : expose OL6 transformExtent in Lizmap
                    // https://openlayers.org/en/latest/apidoc/module-ol_proj.html#.transformExtent
                    const topleft = lizMap.mainLizmap.transform([extent[0], extent[1]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    const bottomright = lizMap.mainLizmap.transform([extent[2], extent[3]], 'EPSG:4326', lizMap.mainLizmap.projection);
                    // Zoom sur l'emprise de la/les parcelles en paramètre
                    lizMap.mainLizmap.map.getView().fit([topleft[0], topleft[1], bottomright[0], bottomright[1]]);

                } else {
                    lizMap.addMessage('Aucune parcelle trouvée', 'info', true);
                }
            });
        }
    },
    lizmappopupdisplayed: () => {

        const layerFidNodes = document.querySelectorAll(`input[value^="${NOM_COUCHE_PARCELLES}_"].lizmap-popup-layer-feature-id`);

        if (!layerFidNodes) {
            return;
        }

        function getPromiseDossier(layerFidNode) {
            return new Promise(function(resolve, reject) {
                const featureId = layerFidNode.value.split('.')[1];
                fetch(`${lizUrls.basepath}index.php/netads/dossiers?repository=${lizUrls.params.repository}&project=${lizUrls.params.project}&parcelle_fid=${featureId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.status+': '+response.statusText);
                    }
                    return response.text();
                })
                .then(text => {
                    layerFidNode.parentElement.insertAdjacentHTML('beforeend', text);
                    resolve(featureId);
                })
                .catch(reason => { reject(reason); });
            });
        }
        function getPromiseImpact(layerFidNode) {
            return new Promise(function(resolve, reject) {
                const featureId = layerFidNode.value.split('.')[1];
                fetch(`${lizUrls.basepath}index.php/netads/impacts/info?repository=${lizUrls.params.repository}&project=${lizUrls.params.project}&parcelle_fid=${featureId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.status+': '+response.statusText);
                    }
                    return response.text();
                })
                .then(text => {
                    layerFidNode.parentElement.insertAdjacentHTML('beforeend', text);
                    resolve(featureId);
                })
                .catch(reason => { reject(reason); });
            });
        }

        const promises = [];
        for (const layerFidNode of layerFidNodes) {
            promises.push(getPromiseDossier(layerFidNode));
            promises.push(getPromiseImpact(layerFidNode));
        }

        Promise.allSettled(promises).then((results) => {
            results.forEach((result) => {
                if (result.status === 'rejected') {
                    console.log(result.reason);
                }
            });
        });
    }
});
