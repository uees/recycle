import request from '@/utils/request'

// 登录
export function login(data) {
    return request({
        url: '/auth/login',
        method: 'post',
        data
    })
}

// 刷新 jwt
export function refresh() {
    return request.put('auth/refresh')
}

// 获取个人信息
export function getInfo() {
    return request({
        url: '/user',
        method: 'get'
    })
}

// 登出
export function logout() {
    return request({
        url: '/auth/logout',
        method: 'delete'
    })
}

// 修改密码
export function changePassword(data) {
    return request({
        url: '/user/password',
        method: 'put',
        data
    })
}

// 修改信息
export function update(data) {
    return request({
        url: '/user',
        method: 'patch',
        data
    })
}
