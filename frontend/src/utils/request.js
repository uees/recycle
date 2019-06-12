import axios from 'axios'
import Toast from 'muse-ui-toast';
import store from '@/store'

// create an axios instance
const service = axios.create({
    baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
    // withCredentials: true, // send cookies when cross-domain requests
    timeout: 30000 // request timeout 30s
})

// request interceptor
service.interceptors.request.use(
    config => {
        // do something before request is sent
        if (typeof config.notAuth === 'undefined' || !config.notAuth) {
            if (store.getters.accessToken) {
                config.headers['Authorization'] = 'Bearer ' + store.getters.accessToken
            }
        }
        return config
    },
    error => {
        // do something with request error
        if (process.env.NODE_ENV === 'development') {
            console.log(error) // for debug
        }
        return Promise.reject(error)
    }
)

// response interceptor
service.interceptors.response.use(
    response => response,
    error => {
        if (process.env.NODE_ENV === 'development') {
            console.log('err' + error) // for debug
        }

        let $message

        if (error.response) {
            const data = error.response.data
            $message = data.message || (data.data && data.data.message) || error.message
        } else {
            $message = error.message
        }

        Toast.error($message)
        return Promise.reject(error)
    }
)

export default service
