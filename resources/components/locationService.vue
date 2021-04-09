<template>
  <form v-if="services" ref="servicesForm" @submit.prevent="setServices()">
    <div class="form-group row mb-4" :key="service" v-for="service in services">
      <div class="col-5">{{service.name}}</div>
      <div class="col-7 d-flex justify-content-between">
        <select style="width:47%" v-if="channels" name="channels" v-model="service.channel" :value="service.channel">
          <option value="null">{{$root.t('locationService_1')}}</option>
          <option v-if="channels.email">{{$root.t('locationService_2')}}</option>
          <option v-if="channels.tg">{{$root.t('locationService_3')}}</option>
        </select>
        <select style="width:47%" name="frequencies" v-model="service.frequency" :value="service.frequency">
          <option value="null">{{$root.t('locationService_4')}}</option>
          <option :key="frequency" v-for="frequency in frequencies" :value="frequency">{{frequency}}</option>
        </select>
      </div>
    </div>
  </form>
</template>

<i18n>
{
  "en":{
    "locationService_1":"Channel",
    "locationService_2":"Email",
    "locationService_3":"Telegram",
    "locationService_4":"Frequency"
  },
  "de":{
    "locationService_1":"Channel",
    "locationService_2":"Email",
    "locationService_3":"Telegram",
    "locationService_4":"Frequency"
  }
}
</i18n>

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
