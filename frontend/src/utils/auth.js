import Cookies from 'js-cookie'

const TokenKey = require('@/settings').tokenKey

export function getToken() {
    return Cookies.get(TokenKey)
}

export function setToken(token) {
    return Cookies.set(TokenKey, token, { expires: 14 }) // 两周过期
}

export function removeToken() {
    return Cookies.remove(TokenKey)
}
