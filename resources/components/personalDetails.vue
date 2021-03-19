<template>
    <div>
        <form @submit.prevent="updatePersonalDetails()" action="POST">
            <div class="form-group">
                <input type="text" v-model="firstname" ref="firstname">
            </div>
            <div class="form-group">
            <input type="text" v-model="lastname" ref="lastname">
            </div>
            <div class="form-group">
                <select v-model="language" ref="language">
                    <option value="en">English</option>
                    <option value="de">Deutsch</option>
                </select>
            </div>
            <input type="submit" class="btn btn-primary">  
        </form>
    </div>
</template>
<script>
export default {
    data() {
        return {
            firstname:null,
            lastname:null,
            language:null
        }
    },
    methods: {
        async updatePersonalDetails(){
            let old_firstname = this.firstname
            let old_lastname = this.lastname
            let old_language = this.language
            await axios({
                method: 'post',
                url: '/profile/personalDetails',
                data:{
                    firstname:this.firstname,
                    lastname:this.lastname,
                    language:this.language
                }
            }).then(()=>{
                this.fetchPersonalDetails()
            }).catch(()=>{
                this.firstname = old_firstname
                this.lastname = old_lastname
                this.language = old_language
            })
        },
        fetchPersonalDetails(){
            axios({
                method: 'get',
                url: '/profile/personalDetails'
            }).then((response)=>{
                this.firstname = response.data.firstname,
                this.lastname = response.data.lastname,
                this.language = response.data.language
            })
        }
    },
    async mounted() {
        this.fetchPersonalDetails()
    },
}
</script>