<template>
    <div>
        <form class="container" @submit.prevent="updateProfileHead()" action="POST">
            <div class="form-group">
                <input type="text" v-model="firstname" ref="firstname">
            </div>
            <div class="form-group">
            <input type="text" v-model="lastname" ref="lastname">
            </div>
            <div class="form-group">
                <select v-model="language" ref="language">
                    <option value="EN">English</option>
                    <option value="DE">Deutsch</option>
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
        async updateProfileHead(){
            let old_firstname = this.firstname
            let old_lastname = this.lastname
            let old_language = this.language
            await axios({
                method: 'post',
                url: '/profile/profileHead',
                data:{
                    firstname:this.firstname,
                    lastname:this.lastname,
                    language:this.language
                },
                headers:{
                    Authorization: sessionStorage.getItem('sessionKey'),
                }
            }).then(()=>{
                this.fetchProfileHead()
            }).catch(()=>{
                this.firstname = old_firstname
                this.lastname = old_lastname
                this.language = old_language
            })
        },
        fetchProfileHead(){
            axios({
                method: 'get',
                url: '/profile/profileHead',
                headers:{
                    Authorization: sessionStorage.getItem('sessionKey'),
                }
            }).then((response)=>{
                this.firstname = response.data.firstname,
                this.lastname = response.data.lastname,
                this.language = response.data.language
            })
        }
    },
    async mounted() {
        this.fetchProfileHead()
    },
}
</script>