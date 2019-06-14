import router from './router'
import store from './store'
import Toast from 'muse-ui-toast';
import NProgress from 'muse-ui-progress';
import 'muse-ui-progress/dist/muse-ui-progress.css';
import { getToken } from '@/utils/auth' // get token from cookie
import pageTitle from '@/utils/page-title'

const whiteList = ['/login'] // no redirect whitelist

router.beforeEach(async (to, from, next) => {
    // start progress bar
    NProgress.start()

    // set page title
    document.title = pageTitle(to.meta.title)

    // determine whether the user has logged in
    const hasToken = getToken()

    if (hasToken) {
        if (to.path === '/login') {
            // if is logged in, redirect to the home page
            next({ path: '/' })
            NProgress.done()
        } else {
            const hasGetUserInfo = store.getters.name
            if (hasGetUserInfo) {
                next()
            } else {
                try {
                    // get user info
                    await store.dispatch('user/getInfo')

                    next()
                } catch (error) {
                    // remove token and go to login page to re-login
                    await store.dispatch('user/removeToken')
                    Toast.error(error || 'Has Error')
                    next(`/login?redirect=${to.path}`)
                    NProgress.done()
                }
            }
        }
    } else {
        /* has no token*/

        if (whiteList.indexOf(to.path) !== -1) {
            // in the free login whitelist, go directly
            next()
        } else {
            // other pages that do not have permission to access are redirected to the login page.
            next(`/login?redirect=${to.path}`)
            NProgress.done()
        }
    }
})

router.afterEach(() => {
    // finish progress bar
    NProgress.done()
})
