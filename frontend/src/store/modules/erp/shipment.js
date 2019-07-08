import Shipment from '@/defines/models'

const state = {
  action: undefined,
  obj: undefined,
  dialogVisible: false
}

const mutations = {
  SET_VISIBLE: (state, visible) => {
    state.dialogVisible = visible
  },
  SET_ACTION: (state, action) => {
    state.action = action
  },
  SET_OBJ: (state, obj) => {
    state.obj = obj
  }
}

const actions = {
  async close({ commit }) {
    commit('SET_VISIBLE', false)
  },
  async open({ commit }) {
    commit('SET_VISIBLE', true)
  },
  async doAction({ commit }, action) {
    commit('SET_ACTION', action)
  },
  async resetObj({ commit }) {
    commit('SET_OBJ', Shipment())
  },
  async setObj({ commit }, obj) {
    commit('SET_OBJ', obj)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
