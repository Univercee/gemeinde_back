axios.defaults.baseURL = '/api';
axios.defaults.headers.common['Content-Type'] = 'application/json'
let sessionKey = sessionStorage.getItem('sessionKey');
if(sessionKey) axios.defaults.headers.common.Authorization = 'Bearer '+sessionKey
