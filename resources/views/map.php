<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://unpkg.com/vue@next"></script> 
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script> 
    <script src="https://maps.googleapis.com/maps/api/js?key=&language="></script>
    <script src="https://cdn.jsdelivr.net/npm/vue3-sfc-loader/dist/vue3-sfc-loader.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <style type="text/css">
        #app, #map-wrapper, #map {
          height: 100%;
        }
        html, body {
          height: 100%;
          margin: 0;
          padding: 0;
        }
      </style>
    <title>Document</title>
</head>
<body>
  <div id="map-wrapper">
    <div id="map"></div>
  </div>
</body>
</html>

<script>
  var map = Vue.createApp({
      data(){
        return{
          api_path:"/api/",
          init_params:{
            map_center:{lat:46.8095955,lng:7.1032295},
            map_zoom:8
          },
          map:null,
          locations:null,
          infowindow:null,
          location:null,
          location_services:null, 
        }
      },  

      mounted: async function(){ 
        await axios
          .get(this.api_path+'locations')
          .then(responce => (this.locations=responce.data.locations))
        this.mapInit();    
      },

      methods:{
        async getLocationByZipCode(zipcode){
          await axios
            .get(this.api_path+'locations/'+zipcode+'/services')
            .then(responce => (this.location = responce.data.location, this.location_services = responce.data.services))
          this.updateState()
        },
        mapInit(){                   
          this.map = new google.maps.Map(document.getElementById("map"),{
              center: this.init_params.map_center,
              zoom: this.init_params.map_zoom
          });
          this.infowindow = new google.maps.InfoWindow()
          var that = this
          for(var i=0;i<this.locations.length;i++){
            new google.maps.Marker({
              map: this.map,
              position: new google.maps.LatLng(this.locations[i].lat,this.locations[i].lng),
              title: this.locations[i].zipcode
            }).addListener("click", async function(){
                that.infowindow.close()
                await that.getLocationByZipCode(this.title)
                that.infowindow.open(that.map, this);
              });
          }  
        },
        updateState(){
          var location_string = this.location.name+', '+this.location.region+' ('+this.location.zipcode+')'
          var services_string = ""
          for(var i=0; i<this.location_services.length; i++){
            services_string += '<li class="fw-bold">'+this.location_services[i].name+'</li>'
          }
          infowindow_content = '<div class="p-2">\
                                  <h3>'+location_string+'</h3>\
                                  <ul class="fs-6 list-unstyled ms-2">'+services_string+'</ul>\
                                  <div class="d-flex justify-content-center">\
                                    <a href="" class="btn btn-primary">Sign up</a>\
                                  </div>\
                                </div>'
          this.infowindow.setContent(infowindow_content)
        }
      }
    })
    map.mount('#map-wrapper')
</script>