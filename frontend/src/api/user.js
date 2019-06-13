import request from '@/utils/request'

export function login(data) {
    return request({
        url: '/auth/login',
        method: 'post',
        data
    })
}

export function getInfo(token) {
    return request({
        url: '/user',
        method: 'get'
    })
}

export function logout() {
    return request({
        url: '/auth/logout',
        method: 'delete'
    })
}

export function changePassword(data) {
    return request({
        url: '/user/password',
        method: 'put',
        data
    })
}

export function update(data) {
    return request({
        url: '/user',
        method: 'patch',
        data
    })
}
