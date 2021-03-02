<template id="locator-component">
<div>
  <div class="text-center">
    <h2>Es gibt schon 100 Gemeinden online</h2>
  </div>

  <div class="row g-0 mt-3">
    <div class="col-lg-9">
        <div id="mapWrapper" ref="mapWrapper"></div>
    </div>

    <div class="col-lg-3 bg-dark p-4 text-white">
        <div id="signupform">
            <em class="fs-6">Entdecken Sie Dienstleistungen in Ihrer Gemeinde und verbinden Sie sich mit Nachbarn, regionale Unternehmen und Verwaltung in einer Minute.</em>
            <div class="clearfix mt-5">
                <h4 class="float-start">Mein Wohnort</h4>
                <div class="mt-1 float-end"><a class="link-light" href="#">finde mich</a></div>
            </div>
            <form class="d-flex needs-validation" @submit="submit" novalidate>
                <div class="input-group">
                    <input class="form-control" ref="tomSelect" placeholder="PLZ oder Ortsname" v-model="searchable" required>
                    <button class="btn btn-primary" type="submit"><svg class="" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M19 17l-5.15-5.15a7 7 0 1 0-2 2L17 19zM3.5 8A4.5 4.5 0 1 1 8 12.5 4.5 4.5 0 0 1 3.5 8z"/></svg></button>
                </div>
                <div class="invalid-feedback">
                    <p v-if="errors.length">
                    <ul class="list-unstyled">
                        <li v-for="error in errors">{{ error }}</li>
                    </ul>
                    </p>
                </div>
            </form>
        </div>
    </div>

  </div>
</div>
</template>

<script>
  export default {
    data() {
        return {
            map: {
                el: null,
                center: {
                    lat: 46.8095955,
                    lng: 8.1032295
                },
                zoom: 7.5,
                markersDropped: false,
                markers: [],
                infowindow: null
            },
            locations: null,
            log: '',
            errors: [],
            searchable: ''
        }
    },

    mounted: async function() {
        await this.getLocations();
        this.initMap();
        this.initTomSelect();
        window.addEventListener("scroll", _.debounce(this.dropMarkers, 150, { 'leading': true }));
    },

    methods: {

        async getLocations() {
            await axios
                .get('/locations')
                .then(response => (this.locations = response.data.locations));
        },

        initMap() {
            this.map.el = new google.maps.Map(
                this.$refs.mapWrapper,
                {
                    center: this.map.center,
                    zoom: this.map.zoom
                }
            );
        },

        initTomSelect() {
            this.ts = new TomSelect(this.$refs.tomSelect, {
                openOnFocus: false,
                maxItems: 1,
                valueField: 'id',
                searchField: ['name','zipcode'],
                labelField: 'name',
                sortField: 'name',
                options: this.locations
             });

            this.ts.on('change', id => {
                this.openInfoWindow(id)
            });
        },

        dropMarkers() {
            if(!this.checkMapInViewport() || this.map.markersDropped) {
                return;
            }
            let m = null;
            for(let i in this.locations) {
                let zip = this.locations[i].zipcode;
                let id = this.locations[i].id;
                setTimeout(() => {
                    m = new google.maps.Marker({
                        position: new google.maps.LatLng(this.locations[i].lat,this.locations[i].lng),
                        map: this.map.el,
                        zipcode: zip,
                        animation: google.maps.Animation.DROP,
                        id: id
                    });
                    this.map.markers[id] = m;
                    m.addListener("click", () => {
                        this.openInfoWindow(id);
                    });
                }, i*250);
            }
            this.map.markersDropped = true;
        },

        async openInfoWindow(id) {
            if(this.map.infowindow) {
                this.map.infowindow.close();
                this.map.infowindow = null;
            }

            let marker = this.map.markers[id];
            let content = await this.getInfoWindowContent(marker.zipcode);
            const iw = new google.maps.InfoWindow({
                content: content
            });
            this.map.infowindow = iw;
            iw.open(this.map.el, marker);
        },

        async getInfoWindowContent(zipcode) {
            try {
                const response = await axios.get('/locations/'+zipcode+'/services');
                const location = response.data.location;
                const services = response.data.services;

                let title = location.zipcode + ' ' + location.name + ' '+ location.region;
                let services_list = "";
                for(let i = 0; i < services.length; i++) {
                    services_list += '<li class="fs-6">'+services[i].name+'</li>';
                }

                return '<div>\
                    <h5 id="location_name">'+title+'</h5>\
                    <ul>'+services_list+'</ul>\
                    <a href="/signup" class="btn btn-primary btn-sm">Sign up</a>\
                    </div>';
            } catch (error) {
                console.error(error);
            }
        },

        checkMapInViewport() {
            let box = this.$refs.mapWrapper.getBoundingClientRect();
            return (
                box.bottom > 0 &&
                (box.top + 100) <= (window.innerHeight || document.documentElement.clientHeight)
            );
        },

        modalShow() {
            const myModal = new bootstrap.Modal(document.getElementById('locationModal'), {
                keyboard: false
            });
            myModal.show();
	    },

        submit(e) {
            e.preventDefault();
            console.log(e);
        }
    }
}
</script>

<style>
  #mapWrapper {
    height: 400px;
    width: 100%;
  }

  .ts-control.single .ts-input:after {
      content: ''
  }
</style>
