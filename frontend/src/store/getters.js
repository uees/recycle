const getters = {
    phone: state => state.user.phone,
    token: state => state.user.token,
    avatar: state => state.user.avatar,
    name: state => state.user.name
}

export default getters
