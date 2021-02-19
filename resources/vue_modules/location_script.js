
var code = {
  names: 'location', data() {
    return {
      log: '',
      modalOpen: false,
      errors: [],
      list: [],
      location_name: [],
      region_name:[],
      cred: {
        location: null,
      },
    }
  },
  methods: {

    submit(e) {

      this.errors = [];
      const myModal = new bootstrap.Modal(document.getElementById('locationModal'), {
        keyboard: false
      })
      axios.get("http://localhost/gena/api/locations/" + this.cred.location + "/services")
        .then((resp) => {


          this.list = resp.data
          if (resp.data == 'noLocation') {
            this.log = 'noLoc'
            myModal.show()
          } else if('location' in resp.data) {
            this.location_name = resp.data.location.name
            this.region_name = resp.data.location.region
            this.log = 'services'
            myModal.show()
          }else{
            this.log = 'noservices'
            myModal.show()
            // window.$("#locationModal").modal('show')
          }
          document.getElementById("location").classList.remove("is-invalid")
          console.warn(resp.data)
        })
        .catch((err) => {
          if(!this.cred.location || err.response.status === 400){
            this.errors.push('Write valid zipcode pls');
            this.log = 'error'
            document.getElementById("location").classList.add("is-invalid")
          }
          else if (err.response.status === 404) {
            myModal.show()
            this.log = 'noLoc'
            return
          }
          this.list = [];
        });
      e.preventDefault();
    },


  }
  ,mounted: function () {

    //alert(this.cred.location)
  }
}


var app = Vue.createApp(code)

app.mount('#searchbyzipcode')
