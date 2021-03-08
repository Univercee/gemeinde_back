export default new Vuex.Store({
    state:{
        user:null
    },
    getters:{
        getUser(state){
            return state.user
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
            axios.defaults.headers.common['Authorization'] = 'Bearer '+sessionStorage.getItem('sessionKey')
            console.log(axios.defaults.headers)
        },
        logout(commit){
            delete axios.defaults.headers.common['Authorization']
            sessionStorage.removeItem('sessionKey')
            //axios.post('/deleteSessionKey')
        },
        async fetch(commit){
            if(sessionStorage.getItem('sessionKey')){
                let user = await axios.post('/api/getUser',{
                    sessionKey:sessionStorage.getItem('sessionKey')         //сейчас токен передается через параметр, т.к заголовок почему-то не проходит
                })
                this.commit('setUser', user.data[0])
            }
        }
    }
})
