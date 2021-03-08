export default new Vuex.Store({
    state:{
        user:null
    },
    getters:{
        getUser(){
            return this.state.user
        },
        getSessionKey(){
            return sessionStorage.getItem('sessionKey') ?? null
        }
    },
    mutations:{
        setUser(state, user){
            state.user = user
        }
    },
    actions:{
        login(commit, sessionKey){
            sessionStorage.setItem('sessionKey', sessionKey)
            axios.defaults.headers.common['Authorization'] = sessionStorage.getItem('sessionKey')
        },
        logout(commit){
            delete axios.defaults.headers.common['Authorization']
            sessionStorage.removeItem('sessionKey')
            //axios.post('/deleteSessionKey')
        },
        async fetch(commit){
            if(sessionStorage.getItem('sessionKey')){
                let user = await axios.post('/api/keys')        //it's test url, must change
                this.commit('setUser', user.data.tgBotName)
            }
        }
    }
})
