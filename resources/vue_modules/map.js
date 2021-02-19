
  export default{
    template:'<div id="map" style="height:100%"></div>',
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
      },
      mapInit(){                   
        this.map = new google.maps.Map(document.getElementById("map"),{
            center: this.init_params.map_center,
            zoom: this.init_params.map_zoom
        })
        this.infowindow = new google.maps.InfoWindow()
        var that = this
        this.map.addListener("click", async function(){
              that.infowindow.close()
            })
        for(var i=0;i<this.locations.length;i++){
          const marker = new google.maps.Marker({
            map: this.map,
            position: new google.maps.LatLng(this.locations[i].lat,this.locations[i].lng),
            title: this.locations[i].zipcode
          }).addListener("click", async function(){
              that.infowindow.close()
              await that.getLocationByZipCode(this.title)
              that.updateState()
              that.infowindow.open(that.map, this);
            })
        }  
      },
      updateState(){
        var location_string = this.location.name+', '+this.location.region+' ('+this.location.zipcode+')'
        var services_string = ""
        for(var i=0; i<this.location_services.length; i++){
          services_string += '<li class="fw-bold">'+this.location_services[i].name+'</li>'
        }
        var infowindow_content = '<div class="p-2">\
                                <h3>'+location_string+'</h3>\
                                <ul class="fs-6 list-unstyled ms-2">'+services_string+'</ul>\
                                <div class="d-flex justify-content-center">\
                                  <a href="" class="btn btn-primary">Sign up</a>\
                                </div>\
                              </div>'
        this.infowindow.setContent(infowindow_content)
      }
    }
  }

      