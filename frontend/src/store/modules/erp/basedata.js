import { rolesApi } from '@/api/user'
import { customersApi } from '@/api/erp'

// 用于提示建议的数据
const state = {
  allRoles: [],
  customers: []
}

const mutations = {
  SET_ROLES: (state, roles) => {
    state.allRoles = roles
  },
  SET_CUSTOMERS: (state, customers) => {
    state.customers = customers
  }
}

const actions = {
  async loadRoles({ commit }) {
    const response = await rolesApi.list({
      params: {
        all: true
      }
    })
    const { data } = response
    commit('SET_ROLES', data)
  },
  async loadCustomers({ commit }) {
    const response = await customersApi.list()
    const { data } = response
    commit('SET_CUSTOMERS', data)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
