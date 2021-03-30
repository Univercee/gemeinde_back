<template>
    <div>
        <div class="avatar">
            <img class="delete-img" v-on:click="deleteAvatar()" v-if="avatar != null" src="resources/assets/images/delete.png" alt="">
            <img class="avatar-img" :src="avatar_src" alt="User avatar">
            <img class="edit-img modal-open" src="resources/assets/images/edit.png" alt="">
        </div>

        <!-- Modal -->
        <div class="modal" id="modal">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body">
                        <form @submit.prevent="updateAvatar()" enctype="multipart/form-data" action="POST">
                            <div class="form-group">
                                <input type="file" ref="avatar" required>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default modal-close">Close</button>
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
            avatar:null,
            avatar_src:null,
            message:null
        }
    },
    methods: {
        async fetchAvatar(){
            await axios({
                method: 'get',
                url: '/profile/avatar',
            }).then((response)=>{
                this.avatar = response.data.image
                let delimiter = ""
                if(this.avatar != null){
                   delimiter = this.avatar.includes('?') ? '&' : '?'
                }
                this.avatar_src = response.data.image + delimiter + new Date().getTime()
            })
        },
        updateAvatar(){
           document.getElementById('modal').classList = "modal modal-close-animation"
            let formData = new FormData()
            formData.append('file',this.$refs.avatar.files[0])
            axios({
                method: 'post',
                url: '/profile/avatar',
                data:formData,
                headers:{
                    'content-type': 'multipart/form-data'
                }
            }).then(async (response)=>{
                this.message = response.data.message
                await this.fetchAvatar()
            }).catch((err)=>{
                this.message = err.response.data.error
            })
        },
        deleteAvatar(){
            axios({
                method: 'delete',
                url: '/profile/avatar'
            }).then(async (response)=>{
                this.message = response.data.message
                await this.fetchAvatar()
            }).catch((err)=>{
                this.message = err.response.data.error
            })
        },
        initModal(){
            var avatarModal = document.getElementById('modal');
            var btnClose = document.getElementsByClassName('modal-close');
            var btnShow= document.getElementsByClassName('modal-open');
            for(let el of btnShow){
                el.addEventListener('click', (e) => {
                    avatarModal.style.display = "block"
                    avatarModal.classList = "modal modal-open-animation"
                })
            }
            for(let el of btnClose){
                el.addEventListener('click', (e) => {
                    avatarModal.classList = "modal modal-close-animation"
                })
            }
            avatarModal.addEventListener('animationend', () => {
                if(avatarModal.classList.contains('modal-close-animation')){
                    avatarModal.classList = "modal"
                    avatarModal.style.display = "none"
                }
                else if(avatarModal.classList.contains('modal-open-animation')){
                    avatarModal.classList = "modal"
                }
            });
        }
    },
    async created() {
        await this.fetchAvatar()
        this.initModal()
    }
}
</script>
<style>
    .avatar{
        position: relative;
        width: 200px;
        height: 200px;
    }
    .avatar-img{
        position: absolute;
        z-index: 0;
        height: 100%;
        width: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .edit-img, .delete-img{
        position: absolute;
        cursor: pointer;
        height: 20%;
        width: 20%;
        opacity: 0.9;
        visibility: hidden;
    }
    .delete-img{
        z-index: 10;
        top: 80%;
        left: 0%;
    }
    .edit-img{
        z-index: 10;
        top: 80%;
        left: 80%;
    }
    .edit-img:hover, .delete-img:hover{
        opacity: 1;
    }
    .modal-open-animation{
        animation: modal_open ease-out 0.3s;
        animation-iteration-count: 1;
    }
    .modal-close-animation{
        animation: modal_close ease-in 0.3s;
        animation-iteration-count: 1;
    }
    .avatar:hover .edit-img{
        visibility: visible;
    }
    .avatar:hover .delete-img{
        visibility: visible;
    }
    @keyframes modal_open {
        0% {
            top:-50%;
            opacity: 0;
        }
        100% {
            opacity: 1;
            top:0;
        }
    }
    @keyframes modal_close {
        0% {
            top:0%;
            opacity: 1;
        }
        100% {
            opacity: 0;
            top:-100%;
        }
    }
</style>
