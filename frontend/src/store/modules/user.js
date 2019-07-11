import { login, logout, getInfo } from '@/api/user'
import { getToken, setToken, removeToken } from '@/utils/auth'
import { resetRouter } from '@/router'

const state = {
  token: getToken(),
  id: undefined,
  email: '',
  name: '',
  avatar: '',
  roles: []
}

const mutations = {
  SET_ID: (state, id) => {
    state.id = id
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_EMAIL: (state, email) => {
    state.email = email
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles
  }
}

const actions = {
  // user login
  async login({ commit }, userInfo) {
    const { email, password } = userInfo
    const response = await login({ email: email.trim(), password: password })
    const { data } = response
    commit('SET_TOKEN', data.token)
    setToken(data.token)
  },

  // get user info
  async getInfo({ commit, state }) {
    const response = await getInfo()
    const { data } = response

    if (!data) {
      throw new Error('Verification failed, please Login again.')
    }

    const { id, name, email, avatar, roles } = data

    if (roles && roles.data.length > 0) { // 验证返回的roles是否是一个非空数组
      commit('SET_ROLES', roles.data)
    } else {
      throw new Error('getInfo: roles must be a non-null array !')
    }

    commit('SET_ID', id)
    commit('SET_NAME', name)
    commit('SET_EMAIL', email)
    commit('SET_AVATAR', avatar)

    return data
  },

  // user logout
  async logout({ commit, state }) {
    await logout()
    commit('SET_TOKEN', '')
    removeToken()
    resetRouter()
  },

  // remove token
  async resetToken({ commit }) {
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

