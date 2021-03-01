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
            <em class="fs-5">Entdecken Sie Dienstleistungen in Ihrer Gemeinde und verbinden Sie sich mit Nachbarn, regionale Unternehmen und Verwaltung in einer Minute.</em>
            <div class="clearfix mt-5">
                <h4 class="float-start">Mein Wohnort</h4>
                <div class="mt-1 float-end"><a class="link-light" href="#">finde mich</a></div>
            </div>
            <form class="d-flex needs-validation" @submit="submit" method="post" novalidate>
                <div class="input-group">
                    <input class="form-control" name="location" id="location" type="search" placeholder="PLZ oder Ortsname" aria-label="Search" v-model="cred.location" required>
                    <button class="btn btn-primary"  type="submit"><svg class="" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M19 17l-5.15-5.15a7 7 0 1 0-2 2L17 19zM3.5 8A4.5 4.5 0 1 1 8 12.5 4.5 4.5 0 0 1 3.5 8z"/></svg></button>
                </div>
                <div class="invalid-feedback">
                    <p v-if="errors.length">
                    <ul class="list-unstyled">
                        <li v-for="error in errors">{{ error }}</li>
                    </ul>
                    </p>
                </div>
            </form>

            <!-- Modal -->

            <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="modalLabel_location" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <template v-if="log =='services'">
                                <h5 class="paragraphs" id="location_name"> {{ location_name}},</h5> &nbsp;
                                <h5 class="paragraphs" id="region_name"> {{ location_region}}</h5>  &nbsp;
                                <h5 class="paragraphs" id="location_zip"> ({{ cred.location}})</h5>
                            </template>
                            <button  type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul id="array-rendering">
                                <template v-if="log == 'error'"><p class="paragraphs fw-normal fs-5">Bad input. Please write zipcode correctly! </p></template>

                                <template v-if="log == 'noLoc'"><p class="paragraphs fw-normal fs-5"> There is no this location. Please fill out form! </p></template>
                                <template v-else-if="log =='noservices'">
                                    <p class="paragraphs fw-normal fs-5">
                                        There is no services by {{cred.location}} zipcode. Please fill out form!
                                    </p>
                                </template>
                                <template v-if="log =='services'">
                                    <li class="paragraphs fw-normal fs-5" v-for="(item, index) in list.services" v-bind:key="index">
                                        {{ item.name }}
                                    </li>
                                </template>

                            </ul>
                        </div>
                        <div class="modal-footer">
                            <template v-if="log =='services'">
                                <button type="button" class="btn btn-primary">Sign up</button>
                            </template>
                            <template v-else-if="log =='noLoc'">
                                <button type="button" class="btn btn-primary">Fill form</button>
                            </template>
                            <template v-else-if="log =='noservices'">
                                <button type="button" class="btn btn-primary">Fill form</button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
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
            cred: {
                location: null,
            }
        }
    },

    mounted: async function() {
        this.initMap();
        await this.getLocations();
        window.addEventListener("scroll", _.debounce(this.dropMarkers, 150, { 'leading': true }));
    },

    methods: {
        async getLocations() {
            await axios
                .get('/locations')
                .then(response => (this.locations = response.data.locations));
            console.log(this);
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

        dropMarkers() {
            if(!this.checkMapInViewport() || this.map.markersDropped) {
                return;
            }
            let m = null;
            for(let i in this.locations) {
                let zip = this.locations[i].zipcode;
                setTimeout(() => {
                    m = new google.maps.Marker({
                        position: new google.maps.LatLng(this.locations[i].lat,this.locations[i].lng),
                        map: this.map.el,
                        zipcode: zip,
                        animation: google.maps.Animation.DROP,
                        id: i
                    });
                    this.map.markers[i] = m;
                    m.addListener("click", () => {
                        this.openInfoWindow(i);
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
                    <a href="" class="btn btn-primary btn-sm">Sign up</a>\
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
            this.errors = [];
            axios.get("/locations/" + this.cred.location + "/services")
                .then((resp) => {
                    this.list = resp.data
                    if('location' in resp.data) {
                        this.location_name = resp.data.location.name;
                        this.location_region = resp.data.location.region;
                        this.location_zip = resp.data.location.zipcode;
                        this.location_services = resp.data.services;
                        this.log = 'services';
                        var key = this.location_zip;
                        map_intsance.infowindow.open(map_intsance.map, map_intsance.markers[key]);
                        var location_string = this.location_name+', '+this.location_region+' ('+this.location_zip+')';
                        var services_string = "";
                        for(var i=0; i<this.location_services.length; i++) {
                            services_string += '<li class="fw-normal fs-5">'+this.location_services[i].name+'</li>';
                        }
                        var infowindow_content = '<div>\
                        <div class="modal-header"> \
                            <h3 id="location_name">'+location_string+'</h3>\
                            </div>\
                            <div class="modal-body"> \
                            <ul id="array-rendering">'+services_string+'</ul>\
                            </div>\
                            <div class="modal-footer"> \
                            <div class="d-flex justify-content-end">\
                              <a href="" class="btn btn-primary">Sign up</a>\
                            </div>\
                          </div>';
                        map_intsance.infowindow.setContent(infowindow_content);
                    } else {
                        this.log = 'noservices';
                    }
                    document.getElementById("location").classList.remove("is-invalid");

                })
                .catch((err) => {
                    if(!this.cred.location || err.response.status === 400) {
                        this.errors.push('Write valid zipcode pls');
                        this.log = 'error';
                        document.getElementById("location").classList.add("is-invalid");
                    } else if (err.response.status === 404) {
                        this.log = 'noLoc';
                        return;
                    }
                    this.list = [];
                });
            e.preventDefault();
        }
    }
}
</script>

<style>
  #mapWrapper {
    height: 400px;
    width: 100%;
  }
</style>
