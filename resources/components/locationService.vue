<template>
  <form v-if="services" ref="servicesForm" @submit.prevent="setServices()">
    <div class="form-group row mb-4" :key="service" v-for="service in services">
      <div class="col-5">{{service.name}}</div>
      <div class="col-7 d-flex justify-content-between">
        <select style="width:47%" v-if="channels" name="channels" v-model="service.channel" :value="service.channel">
          <option value="null">Channel</option>
          <option v-if="channels.email">Email</option>
          <option v-if="channels.tg">Telegram</option>
        </select>
        <select style="width:47%" name="frequencies" v-model="service.frequency" :value="service.frequency">
          <option value="null">Frequency</option>
          <option :key="frequency" v-for="frequency in frequencies" :value="frequency">{{frequency}}</option>
        </select>
      </div>
    </div>
  </form>
</template>

<script>

export default {
  props:{
    location:null,
    userLocation:null,
    channels:null
  },
  watch: { 
    location: async function(newVal, oldVal) {
      if(newVal){
        await this.getServices(newVal)
      }
    }
  },
  data() {
    return {
      services: null,
      frequencies: {'daily_brief': 'Daily brief', 'daily_digest': 'Daily digest', 'weekly_brief': 'Weekly brief', 'weekly_digest': 'Weekly digest'},
    }
  },
  methods: {
    
    async getServices(id){
      if(id){
        await axios({
          method: 'get',
          url: '/locations/services/'+id+'/'+this.userLocation
        }).then((response) => {
          this.services = response.data.results
        }).catch((err) =>{
        });
      }
    },
    async setServices(){
      if(this.services){
        await axios({
          method: 'patch',
          url: '/profile/services',
          data: {
            services:this.services, 
            user_location_id: this.userLocation
          }
        })
      }
    },
    async deleteServices(){
      if(this.services){
        await axios({
          method: 'delete',
          url: '/profile/services',
          data: {
            user_location_id: this.userLocation
          }
        })
      }
    }

  },

  mounted: async function(){
    await this.getServices(this.location)
  },
}
</script>

<style scoped>
  select{
    height: 30px;
  }
</style>
