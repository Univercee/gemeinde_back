
export default ({
    state:{
        user_data:null
    },
    getters:{
        getUserData(){
            return this.state.user_data
        }
    },
    mutations:{
        setSessionKey(state, sessionKey){
            state.sessionKey = sessionKey
        },
        setUserData(state, user_data){
            state.user_data = user_data
        }
    },
    actions:{
        login(commit, sessionKey){
            axios.defaults.headers.common['sessionKey'] = sessionKey
            sessionStorage.setItem('key', sessionKey)
        },
        logout(commit){
            delete axios.defaults.headers.common['sessionKey']
            sessionStorage.removeItem('key')
        },
        async request(commit, url){
            response = await axios.post(url, sessionStorage.getItem('sessionKey'))
            await commit('setUserData', response.data)
            return getUserData()
        }
    }
})
