import { login, logout, getInfo, refresh } from '@/api/user'
import { getToken, setToken, removeToken } from '@/utils/auth'
import { resetRouter } from '@/router'

const state = {
    token: getToken(),
    phone: '',
    name: '',
    avatar: ''
}

const mutations = {
    SET_TOKEN: (state, token) => {
        state.token = token
    },
    SET_NAME: (state, name) => {
        state.name = name
    },
    SET_AVATAR: (state, avatar) => {
        state.avatar = avatar
    },
    SET_PHONE: (state, phone) => {
        state.phone = phone
    }
}

const actions = {
    // user login
    async login({ commit }, userInfo) {
        const { email, password } = userInfo
        // eslint-disable-next-line
        console.log(email, password)
        const response = await login({ email: email.trim(), password: password })
        const { data } = response.data
        commit('SET_TOKEN', data.token)
        setToken(data.token)
    },

    async refreshToken({ commit }) {
        const response = await refresh()
        const { data } = response.data
        commit('SET_TOKEN', data.token)
        setToken(data.token)
    },

    // get user info
    async getInfo({ commit, state }) {
        const response = await getInfo(state.token)
        const { data } = response.data
        if (!data) {
            throw new Error('Verification failed, please Login again.')
        }
        const { name, avatar, phone } = data
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        commit('SET_PHONE', phone)
    },

    // user logout
    async logout({ commit, state }) {
        await logout(state.token)
        commit('SET_TOKEN', '')
        removeToken()
        resetRouter()
    },

    // remove token
    async removeToken({ commit }) {
        commit('SET_TOKEN', '')
        removeToken()
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
